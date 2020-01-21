<?php

$initTheme[] = function () {
	setlocale(LC_ALL, get_locale().'.UTF-8');
	setlocale(LC_NUMERIC, "en_US.UTF-8");
};
