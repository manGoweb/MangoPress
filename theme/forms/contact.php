<?php

// Latte: {$Forms[contact]}

use Nette\Forms\Form;

$form = new Form;

$form->getElementPrototype()->class = 'defaultForm';

$form->addProtection('Detected robot activity.');

$c = $form->addContainer('frm');

$c->addText('email', 'Your email')
	->addCondition($form::FILLED)
		->addRule($form::EMAIL, 'Please fill in a valid e-mail address.');

$c->addTextarea('message', 'Message')
	->setRequired('Please fill in a message.');

$c->addSubmit('send', 'Send');

MangoPress\formHandler($form, __FILE__, function(Form $form) {
	if ($form->isValid()){
		flashMessage('Contact form is valid. Yay');
	} else {
		flashMessage('Not valid');
	}
});

return $form;
