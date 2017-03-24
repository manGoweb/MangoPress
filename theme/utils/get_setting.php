<?php

function get_setting($group, $field) {
	static $settings;
	if (!$settings) {
		$settings = [];
	}
	if (!isset($settings[$group])) {
		$settings[$group] = get_option($group);
		$settings[$group]['postfix'] = str_replace('{lang}', get_active_lang_code(), $settings[$group]['postfix-format'] ?? '');
	}
	return $settings[$group][$field . $settings[$group]['postfix']] ?? $settings[$group][$field] ?? NULL;
}
