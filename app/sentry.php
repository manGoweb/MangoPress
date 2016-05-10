<?php

$configFile = dirname(__DIR__) . '/config/sentry.json';
if (!file_exists($configFile)) {
	echo "Sentry configuration not found at '$configFile'";
	die;
}

/** @var array $config */
$config = json_decode(file_get_contents($configFile), TRUE) + [
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
