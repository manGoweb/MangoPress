<?php

require_once __DIR__.'/lib/neon-schema.php';

$initTheme[] = function ($dir) {

	add_action('admin_enqueue_scripts', function () {
		$basePath = WP_SITEURL;
		add_editor_style($basePath.'/assets/styles/'.getBuildstamp().'wp-editor.css');
		wp_enqueue_style('mango-styles-admin', $basePath.'/assets/styles/wp-admin.css', null, getBuildstamp());
		wp_enqueue_script('mango-scripts-admin', $basePath.'/assets/scripts/wp-admin.js', ['jquery', 'wp-element'], getBuildstamp(), true);
	});

	add_action('enqueue_block_editor_assets', function () {
		$basePath = WP_SITEURL;
		wp_enqueue_style('mango-styles-gutenberg', $basePath.'/assets/styles/gutenberg.css', null, getBuildstamp());
		wp_enqueue_script('mango-scripts-gutenberg', $basePath.'/assets/scripts/gutenberg.js', ['jquery', 'wp-element', 'wp-blocks', 'wp-components', 'wp-i18n', 'wp-editor'], getBuildstamp(), true);
	});

	// allow styling, when wp admin bar might be visible
	add_action('wp_enqueue_scripts', function () {
		if(is_user_logged_in()) {
			$basePath = WP_SITEURL;
			wp_enqueue_style('mango-styles-admin', $basePath.'/assets/styles/wp-admin.css', null, getBuildstamp());
			wp_enqueue_script('mango-scripts-admin', $basePath.'/assets/scripts/wp-admin.js', ['jquery', 'wp-element'], getBuildstamp(), true);
		}
	});

};
