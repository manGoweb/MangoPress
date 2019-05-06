<?php

function generateFormAction($path)
{
	return 'submit-' . basename($path, '.php');
}

function isFormValid($form, $path)
{
	global $Url;
	$action = generateFormAction($path);
	$formUrl = clone $Url;
	$formUrl->setQueryParameter("do", $action);
	$form->setAction($formUrl);
	if (isset($_GET['do']) && $_GET['do'] === $action && $form->isSubmitted()) {
		$form->validate();
		return $form->isSuccess();
	}
	return false;
}

$initTheme[] = function ($dir) {
	add_action('template_redirect', function () use ($dir) {
		global $Forms;
		global $View;
		global $Post;
		$Post = get_queried_object();

		$Forms = $Forms ?? [];
		$View = $View ?? Nette\Utils\ArrayHash::from([]);

		foreach (glob($dir . '/forms/*.php') as $filepath) {
			$Forms[basename($filepath, '.php')] = require_once $filepath;
		}

		$View['Forms'] = $Forms;
	});
};
