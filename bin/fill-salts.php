<?php

require __DIR__.'/../vendor/autoload.php';

$contents = file_get_contents(__DIR__.'/../config/config.local.neon');
$newContents = Nette\Utils\Strings::replace($contents, '~([0-9a-zA-Z]{10} CHANGE ON PRODUCTION [0-9a-zA-Z]{10})~', function ($match) {
	return Nette\Utils\Random::generate(48, '0-9a-zA-Z');
});

file_put_contents(__DIR__.'/../config/config.local.neon', $newContents);

echo "Filled salts to config.local.neon\n";
