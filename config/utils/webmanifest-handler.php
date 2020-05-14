<?php

require_once __DIR__ . '/baseUrl.php';

$initTheme[] = function ($dir) {
	add_action('template_redirect', function () use ($dir) {
		global $Req;

		if (!isset($Req)) {
			return;
		}

		if ($Req->getUrl()->getPath() === '/webmanifest.json/' && file_exists($dir . '/webmanifest.php')) {
			$webmanifest = require($dir . '/webmanifest.php');
			sendPayload($webmanifest);
		}
	});
};
