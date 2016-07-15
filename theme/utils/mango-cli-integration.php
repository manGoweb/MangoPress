<?php

// Browsersync
$View->mangoSnippet = NULL;
$mangoSnippetPath = BASE_DIR . '/.mango-snippet.html';

if(!empty($App->parameters['mango']) && file_exists($mangoSnippetPath)) {
	$View->mangoSnippet = Nette\Utils\Html::el()->setHtml(file_get_contents($mangoSnippetPath));
}


// Mango buildstamp
$View->buildstamp = NULL;
$buildstampPath = BASE_DIR . '/public/assets/.buildstamp.txt';

if(file_exists($buildstampPath)) {
	$View->buildstamp = trim(file_get_contents($buildstampPath));
}
