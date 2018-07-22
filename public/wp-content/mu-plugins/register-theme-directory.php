<?php

register_theme_directory(PROJECT_ROOT_DIR);

// register_theme_directory uses untrailingslashit()
// which simply rtrims /
// if we are in the root then the directory won't be registered
if (PROJECT_ROOT_DIR === '/') {
	global $wp_theme_directories;
	$wp_theme_directories[] = PROJECT_ROOT_DIR;
}

if (is_blog_installed()) {
	add_action('plugins_loaded', 'search_theme_directories');
}
