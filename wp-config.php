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
//define('WPMU_PLUGIN_DIR', BASE_DIR . '/wp-content/mu-plugins');
//define('WPMU_PLUGIN_URL', WP_SITEURL . '/wp-content/mu-plugins');
define('WP_DEFAULT_THEME', 'mango');

// define('WP_DEBUG_DISPLAY', FALSE); // Do not display error messages

// define('DISALLOW_FILE_EDIT', TRUE); // Disable the Plugin and Theme Editor
// define('DISALLOW_FILE_MODS', true); // Disable Plugin and Theme Update and Installation
// define('AUTOMATIC_UPDATER_DISABLED', true); // Disable all automatic updates
// define('WP_AUTO_UPDATE_CORE', 'minor'); // Enable core updates for minor releases (default); true - Enable all core updates, including minor and major; false - Disable all core updates
// define('FORCE_SSL_LOGIN', true); // Require SSL for Admin and Logins


require BASE_DIR . '/wp-config-local.php';

require_once WP_DIR . '/wp-settings.php';
