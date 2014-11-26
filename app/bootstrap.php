<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/exceptions.php';
require __DIR__ . '/config/Configurator.php';

$configurator = new App\Config\Configurator;

$container = $configurator->createContainer();

$useNette = FALSE; // use your own condition

if($useNette) {
	$container->application->run();
	return false;
}

// Pass the Container to the WordPress part
return $container;
