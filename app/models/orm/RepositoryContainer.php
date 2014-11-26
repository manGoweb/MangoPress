<?php

namespace App\Models\Orm;

use App;
use Nette\Reflection;
use Orm;
use Orm\IRepository;
use Orm\RepositoryAlreadyRegisteredException;
use Orm\RepositoryNotFoundException;


/**
 * // @property-read App\Models\Rme\ExampleRepository $example
 */
class RepositoryContainer extends Orm\RepositoryContainer
{

	/**
	 * @var array
	 */
	private $aliases = [];

	protected $repositoryClasses = [];

	/**
	 * Automatically registers repository aliases
	 *
	 * @param NULL|Orm\IServiceContainerFactory|Orm\IServiceContainer
	 * @param array ($alias => $className)
	 */
	public function __construct($containerFactory = NULL, $repositories = [])
	{
		parent::__construct($containerFactory);

		$this->registerAnnotations();

		// registers repositories from config
		foreach ($repositories as $alias => $repositoryClass)
		{
			if (!$this->isRepository($alias))
			{
				$this->register($alias, $repositoryClass);
				$this->aliases[$alias] = $repositoryClass;
			}
		}
	}

	/**
	 * @param string
	 * @param string
	 * @return self
	 * @throws RepositoryAlreadyRegisteredException
	 */
	public function register($alias, $repositoryClass)
	{
		$res = parent::register($alias, $repositoryClass);
		$this->repositoryClasses[] = $repositoryClass;
		return $res;
	}

	/**
	 * @return string[] of class names
	 */
	public function getRepositoryClasses()
	{
		return $this->repositoryClasses;
	}

	/**
	 * Registers repositories from annotations
	 */
	private function registerAnnotations()
	{
		$annotations = Reflection\ClassType::from($this)->getAnnotations();
		if (isset($annotations['property-read']))
		{
			$c = get_called_class();
			$namespace = substr($c, 0, strrpos($c, '\\'));
			foreach ($annotations['property-read'] as $value)
			{
				if (preg_match('#^([\\\\\\w]+Repository)\\s+\\$(\\w+)$#', $value, $m))
				{
					$class = strpos($m[1], '\\') === FALSE ? $namespace . '\\' . $m[1] : $m[1];
					$this->register($m[2], $class);
					$this->aliases[$m[2]] = $class;
				}
			}
		}
	}

	/**
	 * Vrací instanci repository.
	 * Přednostně instancuje třídy vyjmenované v aliasech, přičemž bere v potaz dědičnost.
	 *
	 * @param string - repositoryClassName|alias
	 * @return Repository|IRepository
	 * @throws RepositoryNotFoundException
	 */
	public function getRepository($name)
	{
		$name = (string) $name;
		if (isset($this->aliases[$name]))
		{
			$repositoryClass = $this->aliases[$name];
			return parent::getRepository($repositoryClass);
		}
		else
		{
			$repositoryClass = NULL;
			foreach ($this->aliases as $alias => $class)
			{
				if (is_subclass_of($class, $name))
				{
					$repositoryClass = $class;
					break;
				}
			}
			$repository = parent::getRepository($repositoryClass ?: $name);
			$this->aliases[$name] = get_class($repository);
		}

		return $repository;
	}

	/**
	 * Black magic. Work-around pro nefunkční RepositoryContainer::clean()
	 * Vymaže změny ve všech repozitářích (zapomene nové, změněné a načtené entity)
	 */
	public function purge()
	{
		$ref = new Reflection\ClassType('Orm\\RepositoryContainer');
		$ref = $ref->getProperty('repositories');
		$ref->setAccessible(TRUE);

		$repositories = $ref->getValue($this);

		foreach ($repositories as $repository)
		{
			$this->purgeRepository($repository);
		}
	}

	/**
	 * Black magic. Work-around pro nefunkční Repository::clean()
	 * Vymaže změny v repozitáři (zapomene nové, změněné a načtené entity)
	 *
	 * @param Orm\Repository $repository
	 */
	public function purgeRepository(Orm\Repository $repository)
	{
		$ref = new Reflection\ClassType('Orm\\IdentityMap');
		$ref = $ref->getProperty('entities');
		$ref->setAccessible(TRUE);

		$map = $repository->getIdentityMap();
		$ref->setValue($map, []);

		foreach ($map->getAllNew() as $entity)
		{
			$map->detach($entity);
		}
	}

}
