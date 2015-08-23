<?php

// Latte: {$Forms[contact]}

use Nette\Forms\Form;

$form = new Form;
$form->setRenderer(new \Nextras\Forms\Rendering\Bs3FormRenderer);

$form->addProtection('Detected robot activity.');

$c = $form->addContainer('frm');

$c->addText('email', 'Your email')
	->addCondition($form::FILLED)
		->addRule($form::EMAIL, 'Please fill in a valid e-mail address.');

$c->addTextarea('message', 'Message')
	->setRequired('Please fill in a message.');

$c->addSubmit('send', 'Send');

if(isFormValid($form, __FILE__)) {
	dump($c->getValues());
}

return $form;
