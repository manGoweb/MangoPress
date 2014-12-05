<?php

// run, WordPress, run!
if(file_exists(dirname(__FILE__) . '/wp-core/index.php') && file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
	require dirname(__FILE__) . '/wp-core/index.php';
} else {
	header('Content-Type: text/plain;charset=utf-8');
	die("Please install Composer dependencies.\n\nhttp://getcomposer.org");
}
