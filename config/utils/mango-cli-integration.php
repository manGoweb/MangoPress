<?php

function getBuildstamp() {
	$buildstampPath = ABSPATH.'/assets/.buildstamp.txt';

	if (file_exists($buildstampPath)) {
		return trim(file_get_contents($buildstampPath));
	}

	return NULL;
}

$initTheme[] = function () {
	global $View;
	global $App;

	if(!isset($View)) {
		$View = new Nette\Utils\ArrayHash();
	}

	// Browsersync
	$View->mangoSnippet = null;
	$mangoSnippetPath = PROJECT_ROOT_DIR.'/.mango-snippet.html';

	if (!empty($App->parameters['livereload']) && file_exists($mangoSnippetPath)) {
		$View->mangoSnippet = Nette\Utils\Html::el()->setHtml(file_get_contents($mangoSnippetPath));
	}

	// Mango buildstamp
	$View->buildstamp = getBuildstamp();
};
