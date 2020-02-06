<?php

global $ApiQuery;
global $Req;

switch($ApiQuery[0]) {
	case 'ping':
		$Payload['result'] = 'pong';
		break;
	case 'get':
		$Payload['get'] = $Req->getQuery();
		break;
	case 'server-time':
		sleep(1);
		$Payload['server-time'] = current_time('j. n. Y H:i:s');
		break;
	case 'system-check':
		\Tracy\Debugger::log('Checking if Papertrail is working');
		throw new \Exception('Checking if Sentry is working');
		break;
}
