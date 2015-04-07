<?php

require __DIR__ . '/exceptions.php';
require __DIR__ . '/config/Configurator.php';

$configurator = new App\Config\Configurator;

$container = $configurator->createContainer();

$useNetteApplication = FALSE; // use your own condition

if($useNetteApplication) {
	$container->application->run();
	return false;
}

require __DIR__ . '/misc.php';

// Pass the Container to the WordPress part
return $container;
