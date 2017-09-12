<?php

if (!$container->parameters['consoleMode']) {
	// If path to public directory is nested in document_root, the rest is a basePath
	$Req = $container->getByType(Nette\Http\Request::class);
	$Url = $Req->getUrl();
	if (0 === strpos(realpath(ABSPATH), realpath($_SERVER['DOCUMENT_ROOT']))) {
		$basePath = trim(substr(realpath(ABSPATH), strlen(realpath($_SERVER['DOCUMENT_ROOT']))), '/\\').'/';
		$Url->setScriptPath($basePath);
	}
}
