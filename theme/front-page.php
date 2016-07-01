<?php

if(isset($_GET['styleguide'])) {
	view('styleguide');
	exit;
}

// latte file has same name as this file
view([ 'greeting' => 'Hello' ]);
