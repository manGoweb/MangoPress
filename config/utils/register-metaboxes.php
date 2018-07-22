<?php

$initTheme[] = function ($dir) {
	$metaboxesDir = $dir . '/admin/metaboxes';

	foreach (glob($metaboxesDir . '/*.php') as $path) {
		require_once $path;
	}
};
