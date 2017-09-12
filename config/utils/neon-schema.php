<?php

require_once __DIR__.'/lib/neon-schema.php';

$initTheme[] = function ($dir) {
	Mangoweb\runNeonConfigs($dir.'/schema');
};
