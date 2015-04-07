<?php

$View = [];

setlocale(LC_ALL,  get_locale().'.utf-8');

define('THEME_DIR', dirname(__FILE__));
define('THEME_UTILS_DIR', THEME_DIR . '/utils');
define('THEME_VIEWS_DIR', THEME_DIR . '/views');
define('NEON_WP_DIR', __DIR__ . '/define');

foreach(glob(THEME_UTILS_DIR . '/*.php') as $filename) {
	require_once $filename;
}
