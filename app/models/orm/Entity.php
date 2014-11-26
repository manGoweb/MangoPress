<?php

namespace App\Models\Orm;

use DateTime;
use Orm;


/**
 */
abstract class Entity extends Orm\Entity
{

	/**
	 * Adds support for FQN annotations
	 * @param $entityClass
	 * @return Orm\MetaData
	 */
	public static function createMetaData($entityClass)
	{
		return FQNAnnotationMetaData::getMetaData($entityClass);
	}

}
