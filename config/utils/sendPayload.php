<?php

function sendPayload($params = []) {
	global $Payload;
	$params = (array) $params;
	$payloadArray = (array) $Payload;

	Tracy\Debugger::$productionMode = TRUE;
	header('Content-Type:application/json;charset=utf-8');

	print(json_encode($params + $payloadArray)) and die();
}
