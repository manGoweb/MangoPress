<?php

MangoFilters::$set['webalize'] = function($str) {
	return Nette\Utils\Strings::webalize($str);
};

// you may add more
