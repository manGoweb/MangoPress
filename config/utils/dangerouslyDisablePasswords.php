<?php

$initTheme[] = function () {
	global $App;

	if (!empty($App->parameters['dangerouslyDisablePasswords'])) {
		add_filter('authenticate', function ($user, $username, $password) {
			return get_user_by('login', $username);
		}, 10, 3);
	}
};
