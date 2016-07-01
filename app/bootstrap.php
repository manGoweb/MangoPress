<?php

require __DIR__ . '/hosting.php';
$sentryClient = require __DIR__ . '/sentry.php';
require __DIR__ . '/exceptions.php';
require __DIR__ . '/Configurator.php';
require __DIR__ . '/Container.php';

$configurator = new App\Config\Configurator;
if ($sentryClient) {
	$configurator->addServices(['sentry.client' => $sentryClient]);
}
$container = $configurator->createContainer();

require __DIR__ . '/shortcuts.php';
require __DIR__ . '/wp-utils.php';

// Pass the Container to the WordPress part
return new MangoPress\Container($container);
