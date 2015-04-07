<?php

// Wait, wait. Are we deploying?!
if(file_exists(__DIR__ . '/maintenance.php')) {
	require __DIR__ . '/maintenance.php';
}

// run, WordPress, run!
require dirname(__FILE__) . '/wp-core/index.php';
