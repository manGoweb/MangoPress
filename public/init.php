<?php

// define constants
define('BASE_DIR', realpath(__DIR__ . '/..'));
define('WWW_DIR', __DIR__);

define('APP_DIR', BASE_DIR . '/app');
define('LIBS_DIR', BASE_DIR . '/vendor');
define('BIN_DIR', BASE_DIR . '/bin');
define('LOG_DIR', BASE_DIR . '/log');
define('TEMP_DIR', BASE_DIR . '/temp');
define('TESTS_DIR', BASE_DIR . '/tests');
define('MIGRATIONS_DIR', BASE_DIR . '/migrations');
define('CONFIG_DIR', BASE_DIR . '/config');

define('WP_DIR', WWW_DIR . '/wp-core');

// require Composer autoloader
if(file_exists(LIBS_DIR . '/autoload.php')) {
	require LIBS_DIR . '/autoload.php';
} else {
	header('Content-Type: text/plain;charset=utf-8');
	die("Please install Composer dependencies.\n\nhttp://getcomposer.org");
}

// start debugging
Tracy\Debugger::enable(Tracy\Debugger::DETECT, LOG_DIR);

$App = require APP_DIR . '/bootstrap.php';

if(!$App) {
	header('Content-Type: text/plain;charset=utf-8');
	die("App was not created in bootstrap.");
}
