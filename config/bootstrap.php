<?php

require_once __DIR__.'/../vendor/autoload.php';

define('PROJECT_ROOT_DIR', realpath(__DIR__.'/..'));
define('LOG_DIR', __DIR__.'/../log');
define('TEMP_DIR', __DIR__.'/../temp');

$initTheme = [];

$configurator = new Nette\Configurator();
$configurator->enableTracy(LOG_DIR);
$configurator->setTempDirectory(TEMP_DIR);
$configurator->addConfig(__DIR__.'/config.neon');
$configurator->addConfig(__DIR__.'/config.local.neon');

$container = $configurator->createContainer();

$container->getService('session')->start();

foreach (glob(__DIR__.'/lib/*.php') as $filepath) {
	require $filepath;
}

foreach (glob(__DIR__.'/utils/*.php') as $filepath) {
	require $filepath;
}

return $container;
