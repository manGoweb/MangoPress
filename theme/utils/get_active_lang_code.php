<?php

function get_active_lang_code() {
	return explode('_', get_locale())[0];
}
