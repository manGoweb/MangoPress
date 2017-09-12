<?php

$initTheme[] = function ($dir) {
	$filepath = $dir.'/schema/navs.neon';

	if(file_exists($filepath)) {
		$array = Nette\Neon\Neon::decode(file_get_contents($filepath));
		register_nav_menus($array['register'] ?? []);
	}
};
