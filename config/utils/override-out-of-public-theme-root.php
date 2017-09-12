<?php

$initTheme[] = function () {
	add_filter('template_directory_uri', function ($args) {
		return get_site_url();
	});
};
