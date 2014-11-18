<?php

require_once dirname(__FILE__) . '/init.php';

function getBaseUrl()
{
	$basePath = '/'.ltrim(str_replace(realpath($_SERVER['DOCUMENT_ROOT']), '', realpath(dirname(__FILE__))), '/');

	$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http';
	$host = $_SERVER['HTTP_HOST'];
	$path = str_replace('\\', '/', $basePath);

	$baseUrl = $protocol . '://' . $host . $path;
	return $baseUrl;
}

define('WP_HOME', getBaseUrl());
define('WP_SITEURL', getBaseUrl());

define('WP_CONTENT_DIR', BASE_DIR . '/wp-content');
define('WP_CONTENT_URL', WP_SITEURL . '/wp-content');
define('WP_DEFAULT_THEME', 'mango');

require BASE_DIR . '/wp-config-local.php';

require_once WP_DIR . '/wp-settings.php';
