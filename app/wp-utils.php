<?php

$Req = $container->getService('httpRequest');

$Url = $Req->getUrl();
$cwd = strtolower(realpath('.'));
$wpdir = strtolower(realpath(WP_DIR));

if(substr($cwd, 0, strlen($wpdir)) === $wpdir) {
	$scriptPath = '/';
	$basePathTest = str_replace('\\', '/', str_replace(realpath($_SERVER['DOCUMENT_ROOT']), '', realpath(WWW_DIR))) . '/';
	if(strpos(strtolower($Url->getPath()), strtolower($basePathTest)) === FALSE) {
		$Url->setScriptPath($scriptPath);
	} else {
		$Url->setScriptPath($basePathTest);
	}
}
