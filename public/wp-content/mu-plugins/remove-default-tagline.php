<?php

add_action('wp_install', function () {
	update_option('blogdescription', '');
});
