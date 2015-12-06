<?php

// non-breakable space UTF-8 char
define('NBSP', "\xc2\xa0");

function nbsp($str) {
	$str = trim($str);
	return Nette\Utils\Strings::replace($str, '~[ ]~', NBSP);
}
