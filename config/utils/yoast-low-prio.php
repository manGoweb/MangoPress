<?php

$initTheme[] = function () {
	add_filter('wpseo_metabox_prio', function() {
		return 'low';
	});
};
