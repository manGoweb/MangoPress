<?php

add_filter('pto/get_options', function($options) {
	$options['capability'] = 'manage_options';
	return $options;
});
