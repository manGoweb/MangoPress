<?php

$initTheme[] = function ($dir) {
	foreach (glob($dir . '/utils/*.php') as $path) {
		require_once $path;
	}

	if (is_user_logged_in()) {
		foreach (glob($dir . '/admin/utils/*.php') as $path) {
			require_once $path;
		}
	}
};
