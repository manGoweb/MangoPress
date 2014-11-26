<?php

namespace App\Config;

use Bin\Support\VariadicArgvInput;
use Nette;
use Nette\DI;
use Nette\DI\Container;
use Nette\FileNotFoundException;
use Nette\Loaders\RobotLoader;
use RuntimeException;
use Tracy\Debugger;


/**
 * @method void onInit
 * @method void onAfter
 */
class Configurator extends Nette\Configurator
{

	/**
	 * @var array of function(Configurator $sender); Occurs before first Container is created
	 */
	public $onInit = [];

	/**
	 * @var array of function(Configurator $sender); Occurs after first Container is created
	 */
	public $onAfter = [];


	/**
	 * @param NULL|array $params
	 */
	public function __construct(array $params = NULL)
	{
		parent::__construct();

		$this->setTempDirectory(TEMP_DIR);

		$defaults = array_map('realpath', [
			'appDir' => APP_DIR,
			'binDir' => BIN_DIR,
			'libsDir' => LIBS_DIR,
			'wwwDir' => WWW_DIR,
			'logDir' => LOG_DIR,
			'configDir' => CONFIG_DIR,
			'testsDir' => TESTS_DIR,
			'migrationsDir' => MIGRATIONS_DIR,
		]);
		$defaults += [
			'testMode' => FALSE,
		];

		$this->addParameters((array) $params + $defaults);

		foreach (get_class_methods($this) as $name)
		{
			if ($pos = strpos($name, 'onInit') === 0 && $name !== 'onInitPackages')
			{
				$this->onInit[lcfirst(substr($name, $pos + 5))] = [$this, $name];
			}
		}

		foreach (get_class_methods($this) as $name)
		{
			if ($pos = strpos($name, 'onAfter') === 0)
			{
				$this->onAfter[lcfirst(substr($name, $pos + 5))] = [$this, $name];
			}
		}

		$this->createRobotLoader()->register();
	}

	public function onInitConfigs()
	{
		$params = $this->getParameters();

		$this->addConfig($params['configDir'] . '/system.neon', FALSE);
		if ($this->isConsoleMode())
		{
			$this->addConfig($params['configDir'] . '/console.neon', FALSE);
		}
		$this->addConfig($params['configDir'] . '/config.neon', FALSE);
		$this->addConfig($params['configDir'] . '/config.local.neon', FALSE);
	}

	public function onAfterDebug(Container $c)
	{
		$p = $c->parameters;
		if (isset($p['forceDebug']))
		{
			$mode = $p['forceDebug'] === FALSE
				? Debugger::PRODUCTION
				: Debugger::DEVELOPMENT;
			Debugger::enable($mode, LOGS_DIR, 'bugs+mangopress@mangoweb.cz');
		}
	}

	public function onAfterConsole(Container $c)
	{
		if ($this->parameters['consoleMode'])
		{
			$c->getService('console.router')->setInput(new VariadicArgvInput());
		}
	}

	/**
	 * @return RobotLoader
	 */
	public function createRobotLoader()
	{
		$params = $this->getParameters();
		$loader = parent::createRobotLoader();
		$loader->addDirectory($params['appDir']);

		if ($this->isConsoleMode())
		{
			$loader->addDirectory($params['binDir']);
			$loader->addDirectory($params['migrationsDir']);
		}

		if ($this->isTestMode())
		{
			$loader->addDirectory($params['testsDir']);
		}

		return $loader;
	}

	/**
	 * @return array
	 */
	public function getParameters()
	{
		return $this->parameters;
	}

	/**
	 * @throws MissingLocalConfigException
	 * @throws \Exception
	 * @throws \Nette\FileNotFoundException
	 * @return Container
	 */
	public function createContainer()
	{
		$this->onInit($this);
		$this->onInit = [];

		try {
			$container = parent::createContainer();
			$this->onAfter($container);

			return $container;
		}
		catch (FileNotFoundException $e)
		{
			if (strpos($e->getMessage(), 'local') !== FALSE)
			{
				throw new MissingLocalConfigException($e);
			}
			else
			{
				throw $e;
			}
		}
	}

	protected function isConsoleMode()
	{
		return $this->parameters['consoleMode'];
	}

	protected function isTestMode()
	{
		return $this->parameters['testMode'];
	}

}


class MissingLocalConfigException extends RuntimeException
{

	/**
	 * @param \Nette\FileNotFoundException $e
	 */
	public function __construct(FileNotFoundException $e)
	{
		parent::__construct('Copy "app/config/config.local.example.neon" to "app/config/config.local.neon" and update credentials.', NULL, $e);
	}

}
