<?php

// define constants
define('BASE_DIR', realpath(dirname(__FILE__) . '/..'));
define('WWW_DIR', dirname(__FILE__));

define('APP_DIR', BASE_DIR . '/app');
define('LIBS_DIR', BASE_DIR . '/vendor');
define('BIN_DIR', BASE_DIR . '/bin');
define('LOG_DIR', BASE_DIR . '/log');
define('TEMP_DIR', BASE_DIR . '/temp');
define('TESTS_DIR', BASE_DIR . '/tests');
define('MIGRATIONS_DIR', BASE_DIR . '/migrations');
define('CONFIG_DIR', BASE_DIR . '/config');

define('WP_DIR', WWW_DIR . '/wp-core');

// Wait, wait. Are we deploying?!
if(file_exists(BASE_DIR . '/maintenance.php')) {
	require BASE_DIR . '/maintenance.php';
}

// require Composer autoloader
require LIBS_DIR . '/autoload.php';

// start debugging
Tracy\Debugger::enable(Tracy\Debugger::DETECT, LOG_DIR);

$App = require APP_DIR . '/bootstrap.php';

if(!$App) {
	exit;
}
