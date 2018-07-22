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
	case 'server-time';
		sleep(1);
		$Payload['server-time'] = current_time('j. n. Y H:i:s');
		break;
}
