<?php

namespace MangoPress;

use Nette;

class Container extends Nette\Object {

	private $object;

	public function __construct(Nette\DI\Container $object) {
		$this->object = $object;
	}

	public function &__get($name)
	{
		$tmp = @$this->object->{$name};
		return $tmp;
	}

	public function __set($name, $value)
	{
		@$this->object->{$name} = $value;
	}

	public function __isset($name)
	{
		return @isset($this->object->{$name});
	}

	public function __unset($name)
	{
		unset($this->object->{$name});
	}

	function __call($method, $args) {
		return call_user_func_array(array($this->object, $method), $args);
	}

}
