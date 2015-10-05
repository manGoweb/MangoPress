<?php

function eu_cookies($lang = 'cs', $force = FALSE) {
	if($lang === 'cs') {
		$lang = 'cz';
	}
	if($force || !eu_cookies_allowed()) {
		return Nette\Utils\Html::el()->setHtml('<script src="//s3-eu-west-1.amazonaws.com/fucking-eu-cookies/' . $lang . '.js" async></script>');
	}
	return '';
}

function eu_cookies_allowed() {
	return !empty($_COOKIE['fucking-eu-cookies']) && $_COOKIE['fucking-eu-cookies'] === '1';
}
