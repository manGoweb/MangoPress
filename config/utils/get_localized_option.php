<?php

use Nette\Utils\Strings;

function clear_postfix($str, $postfix)
{
	if (Strings::endsWith($str, $postfix)) {
		$len = Strings::length($str) - Strings::length($postfix);
		return Strings::substring($str, 0, $len);
	}
	return $str;
}

// get_option for localized admin pages
function get_localized_option($group, $field = null, $lang = null)
{
	static $settings;
	if (!$settings) {
		$settings = [];
	}
	if (!$lang) {
		$lang = get_active_lang_code();
	}
	if (!isset($settings[$group])) {
		$settings[$group] = get_option($group) ?: [];
	}
	$postfix = str_replace('{mp:lang}', $lang, $settings[$group]['postfix-format'] ?? '');
	if (!$field) {
		$group = $settings[$group];
		$result = [];
		foreach ($group as $key => $val) {
			$result[clear_postfix($key, $postfix)] = $val;
		}
		return $result;
	}
	return $settings[$group][$field.$postfix] ?? $settings[$group][$field] ?? null;
}
