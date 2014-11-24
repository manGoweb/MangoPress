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

$useNette = FALSE; // use your own condition

if($useNette) {
	$container->application->run();
	return false;
}

// Pass the Container to the WordPress part
return $container;
