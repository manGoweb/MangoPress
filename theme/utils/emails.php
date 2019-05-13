<?php

function sendEmail($message, $to = null)
{
	global $App;
	if ($to) {
		$message->addTo($to);
	}
	return $App->getService('mail.mailer')->send($message);
}

function makeEmail($view, $params = [])
{
	global $App;
	$msg = new Nette\Mail\Message;
	$viewPath = __DIR__ . '/../emails/' . $view . '.latte';
	$stylesPath = __DIR__ . '/../emails/styles.php';
	if (!file_exists($viewPath)) {
		throw new \Exception("Email template does not exist: $viewPath");
	}
	$msg->setFrom($App->parameters['fromEmail']);
	if (file_exists($stylesPath)) {
		$styles = require $stylesPath;
	}
	$msg->setHtmlBody(renderLatteToString($viewPath, array_merge([ 'styles' => $styles ?? [] ], $params)));
	return $msg;
}
