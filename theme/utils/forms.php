<?php

function generateFormAction($path) {
	return 'submit-' . basename($path, '.php');
}

function isFormValid($form, $path) {
	$action = generateFormAction($path);
	$form->setAction("?do=$action");
	if(isset($_GET['do']) && $_GET['do'] === $action && $form->isSubmitted()) {
		$form->validate();
		return $form->isSuccess();
	}
	return FALSE;
}
