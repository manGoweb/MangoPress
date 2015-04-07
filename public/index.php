<?php

// Wait, wait. Are we deploying?!
if(file_exists(__DIR__ . '/maintenance.php')) {
	require __DIR__ . '/maintenance.php';
}

// run, WordPress, run!
require __DIR__ . '/wp-core/index.php';
