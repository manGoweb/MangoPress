<?php

$initTheme[] = function ($dir) {
	add_action('template_include', function ($template) {
		global $wp_query;
		bdump(basename($template));
		bdump($wp_query);

		return $template;
	});
};
