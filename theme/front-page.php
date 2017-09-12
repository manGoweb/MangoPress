<?php

// Template Name: Front page
if (!empty($Req->getQuery('view'))) {
	view($Req->getQuery('view'));
} elseif (isset($Req->getQuery()['sg']) || isset($Req->getQuery()['styleguide'])) {
	$styleguideView = $Req->getQuery('sg') ?: $Req->getQuery('styleguide') ?: 'index';
	view('styleguide/'.$styleguideView);
} else {
	view();
}
