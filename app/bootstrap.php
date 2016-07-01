<?php

require __DIR__ . '/hosting.php';
$sentryClient = require __DIR__ . '/sentry.php';
require __DIR__ . '/exceptions.php';
require __DIR__ . '/config/Configurator.php';
require __DIR__ . '/Configurator.php';

$configurator = new App\Config\Configurator;
if ($sentryClient) {
	$configurator->addServices(['sentry.client' => $sentryClient]);
}
$container = $configurator->createContainer();

$useNetteApplication = FALSE; // use your own condition

if($useNetteApplication) {
	$container->application->run();
	return false;
}

require __DIR__ . '/shortcuts.php';
require __DIR__ . '/wp-utils.php';

// Pass the Container to the WordPress part
return $container;
