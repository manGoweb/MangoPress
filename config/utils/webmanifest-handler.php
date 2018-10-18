<?php

require_once __DIR__ . '/baseUrl.php';

$initTheme[] = function ($dir) {
	add_action('template_redirect', function () use ($dir) {
		if (file_exists($dir . '/webmanifest.php')) {
			global $Req;

			if (!isset($Req)) {
				return;
			}

			$path = $Req->getUrl()->getRelativeUrl();
			$parts = explode('/', trim($path, '/'));
			if ($parts[0] === 'manifest.webmanifest' && count($parts) === 1) {
				$webmanifest = [];

				Tracy\Debugger::$productionMode = TRUE;
				header('Content-Type:application/json;charset=utf-8');

				require $dir . '/webmanifest.php';

				print(json_encode($webmanifest)) and die();
			}
		}
	});
};
