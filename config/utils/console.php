<?php

$initTheme[] = function ($dir) {
	if (defined('MANGO_PRESS_CONSOLE') && MANGO_PRESS_CONSOLE) {
		runMangoPressConsole();
	}
};
