<?php

$webmanifest['name'] = get_bloginfo('name');
$webmanifest['short_name'] = get_bloginfo('name');
$webmanifest['orientation'] = 'any';
$webmanifest['display'] = 'standalone';
$webmanifest['start_url'] = get_home_url();
$webmanifest['background_color'] = '#ffffff';
$webmanifest['theme_color'] = '#000000';
$webmanifest['icons'] = array_map(function($size) {
	return [
		'src' => 'https://placekitten.com/' . $size . '/' . $size,
		'sizes' => $size . 'x' . $size,
		'type' => 'image/jpg',
	];
}, [36, 96, 192, 256, 384, 512, 1024]);
