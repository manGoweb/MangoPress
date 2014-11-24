<?php

define('THEME_DIR', dirname(__FILE__));
define('THEME_UTILS_DIR', THEME_DIR . '/utils');
define('THEME_VIEWS_DIR', THEME_DIR . '/views');

foreach(array( 'latte', 'theme-uri' ) as $util_name) {
	require THEME_UTILS_DIR . "/$util_name.php";
}

