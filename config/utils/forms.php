<?php

namespace MangoPress;

function generateFormAction($path)
{
	return 'submit-' . basename($path, '.php');
}

function formHandler(\Nette\Forms\Form $form, string $path, callable $onSubmit) {

	global $Url;
	$action = generateFormAction($path);
	$formUrl = clone $Url;
	$formUrl->setQueryParameter("do", $action);
	$form->setAction($formUrl);
	$form->getElementPrototype()->data('nette-form', 'true');
	if (isset($_GET['do']) && $_GET['do'] === $action && $form->isSubmitted()) {
		$form->validate();
		return call_user_func($onSubmit, $form);
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
		$View = $View ?? \Nette\Utils\ArrayHash::from([]);

		foreach (glob($dir . '/forms/*.php') as $filepath) {
			$Forms[basename($filepath, '.php')] = require_once $filepath;
		}

		$View['Forms'] = $Forms;
	});
};
