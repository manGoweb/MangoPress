<?php declare(strict_types = 1);

$initTheme[] = function () {
	add_action('phpmailer_init', function (PHPMailer $mailer) {
		global $App;
		assert($App instanceof \Nette\DI\Container);

		if (!array_key_exists('smtp', $App->parameters)) {
			return;
		}

		$config = $App->parameters['smtp'];
		assert(array_key_exists('host', $config), 'Host key is required in SMTP config!');

		$mailer->isSMTP();
		$mailer->SMTPAuth = true;
		$mailer->SMTPSecure = $config['secure'] ?? '';
		$mailer->Host = $config['host'];
		$mailer->Port = $config['port'] ?? ($mailer->SMTPSecure === 'ssl' ? 465 : 25);
		$mailer->Username = $config['username'] ?? '';
		$mailer->Password = $config['password'] ?? '';
	});
};
