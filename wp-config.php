<?php

define('MANGOPRESS_VERSION', '2.0.0');
define('APP_ROOT', __DIR__);

$App = require __DIR__.'/config/bootstrap.php';

$databaseParams = $App->parameters['database'];
$wpParams = $App->parameters['wp'] ?? [];
$s3Params = $App->parameters['s3'] ?? [];

define('DB_NAME', $databaseParams['database']);
define('DB_USER', $databaseParams['username']);
define('DB_PASSWORD', $databaseParams['password']);
define('DB_HOST', $databaseParams['host']);
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');

if (!$App->parameters['consoleMode']) {
	define('WP_HOME', rtrim($wpParams['WP_HOME'] ?? $Url->getBaseUrl(), '/'));
	define('WP_SITEURL', rtrim($Url->getBaseUrl(), '/'));
}

define('WP_DEFAULT_THEME', 'theme');

define('AUTH_KEY', $wpParams['AUTH_KEY'] ?? 'put your unique phrase here');
define('SECURE_AUTH_KEY', $wpParams['SECURE_AUTH_KEY'] ?? 'put your unique phrase here');
define('LOGGED_IN_KEY', $wpParams['LOGGED_IN_KEY'] ?? 'put your unique phrase here');
define('NONCE_KEY', $wpParams['NONCE_KEY'] ?? 'put your unique phrase here');
define('AUTH_SALT', $wpParams['AUTH_SALT'] ?? 'put your unique phrase here');
define('SECURE_AUTH_SALT', $wpParams['SECURE_AUTH_SALT'] ?? 'put your unique phrase here');
define('LOGGED_IN_SALT', $wpParams['LOGGED_IN_SALT'] ?? 'put your unique phrase here');
define('NONCE_SALT', $wpParams['NONCE_SALT'] ?? 'put your unique phrase here');
define('WP_CRON_LOCK_TIMEOUT', $wpParams['CRON_LOCK_TIMEOUT'] ?? 10 * 60);

$table_prefix = 'wp_';

$imgproxy = $App->parameters['imgproxy'];
define('IMGPROXY_KEY', $imgproxy['key']);
define('IMGPROXY_SALT', $imgproxy['salt']);
define('IMGPROXY_BASE_URL', $imgproxy['baseUrl'] ?? 'https://snappycdn.net');

function get_img_proxy_base_url() {
	global $App;
	$imgproxy = $App->parameters['imgproxy'];

	if (empty($imgproxy['projectId'])) {
		throw new Exception('Missing imgproxy.projectId');
	}
	return IMGPROXY_BASE_URL . '/' . rawurlencode($imgproxy['projectId']);
}

if ($s3Params && $s3Params['enabled']) {
	if (empty($s3Params['secret'])) {
		die('S3 is enabled, but secret is missing');
	}

	define('S3_UPLOADS_BUCKET', trim($s3Params['bucket'] . '/' . ($s3Params['basePath'] ?? ''), '/'));
	define('S3_UPLOADS_KEY', $s3Params['key'] ?? null);
	define('S3_UPLOADS_SECRET', $s3Params['secret'] ?? null);
	define('S3_UPLOADS_REGION', $s3Params['region'] ?? null);
	if (!empty($s3params['bucketPublicUrl'])) {
		define('S3_UPLOADS_BUCKET_URL', $s3Params['bucketPublicUrl'] ?? null);
	} else {
		define('S3_UPLOADS_BUCKET_URL', 'https://s3.'. S3_UPLOADS_REGION . '.amazonaws.com/' . S3_UPLOADS_BUCKET);
	}
} else {
	define('S3_UPLOADS_USE_LOCAL', true);
	define('S3_UPLOADS_DISABLE_REPLACE_UPLOAD_URL', true);
}

define('WP_DEBUG', true);
define('SCRIPT_DEBUG', true);

if (!defined('ABSPATH')) {
	define('ABSPATH', dirname(__FILE__).'/public/');
}

require_once ABSPATH.'wp-settings.php';
