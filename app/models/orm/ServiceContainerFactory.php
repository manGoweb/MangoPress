<?php

namespace App\Models\Orm;

use DibiConnection;
use Nette\Caching\Cache;
use Nette\Object;
use Orm;


/**
 * Creates service container for RepositoryContainer.
 *
 * @author Jan Tvrdík
 *
 * @property-read Orm\IServiceContainer $container
 */
class ServiceContainerFactory extends Object implements Orm\IServiceContainerFactory
{

	/**
	 * @var DibiConnection
	 */
	private $dibiConnection;

	/**
	 * @var Cache
	 */
	private $cache;

	/**
	 * @param DibiConnection $dibiConnection
	 * @param NULL|Cache $cache for Orm\PerformanceHelper or null to disable the cache
	 */
	public function __construct(DibiConnection $dibiConnection, Cache $cache = NULL)
	{
		$this->dibiConnection = $dibiConnection;
		$this->cache = $cache;
	}

	/**
	 * @return Orm\IServiceContainer
	 */
	public function getContainer()
	{
		$container = new Orm\ServiceContainer();
		$container->addService('annotationsParser', 'Orm\AnnotationsParser');
		$container->addService('annotationClassParser', [$this, 'createAnnotationClassParser']);
		$container->addService('mapperFactory', [$this, 'createMapperFactory']);
		$container->addService('repositoryHelper', 'Orm\RepositoryHelper');
		$container->addService('dibi', $this->dibiConnection);

		if ($this->cache !== NULL)
		{
			$container->addService('performanceHelperCache', $this->cache);
		}

		return $container;
	}

	/**
	 * @internal
	 * @param Orm\IServiceContainer $container
	 * @return Orm\IMapperFactory
	 */
	public function createMapperFactory(Orm\IServiceContainer $container)
	{
		/** @var Orm\AnnotationClassParser $service */
		$service = $container->getService('annotationClassParser', 'Orm\AnnotationClassParser');
		return new Orm\MapperFactory($service);
	}

	/**
	 * @internal
	 * @param Orm\IServiceContainer $container
	 * @return Orm\AnnotationClassParser
	 */
	public function createAnnotationClassParser(Orm\IServiceContainer $container)
	{
		/** @var Orm\AnnotationsParser $service */
		$service = $container->getService('annotationsParser', 'Orm\AnnotationsParser');
		return new Orm\AnnotationClassParser($service);
	}

}
