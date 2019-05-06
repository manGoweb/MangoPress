<?php

namespace App\Commands;
use Nextras\Migrations\Bridges\SymfonyConsole;

class ContinueCommand extends SymfonyConsole\ContinueCommand
{
	protected static $defaultName = 'migrations:continue';
}
