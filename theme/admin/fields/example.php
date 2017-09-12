<?php

return function($field, $value) {
	return sprintf(
		'<input type="tel" name="%s" id="%s" value="%s" pattern="\d{3}-\d{4}">',
		$field['field_name'],
		$field['id'],
		$meta
	);
};
