<?php

register_theme_directory(BASE_DIR);

if(is_blog_installed()) {
	add_action('plugins_loaded', 'search_theme_directories');
}
