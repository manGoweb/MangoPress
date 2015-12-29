<?php

function get_field($id, $key, $as = null, $single = true) {
	$id = ($id instanceof WP_Post) ? $id->ID : $id;
	$as = strtolower($as);
	$val = get_post_meta($id, $key, $single);
	if(!$val) {
		return $val;
	}
	switch($as) {
		case 'date':
		case 'datetime':
		case 'time':
			return new \DateTime($val);
		case 'number':
			return (double) $val;
		case 'string':
			return (string) $val;
		case 'post':
			return get_post($val);
		default:
			return $val;
	}
}

function meta($id, $key, $as = null) {
	return get_field($id, $key, $as);
}

function get_field_array($id, $key, $as = null) {
	return get_field($id, $key, $as, false);
}

function meta_array($id, $key, $as = null) {
	return get_field($id, $key, $as, false);
}

function get_image_url($id, $size = 'thumbnail') {
	return wp_get_attachment_image_src($id, $size)[0];
}

function get_thumbnail_url($id, $size = 'thumbnail') {
	return get_image_url(get_post_thumbnail_id($id), $size);
}
