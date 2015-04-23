<?php

function shortcode_gallery($atts) {
	$ids = explode(',', $atts['ids']);
	$pairs = [];

	foreach($ids as $id) {
		$pairs[get_image_url($id)] = get_image_url($id, 'large');
	}
	return renderLatteToString(__DIR__ . '/gallery.latte', [ 'pairs' => $pairs ]);
}

add_shortcode('gallery', 'shortcode_gallery');
