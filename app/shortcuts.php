<?php

if (!function_exists('barDump')) {
	function barDump($var)
	{
		array_map('Tracy\Debugger::barDump', func_get_args());
		return $var;
	}
}
