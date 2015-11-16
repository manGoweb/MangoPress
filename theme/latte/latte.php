<?php

require __DIR__ . '/utils/MangowebLatteMacroSet.php';
require __DIR__ . '/utils/MangowebLatteFilterSet.php';

function toPath($url) {
	$urlscript = new Nette\Http\UrlScript($url);
	return rtrim($urlscript->scheme . '://' . $urlscript->authority . $urlscript->path, '/');
}

function toRelativePath($url) {
	$urlscript = new Nette\Http\UrlScript($url);
	return rtrim($urlscript->getPath(), '/');
}

function renderLatte($path, $parameters = array()) {
	global $App;
	global $View;
	global $wp_query;
	global $post;


	$fullParameters = array(
		'App' => $App,
		'baseUrl' => toPath(WP_HOME),
		'basePath' => toRelativePath(WP_HOME),
		'assetsUrl' => toPath(WP_HOME) . '/assets',
		'assetsPath' => toRelativePath(WP_HOME) . '/assets',
		'wp_query' => $wp_query,
		'post' => $post
	);

	foreach($View as $key => $val) {
		$fullParameters[$key] = $val;
	}

	foreach($parameters as $key => $val) {
		$fullParameters[$key] = $val;
	}

	$latte = new Latte\Engine;
	$latte->setTempDirectory(TEMP_DIR . '/cache/latte');

	MangowebLatteMacroSet::install($latte->getCompiler());
	Nette\Bridges\FormsLatte\FormMacros::install($latte->getCompiler());

	MangowebLatteFilterSet::install($latte);

	return $latte->render($path, (array) $fullParameters);
}

function view($view, $parameters = array()) {
	$path = THEME_VIEWS_DIR . "/$view.latte";
	return renderLatte($path, $parameters);
}

function renderLatteToString($path, $parameters = array()) {
	ob_start();
	renderLatte($path, $parameters);
	$str = ob_get_contents();
	ob_end_clean();
	return $str;
}
