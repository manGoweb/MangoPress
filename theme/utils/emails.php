<?php

function sendEmail($message, $to = NULL) {
	global $App;
	if($to) {
		$message->addTo($to);
	}
	return $App->getService('mailer')->send($message);
}

function makeEmail($view, $params = []) {
	global $App;
	$msg = new Nette\Mail\Message;

	$viewPath = __DIR__ . '/../emails/' . $view . '.latte';

	if(!file_exists($viewPath)) {
		throw new \Exception("Email template does not exist: $viewPath");
	}

	$msg->setFrom($App->parameters['fromEmail']);
	$msg->setHtmlBody(renderLatteToString($viewPath, $params));

	return $msg;
}
