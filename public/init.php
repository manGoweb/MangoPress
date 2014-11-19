<?php

// define constants
define('BASE_DIR', realpath(__DIR__ . '/..'));
define('WWW_DIR', dirname(__FILE__));

define('APP_DIR', BASE_DIR . '/app');
define('LIBS_DIR', BASE_DIR . '/vendor');
define('LOG_DIR', BASE_DIR . '/log');
define('TEMP_DIR', BASE_DIR . '/temp');

define('WP_DIR', WWW_DIR . '/wp-core');
define('ABSPATH', WP_DIR . '/');

// Wait, wait. Are we deploying?!
if(file_exists(BASE_DIR . '/maintenance.php')) {
	require BASE_DIR . '/maintenance.php';
}

// require Composer autoloader
require_once LIBS_DIR . '/autoload.php';

// start debugging
Tracy\Debugger::enable(Tracy\Debugger::DETECT, LOG_DIR);
