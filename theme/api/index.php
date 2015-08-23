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

Tracy\Debugger::$productionMode = TRUE;
header('Content-Type:application/json;charset=utf-8');
print(json_encode($payload)) and die();
