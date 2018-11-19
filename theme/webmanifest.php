<?php

return [
	'name' => get_bloginfo('name'),
	'short_name' => get_bloginfo('name'),
	'orientation' => 'any',
	'display' => 'standalone',
	'start_url' => get_home_url(),
	'background_color' => '#ffffff',
	'theme_color' => '#000000',
	'icons' => array_values(array_filter(array_map(function($size) {
		$filename = 'assets/images/site-icon-' . $size . '.png';
		return file_exists(ABSPATH . $filename) ? [
			'src' => get_home_url() . '/' . $filename,
			'sizes' => $size . 'x' . $size,
			'type' => 'image/png',
		] : null;
	}, [36, 96, 192, 256, 384, 512, 1024]))),
];
