<?php

// $ApiRequest = [ 'param1', 'param2', ... ]

$payload = [
	'status' => 'hello'
];

switch($ApiRequest[0]) {
	case 'ping':
		$payload['status'] = 'pong';
		break;
	case 'echo':
		$payload = [
			'status' => 'ok',
			'data' => $ApiRequest
		];
		break;
}

sendPayload($payload);
