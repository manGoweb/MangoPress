<?php

namespace App\Models\Orm;

use Orm\DibiMapper;


abstract class Mapper extends DibiMapper
{

	/**
	 * @return string
	 */
	public function getTableName()
	{
		return (string) parent::getTableName();
	}

	/**
	 * @return SqlConventional
	 */
	protected function createConventional()
	{
		return new SqlConventional($this);
	}

}
