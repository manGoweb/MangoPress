<?php

if(defined('DOING_AJAX') && DOING_AJAX) {
	Tracy\Debugger::$productionMode = TRUE;
}
