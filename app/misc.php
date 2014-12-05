<?php

$Request = $container->httpRequest;

$Url = $Request->url;
$Url->setScriptPath(str_replace('\\', '/', str_replace(realpath($_SERVER['DOCUMENT_ROOT']), '', realpath(WWW_DIR))) . '/');
