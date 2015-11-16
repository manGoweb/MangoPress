<?php

$View->mangoSnippet = NULL;
$mangoSnippetPath = BASE_DIR . '/.mango-snippet.html';

if(!empty($App->parameters['mango']) && file_exists($mangoSnippetPath)) {
	$View->mangoSnippet = Nette\Utils\Html::el()->setHtml(file_get_contents($mangoSnippetPath));
}
