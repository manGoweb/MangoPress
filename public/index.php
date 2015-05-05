<?php

// Wait, wait. Are we deploying?!
if(file_exists(__DIR__ . '/maintenance.php')) {
	require __DIR__ . '/maintenance.php';
}

// run, WordPress, run!
if(file_exists(__DIR__ . '/wp-core/index.php')) {
	require __DIR__ . '/wp-core/index.php';
} else {
	header('Content-Type: text/plain;charset=utf-8');
	die("Composer dependencies are not installed.");
}
