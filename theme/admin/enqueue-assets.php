<?php

function mango_admin_assets() {
	wp_enqueue_style('mango-admin-style', get_home_url() . '/assets/styles/wp-admin.css');
	add_editor_style(get_home_url() . '/assets/styles/wp-editor.css');
	$script_path = '/assets/scripts/wp-admin.js';
	if(file_exists(WWW_DIR . $script_path)) {
		$v = md5_file(WWW_DIR . $script_path);
		wp_enqueue_script('nette-forms-script', 'https://nette.github.io/resources/js/netteForms.min.js', null, $v, true);
		wp_enqueue_script('mango-admin-script', get_home_url() . $script_path, null, $v, true);
	}
}

add_action( 'admin_init', 'mango_admin_assets' );
