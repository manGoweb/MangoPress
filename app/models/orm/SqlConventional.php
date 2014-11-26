<?php

namespace App\Models\Orm;

use Orm;


class SqlConventional extends Orm\SqlConventional
{

	/**
	 * @var string[]
	 */
	private $tableCache = [];


	/**
	 * Ignores namespace
	 *
	 * @param Orm\IRepository $repository
	 * @return string
	 */
	public function getTable(Orm\IRepository $repository)
	{
		$class = get_class($repository);
		if (!isset($this->tableCache[$class]))
		{
			// remove namespace and Repository suffix
			$pos = strrpos($class, '\\');
			$name = substr($class, $pos + 1, strlen($class) - $pos - 11);

			// transform camelCase to snake_case
			$this->tableCache[$class] = strtolower(
				preg_replace('/([A-Z]+)([A-Z])/', '\1_\2',
					preg_replace('/([a-z\d])([A-Z])/', '\1_\2', $name)));
		}

		return $this->tableCache[$class];
	}

}
