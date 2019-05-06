<?php

namespace App\Commands;
use Nextras\Migrations\Bridges\SymfonyConsole;

class CreateCommand extends SymfonyConsole\CreateCommand
{
	protected static $defaultName = 'migrations:create';
}
