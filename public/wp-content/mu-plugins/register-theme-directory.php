<?php

register_theme_directory(PROJECT_ROOT_DIR);

if (is_blog_installed()) {
	add_action('plugins_loaded', 'search_theme_directories');
}
