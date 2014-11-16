<?php

function toRelativeUrl($url) {
	$urlscript = new Nette\Http\UrlScript($url);
	return '/' . $urlscript->getRelativeUrl();
}

function render($path, $parameters = array()) {
	$defaults = array(
		'baseUrl' => get_site_url(),
		'basePath' => toRelativeUrl(get_site_url()),
		'themeUrl' => get_template_directory_uri(),
		'themePath' => toRelativeUrl(get_template_directory_uri()),
	);

	$parameters += $defaults;

	$latte = new Latte\Engine;
	$latte->setTempDirectory(TEMP_DIR);

	// $latte->addFilter('money', function($val) { return '...'; });

	$latte->render($path, $parameters);
}

function view($view, $parameters = array()) {
	$path = THEME_VIEWS_DIR . "/$view.latte";
	return render($path, $parameters);
}
