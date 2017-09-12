<?php

global $ApiQuery;
global $Req;

switch($ApiQuery[0]) {
	case 'ping';
		$Payload['result'] = 'pong';
		break;
	case 'get';
		$Payload['get'] = $Req->getQuery();
		break;
}
