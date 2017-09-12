<?php

$initTheme[] = function ($dir) {
	$filepath = $dir.'/schema/image-sizes.neon';

	function parseImageSizeString($str) {
		$crop = Nette\Utils\Strings::endsWith($str, 'c') || Nette\Utils\Strings::endsWith($str, 'crop');
		$parts = array_filter(array_map('trim', explode('x', $str)));
		$parts = array_map('intval', $parts);
		return [ $parts[0], $parts[1] ?? $parts[0], $crop ];
	}

	if(file_exists($filepath)) {
		$array = Nette\Neon\Neon::decode(file_get_contents($filepath));
		$sizes = $array['register'] ?? [];

		foreach($sizes as $name => $size) {
			list($a, $b, $c) = parseImageSizeString((string) $size);
			add_image_size($name, $a, $b, $c);
		}
	}
};
