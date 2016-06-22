<?php

if(isset($_GET['styleguide'])) {
	view('styleguide', [ 'components' => Nette\Utils\Finder::findFiles('*.latte')->from(THEME_VIEWS_DIR . '/components') ]);
	exit;
}

// latte file has same name as this file
view([ 'greeting' => 'Hello' ]);
