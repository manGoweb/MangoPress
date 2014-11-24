<?php

$configurator = new Nette\Configurator;

$configurator->enableDebugger(LOG_DIR);
$configurator->setTempDirectory(TEMP_DIR);

$configurator->createRobotLoader()
	->addDirectory(APP_DIR)
	->register();

$configurator->addConfig(CONFIG_DIR . '/config.neon');
$configurator->addConfig(CONFIG_DIR . '/config.local.neon');

$container = $configurator->createContainer();

return $container;
