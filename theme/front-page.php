<?php

// Template Name: Front page
if (!empty($Req->getQuery('view'))) {
	view($Req->getQuery('view'));
} elseif (isset($Req->getQuery()['sg'])) {
	view('styleguide', [ 'components' => \MangoPress\Components::findAll() ]);
	exit;
} else {
	view();
}
