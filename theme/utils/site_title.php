<?php

function site_title() {
	if (function_exists('is_tag') && is_tag()) {
		return 'Tag Archive for "'.$tag.'" - ' . get_bloginfo('name');
	} elseif (is_archive()) {
		return wp_title('', FALSE) . ' Archive - ' . get_bloginfo('name');
	} elseif (is_search()) {
		return 'Search for "'.wp_specialchars($s).'" - ' . get_bloginfo('name');
	} elseif (!(is_404()) && (is_single()) || (is_page())) {
		return wp_title('', FALSE) . ' - ' . get_bloginfo('name');
	} elseif (is_404()) {
		return 'Not Found - ' . get_bloginfo('name');
	}
	return get_bloginfo('name');
}
