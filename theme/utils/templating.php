<?php

function translateString($str, $default = null)
{
	if (function_exists('icl_register_string') && function_exists('icl_translate')) {
		icl_register_string('theme-mango', $str, $default ?: $str);
		return icl_translate('theme-mango', $str, $default ?: $str);
	}

	return $default ?: $str;
}

MangoFilters::$set['translate'] = 'translateString';
