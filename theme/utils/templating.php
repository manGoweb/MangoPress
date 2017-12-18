<?php

MangoPressTemplating::init();

// czech number format
MangoFilters::$set['number'] = function($number, $decimal = 2) {
	if(fmod($number, 1) == 0) {
		$decimal = 0;
	}
	$sep = ',';
	$formatted = number_format($number, $decimal, $sep, "\xC2\xA0");
	if($decimal) {
		$formatted = Strings::replace($formatted, '~,?0$~');
	}

	return $formatted;
};

// czech Kč number format
MangoFilters::$set['czk'] = function($number, $decimal = 2){
	if(fmod($number, 1) == 0) {
		$decimal = 0;
	}
	$sep = ',';
	$formatted = number_format($number, $decimal, $sep, "\xC2\xA0");
	if($decimal) {
		$formatted = Strings::replace($formatted, '~,?0*$~', '');
	}

	return $formatted . (!$decimal ? ',-' : '') . "\xC2\xA0Kč";
};

// czech date format
MangoFilters::$set['datum'] = function($date, $format) {
	$keys['en']['days'] = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
	$keys['cz']['days'] = ['pondělí', 'úterý', 'středa', 'čtvrtek', 'pátek', 'sobota', 'neděle'];

	$keys['en']['months'] = ['January','February','March','April','May','June','July','August','September','October','November','December'];
	$keys['cz']['months'] = ['ledna','února','března','dubna','května','června','července','srpna','září','října','listopadu','prosince'];

	for ($i = 0; $i < 7; $i++) {
		$keys['en']['days-short'][] = substr($keys['en']['days'][$i], 0, 3);
		$keys['cz']['days-short'][] = substr($keys['cz']['days'][$i], 0, 2);
	}

	for ($i = 0; $i < 12; $i++) {
		$keys['en']['months-short'][] = substr($keys['en']['months'][$i], 0, 3);
		$keys['cz']['months-short'][] = substr($keys['cz']['months'][$i], 0, 3);
	}
	$keys['en']['others'] = ['th', 'am', 'AM', 'pm', 'PM'];
	$keys['cz']['others'] = ['.', 'dopoledne', 'dopoledne', 'odpoledne', 'odpoledne'];

	$result = date($format, strtotime($date));

	foreach ($keys['en'] as $key => $item) {
		$result = str_replace($keys['en'][$key], $keys['cz'][$key], $result);
	}

	return $result;
};
