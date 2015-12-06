<?php

function page_id($slug) {
	$page = get_page_by_path($slug, 'OBJECT', 'page');
	if($page) {
		return $page->ID;
	}
	return null;
}
