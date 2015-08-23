<?php

use Nette\Utils\Strings;

$View = [];

setlocale(LC_ALL,  get_locale().'.utf-8');

define('THEME_DIR', dirname(__FILE__));
define('THEME_UTILS_DIR', THEME_DIR . '/utils');
define('ADMIN_UTILS_DIR', THEME_DIR . '/admin');
define('API_DIR', THEME_DIR . '/api');
define('FORMS_DIR', THEME_DIR . '/forms');
define('THEME_VIEWS_DIR', THEME_DIR . '/views');
define('NEON_WP_DIR', __DIR__ . '/define');

require_once __DIR__ . '/latte/init.php';

foreach(glob(THEME_UTILS_DIR . '/*.php') as $filename) {
	require_once $filename;
}

foreach(glob(FORMS_DIR . '/*.php') as $filename) {
	$Forms[basename($filename, '.php')] = require_once $filename;
}
$View['Forms'] = $Forms;

if(is_admin()) foreach(glob(ADMIN_UTILS_DIR . '/*.php') as $filename) {
	require_once $filename;
}

// CSRF protection
$App->session->start();

if(Strings::startsWith($Url->pathInfo, 'api/')) {
	$ApiRequest = Strings::split(Strings::trim($Url->pathInfo, '~/+~'), '~/~');
	array_shift($ApiRequest);
	require API_DIR . '/index.php';
}
