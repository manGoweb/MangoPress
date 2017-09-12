<?php

define('NBSP', "\xC2\xA0");

$initTheme[] = function ($dir) {
	define('THEME_VIEWS_DIR', $dir . '/views');
	MangoPressTemplating::init();
};
