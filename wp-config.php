<?php

// Hello, hard worker!

define('BASE_DIR', dirname(__FILE__));
define('WWW_DIR', dirname(__FILE__));

define('APP_DIR', BASE_DIR . '/app');
define('LOG_DIR', BASE_DIR . '/log');
define('TEMP_DIR', BASE_DIR . '/temp');

// Wait, wait. Are we deploying?!

if(file_exists(BASE_DIR . '/maintenance.php')) {
	require BASE_DIR . '/maintenance.php';
}

// require Composer autoloader
require BASE_DIR . '/vendor/autoload.php';

// start debugging
Tracy\Debugger::enable(Tracy\Debugger::DETECT, LOG_DIR);

function getBaseUrl()
{
	$basePath = '/'.ltrim(str_replace(realpath($_SERVER['DOCUMENT_ROOT']), '', realpath(dirname(__FILE__))), '/');

	$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http';
	$host = $_SERVER['HTTP_HOST'];
	$path = str_replace('\\', '/', $basePath);

	$baseUrl = $protocol . '://' . $host . $path;
	return $baseUrl;
}

define('WP_DIR', BASE_DIR . '/wordpress');
define('WP_HOME', getBaseUrl());
define('WP_SITEURL', getBaseUrl());

define('WP_CONTENT_DIR', BASE_DIR . '/wp-content');
define('WP_CONTENT_URL', WP_SITEURL . '/wp-content');

require dirname(__FILE__) . '/wp-config-local.php';

if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/wordpress/');

require_once(ABSPATH . 'wp-settings.php');
