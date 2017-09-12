<?php

function initTheme($dir = null)
{
	global $initTheme;
	if (!$dir) {
		$bt = debug_backtrace();
		$dir = dirname($bt[0]['file']);
	}
	foreach ($initTheme as $fn) {
		call_user_func($fn, $dir);
	}
}
