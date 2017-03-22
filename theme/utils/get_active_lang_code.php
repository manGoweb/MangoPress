<?php

function get_active_lang_code() {
	global $sitepress;
	if (isset($sitepress)) { // WPML is enabled
		return $sitepress->get_current_language();
	}
	return explode('_', get_locale())[0];
}
