<?php

add_action('admin_init', function() {
	wp_enqueue_style('fontawesome', 'https://use.fontawesome.com/releases/v5.1.1/css/all.css', '', '5.1.1', 'all');
});

function normalizeFontAwesomeStyle(array $styles, $forceWeight = null) {
	$tr = [
		'outline' => 'regular',
	];

	$forceWeight = $tr[$forceWeight] ?? $forceWeight;

	if(in_array('brands', $styles, TRUE)) {
		return [ 'section' => 'Brands', 'fontWeight' => 'normal' ];
	}

	$solid = null;
	if(in_array('solid', $styles, TRUE)) {
		$solid = [ 'section' => 'Free', 'fontWeight' => '900' ];
	}

	$regular = null;
	if(in_array('regular', $styles, TRUE)) {
		$regular = [ 'section' => 'Free', 'fontWeight' => '400' ];
	}

	if($forceWeight === 'regular') {
		list($regular, $solid) = [ $solid, $regular ];
	}

	return $solid ?: $regular ?: [ 'section' => 'Free', 'fontWeight' => '400' ];
}

function normalizeFontAwesomeIcon($struct, $forceWeight = null) {
	return [ 'code' => $struct['unicode'], 'style' => normalizeFontAwesomeStyle($struct['styles'], $forceWeight) ];
}

function getFontAwesomeCode($name) {
	static $iconSet;
	$parts = explode('|', $name);
	$name = Nette\Utils\Strings::lower($parts[0]);

	$iconSet = $iconSet ?? Nette\Neon\Neon::decode(file_get_contents(__DIR__ . '/font-awesome.neon'));


	if(!empty($iconSet[$name])) {
		return normalizeFontAwesomeIcon($iconSet[$name], $parts[1] ?? null);
	}
	return null;
}

add_action('admin_head', function() {
	global $FontAwesomeIcons;
	$FontAwesomeIcons = $FontAwesomeIcons ?? [];
	if(!empty($FontAwesomeIcons)) {
		renderLatte(__DIR__ . '/fontAwesomeStylesheet.latte', [ 'icons' => $FontAwesomeIcons ]);
	}
});
