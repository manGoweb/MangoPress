<?php

require_once __DIR__.'/../vendor/autoload.php';

define('PROJECT_ROOT_DIR', realpath(__DIR__.'/..'));
define('LOG_DIR', __DIR__.'/../log');
define('TEMP_DIR', __DIR__.'/../temp');

$initTheme = [];

$configurator = new Nette\Configurator();

if (getenv('NETTE_DEBUG')) {
	$configurator->setDebugMode(getenv('NETTE_DEBUG') === 'TRUE');
}

if (!\Tracy\Debugger::$productionMode) {
	$configurator->enableTracy(LOG_DIR);
}

$configurator->setTempDirectory(TEMP_DIR);
$configurator->addConfig(__DIR__.'/config.neon');
$configurator->addConfig(__DIR__.'/config.local.neon');

if (getenv('TRACY_EDITOR_URL')) {
	Tracy\Debugger::$editor = getenv('TRACY_EDITOR_URL');
}

$container = $configurator->createContainer();

$container->getService('session')->start();

if ($container->parameters['loggerOutput'] ?? false === 'stderr') {
	class StdErrLogger extends Tracy\Logger
	{
		public function log($message, $priority = Tracy\Logger::INFO)
		{
			$line = $this->formatLogLine($message, null);
			file_put_contents($_ENV['LOG_STREAM'] ?? 'php://stderr', "$line\n", LOCK_EX | FILE_APPEND);
		}
	}

	Tracy\Debugger::setLogger(new StdErrLogger(null));
}

foreach (glob(__DIR__.'/lib/*.php') as $filepath) {
	require_once $filepath;
}

foreach (glob(__DIR__.'/utils/*.php') as $filepath) {
	require_once $filepath;
}

define('SHOW_EXAMPLES', !empty($container->parameters['showExamples']));

return $container;
