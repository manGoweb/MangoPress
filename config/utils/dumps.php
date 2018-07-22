<?php

// dump and die
function dd() {
	call_user_func_array('dump', func_get_args());
	die();
}

// bar dump and die
function bdd() {
	call_user_func_array('bdump', func_get_args());
	die();
}
