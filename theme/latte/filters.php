<?php

MangoFilters::$set['url'] = 'rawurlencode';

foreach (array('normalize', 'toAscii', 'webalize', 'padLeft', 'padRight', 'reverse') as $name) {
	MangoFilters::$set[$name] = 'Nette\Utils\Strings::' . $name;
}

MangoFilters::$set['null'] = function () {};

MangoFilters::$set['length'] = function ($var) {
	return is_string($var) ? Nette\Utils\Strings::length($var) : count($var);
};

MangoFilters::$set['modifyDate'] = function ($time, $delta, $unit = NULL) {
	return $time == NULL ? NULL : Nette\Utils\DateTime::from($time)->modify($delta . $unit); // intentionally ==
};

function lazy_post($id) {
	if(is_object($id)) {
		return $id;
	}
	return get_post($id);
}

MangoFilters::$set['wp_title'] = function($id) {
	$post = lazy_post($id);
	if(!$post) return $id;

	return apply_filters('the_title', $post->post_title);
};

MangoFilters::$set['wp_content'] = function($id) {
	$post = lazy_post($id);
	if(!$post) return $id;

	return apply_filters('the_content', $post->post_content);
};

MangoFilters::$set['wp_meta'] = function($id, $meta) {
	$post = lazy_post($id);
	if(!$post) return $id;

	dump(get_post_meta($post->ID, $meta));

	return get_post_meta($post->ID, $meta);
};

MangoFilters::$set['wp_image'] = function($id, $size = 'thumbnail') {
	$post = lazy_post($id);
	if(!$post) return $id;

	return wp_get_attachment_image_src($post->ID, $size)[0];
};

MangoFilters::$set['wp_thumbnail'] = function($id, $size = 'thumbnail') {
	$post = lazy_post($id);
	if(!$post) return $id;

	return wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), $size)[0];
};

// you may add more
