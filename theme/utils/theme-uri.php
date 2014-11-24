<?php

function filter_current_theme_uri($args) {
	return get_site_url();
}

add_filter('template_directory_uri', 'filter_current_theme_uri');
