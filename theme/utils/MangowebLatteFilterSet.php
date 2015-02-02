<?php

class MangowebLatteFilterSet {

	public static function install(Latte\Engine $latte) {
		$me = new static;
		$latte->addFilter('emphasize', array($me, 'filterEmphasize'));
	}

	public function filterEmphasize($value) {
		return "› $value ‹";
	}

}
