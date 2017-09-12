<?php

$configFile = __DIR__.'/../config.local.neon';

if (!file_exists($configFile)) {
	echo "App configuration not found at '$configFile'";
	die;
}

$appConfig = Nette\Neon\Neon::decode(file_get_contents($configFile));

$config = ($appConfig['parameters']['sentry'] ?? []) + [
	'dsn' => null,
	'curl_method' => 'async',
	'release' => null,
];

if ($config['dsn']) {
	$client = new Raven_Client($config);
	$client->tags_context(['stage' => !empty($config['stage']) ? $config['stage'] : (Mangoweb\isBetaHost() ? 'beta' : 'prod')]);

	// Install error handlers and shutdown function to catch fatal errors
	$handler = new Raven_ErrorHandler($client);
	$handler->registerExceptionHandler();
	$handler->registerErrorHandler();
	$handler->registerShutdownFunction();

	$initTheme[] = function ($dir) use ($client) {
		try {
			$user = wp_get_current_user();
			$client->set_user_data($user->ID, $user->user_email, [
			'Name' => $user->display_name,
			'Registered' => $user->user_registered,
		]);
		} catch (\Nette\DI\MissingServiceException $e) {
			// sentry not initialized, ignore
		}
	};
}
