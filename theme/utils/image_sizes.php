<?php

add_action('after_setup_theme', 'add_image_sizes');
function add_image_sizes() {
	add_image_size('xl', 1920);
	add_image_size('l', 1280);
	add_image_size('m', 960);
	add_image_size('s', 720);
	add_image_size('xs', 440);
	add_image_size('xxs', 320);
	add_image_size('xxxs', 240);
}
