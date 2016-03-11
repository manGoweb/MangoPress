<?php

require_once __DIR__ . '/init.php';

define('WP_HOME', rtrim($Url->baseUrl, '/'));
define('WP_SITEURL', WP_HOME. '/wp-core');

define('COOKIEPATH', $Url->basePath);
define('SITECOOKIEPATH', $Url->basePath);
define('ADMIN_COOKIE_PATH', $Url->basePath);

define('WP_CONTENT_DIR', WWW_DIR . '/wp-content');
define('WP_CONTENT_URL', WP_HOME . '/wp-content');
//define('WPMU_PLUGIN_DIR', WP_CONTENT_DIR . '/mu-plugins');
//define('WPMU_PLUGIN_URL', WP_CONTENT_URL . '/mu-plugins');
define('WP_DEFAULT_THEME', 'theme');

define('FS_METHOD', 'direct'); // never prompt for FTP credentials to install plugins

// define('WP_DEBUG_DISPLAY', FALSE); // Do not display error messages

define('DISALLOW_FILE_EDIT', TRUE); // Disable the Plugin and Theme Editor
// define('DISALLOW_FILE_MODS', true); // Disable Plugin and Theme Update and Installation
// define('AUTOMATIC_UPDATER_DISABLED', true); // Disable all automatic updates
// define('WP_AUTO_UPDATE_CORE', 'minor'); // Enable core updates for minor releases (default); true - Enable all core updates, including minor and major; false - Disable all core updates
// define('FORCE_SSL_LOGIN', true); // Require SSL for Admin and Logins

$params = $App->parameters;
$db = $params['database'];

define('DB_NAME', $db['database']);
define('DB_USER', $db['username']);
define('DB_PASSWORD', $db['password']);
define('DB_HOST', $db['host']);
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');

$wpParams = $params['wp'];
define('AUTH_KEY', $wpParams['AUTH_KEY']);
define('SECURE_AUTH_KEY', $wpParams['SECURE_AUTH_KEY']);
define('LOGGED_IN_KEY', $wpParams['LOGGED_IN_KEY']);
define('NONCE_KEY', $wpParams['NONCE_KEY']);
define('AUTH_SALT', $wpParams['AUTH_SALT']);
define('SECURE_AUTH_SALT', $wpParams['SECURE_AUTH_SALT']);
define('LOGGED_IN_SALT', $wpParams['LOGGED_IN_SALT']);
define('NONCE_SALT', $wpParams['NONCE_SALT']);

$table_prefix  = 'wp_';

if (strncmp(gethostname(), 'shared-', 7) === 0) {
	// bedrock-autoloader symlink fix
	define('PROJECT_ROOT', dirname(__DIR__, 3));
	define('WP_PLUGIN_DIR', PROJECT_ROOT . '/public/wp-content/plugins');
	define('WPMU_PLUGIN_DIR', WWW_DIR . '/wp-content/mu-plugins');
}

define('WP_DEBUG', !Tracy\Debugger::$productionMode);

require_once WP_DIR . '/wp-settings.php';
