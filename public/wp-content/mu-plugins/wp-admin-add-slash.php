<?php
return;
if(Nette\Utils\Strings::endsWith($Url->getPath(), '/wp-admin')) {
	$Url->setPath($Url->getPath() . '/');
	header('Location: ' . (string) $Url);
	exit;
}
