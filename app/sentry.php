<?php

use Nette\Neon\Decoder;


$configFile = dirname(__DIR__) . '/config/config.local.neon';
if (!file_exists($configFile)) {
	echo "App configuration not found at '$configFile'";
	die;
}
$decoder = new Decoder();
$appConfig = $decoder->decode(file_get_contents($configFile));


/** @var array $config */
$config = ($appConfig['parameters']['sentry'] ?? []) + [
	'dsn' => NULL,
	'curl_method' => 'async',
	'release' => NULL,
];


/** @var NULL|Raven_Client $client */
$client = NULL;

if ($config['dsn'] && Mangoweb\isSharedHost()) {
	$client = new Raven_Client($config);
	$client->tags_context(['stage' => Mangoweb\isBetaHost() ? 'beta' : 'prod']);

	// Install error handlers and shutdown function to catch fatal errors
	$handler = new Raven_ErrorHandler($client);
	$handler->registerExceptionHandler();
	$handler->registerErrorHandler();
	$handler->registerShutdownFunction();

}

return $client;
