<?php

$initTheme[] = function ($dir) {
	$shortcodesDir = $dir.'/shortcodes';

	foreach (glob($shortcodesDir.'/*.php') as $filepath) {
		$shortcode = basename($filepath, '.php');
		$fn = require_once $filepath;
		if (empty($fn) || is_bool($fn)) {
			continue;
		}
		add_shortcode($shortcode, $fn);
	}

	foreach (glob($shortcodesDir.'/*.latte') as $filepath) {
		$shortcode = basename($filepath, '.latte');
		if (!file_exists($shortcodesDir."/$shortcode.php")) {
			add_shortcode($shortcode, function ($attrs = [], $content = null) use ($filepath) {
				$attrs = empty($attrs) ? [] : $attrs;

				return renderLatteToString($filepath, ['attrs' => $attrs, 'content' => $content]);
			});
		}
	}
};
