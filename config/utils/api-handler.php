<?php

require_once __DIR__ . '/baseUrl.php';
require_once __DIR__ . '/register-image-sizes.php';

$initTheme[] = function ($dir) {
	add_action('template_redirect', function () use ($dir) {
		if (file_exists($dir . "/api/index.php")) {
			global $Req;
			global $ApiQuery;
			global $Payload;

			if (!isset($Req)) {
				return;
			}

			status_header(200);

			if (!isset($Payload)) {
				$Payload = new Nette\Utils\ArrayHash;
			}

			$path = $Req->getUrl()->getRelativeUrl();
			$parts = explode('/', trim($path, '/'));
			if ($parts[0] === 'api') {
				array_shift($parts);
				$ApiQuery = $parts;
				require $dir . "/api/index.php";
				sendPayload();
			}
		}
	});
};
