<?php

function get_setting($group, $field, $lang = NULL) {
	static $settings;
	if (!$settings) {
		$settings = [];
	}
	if (!$lang) {
		$lang = get_active_lang_code();
	}
	if (!isset($settings[$group])) {
		$settings[$group] = get_option($group);
	}

	$postfix = str_replace('{lang}', $lang, $settings[$group]['postfix-format'] ?? '');
	return $settings[$group][$field . $postfix] ?? $settings[$group][$field] ?? NULL;
}
