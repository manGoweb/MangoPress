<?php

function WPML_active()
{
	return function_exists('icl_get_languages');
}

function WPML_get_languages()
{
	return icl_get_languages('skip_missing=0');
}

MangoFilters::$set['translate'] = function ($string, $default = null, $domain = 'theme') {
	return __($string, $domain);
};
