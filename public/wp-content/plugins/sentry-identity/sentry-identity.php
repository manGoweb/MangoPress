<?php

/*
Plugin Name: Sentry Identity
Description: Add Wordpress identity to sentry logs
Author: Mikulas Dite
Version: 1.0
Author URI: https://www.mangoweb.cz
*/

use Nette\DI\Container;

add_action( 'plugins_loaded', 'sentry_identity_init' );

function sentry_identity_init() {
	/** @var Container $App */
	global $App;

	/** @var Raven_Client $sentryClient */
	try {
		$sentryClient = $App->getService('sentry.client');

		$user = wp_get_current_user();
		$sentryClient->set_user_data($user->ID, $user->user_email, [
			'Name' => $user->display_name,
			'Registered' => $user->user_registered,
		]);

	} catch (\Nette\DI\MissingServiceException $e) {
		// sentry not initialized, ignore
	}
}
