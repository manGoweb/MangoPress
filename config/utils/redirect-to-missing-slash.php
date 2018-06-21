<?php

$initTheme[] = function ($dir) {
	global $Url;

	if (is_user_logged_in()) {
		if($Url->getPath() === '/wp-admin') {
			$Url->setPath('/wp-admin/');
			wp_redirect((string) $Url);
			exit;
		}
	}
};
