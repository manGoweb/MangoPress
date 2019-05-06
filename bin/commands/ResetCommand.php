<?php

namespace App\Commands;
use Nextras\Migrations\Bridges\SymfonyConsole;

class ResetCommand extends SymfonyConsole\ResetCommand
{
	protected static $defaultName = 'migrations:reset';
}
