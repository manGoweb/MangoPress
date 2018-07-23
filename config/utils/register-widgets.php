<?php

$initTheme[] = function ($dir) {
	$widgetsDir = $dir.'/admin/widgets';

	add_action('wp_dashboard_setup', function ($toolbar) use ($widgetsDir) {
		foreach (glob("$widgetsDir/*.php") as $filepath) {
			$result = require_once $filepath;

			if (empty($result) || is_bool($result)) {
				continue;
			}

			if (!empty($result['render'])) {
				wp_add_dashboard_widget(
								basename($filepath, '.php'),
								$result['name'] ?? $result['title'] ?? '',
								$result['render'],
								$result['renderControl'] ?? null
							);
			}

			if (!empty($result['latte'])) {
				wp_add_dashboard_widget(
					basename($filepath, '.php'),
					$result['name'] ?? $result['title'] ?? '',
					function () use ($result) {
						view($result['latte'], $result['data'] ?? $result['props'] ?? []);
					},
					empty($result['latteControl']) ? null : function () use ($result) {
						view($result['latteControl'], $result['data'] ?? $result['props'] ?? []);
					}
				);
			}

			if (!empty($result['component'])) {
				wp_add_dashboard_widget(
					basename($filepath, '.php'),
					$result['name'] ?? $result['title'] ?? '',
					function () use ($filepath, $result) {
						Mangoweb\renderAdminComponent(basename($filepath, '.php'), $result['component'], $result['data'] ?? $result['props'] ?? []);
					},
					empty($result['componentControl']) ? null : function () use ($filepath, $result) {
						Mangoweb\renderAdminComponent(basename($filepath, '.php'), $result['componentControl'], $result['data'] ?? $result['props'] ?? []);
					}
				);
			}
		}
	});
};
