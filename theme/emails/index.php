<?php

function sendEmail($message) {
	global $App;
	return $App->mailer->send($message);
}

function makeEmail($view, $params = []) {
	global $App;
	$msg = new Nette\Mail\Message;

	$viewPath = __DIR__ . '/templates/' . $view . '.latte';

	if(!file_exists($viewPath)) {
		throw new \Exception("Email template does not exist: $viewPath");
	}

	$msg->setFrom($App->parameters['fromEmail']);
	$msg->setHtmlBody(renderLatteToString($viewPath, $params));

	return $msg;
}
