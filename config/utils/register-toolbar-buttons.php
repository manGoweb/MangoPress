<?php

$initTheme[] = function ($dir) {
	$toolbarButtonsDir = $dir.'/admin/toolbar-buttons';

	add_action('admin_bar_menu', function ($toolbar) use ($toolbarButtonsDir) {
		$register = [];
		foreach (glob("$toolbarButtonsDir/*.php") as $filepath) {
			$result = require_once $filepath;
			if (empty($result) || is_bool($result)) {
				continue;
			}
			if ($result instanceof \Closure) {
				$result = $result();
			}
			if (empty($result['id'])) {
				$result['id'] = basename($filepath, '.php');
			}
			if (!empty($result['component'])) {
				$r = [
					'id' => $result['id'],
					'title' => $result['title'] ?? '',
					'meta' => [
						'html' => viewString('utils/initComponent', array_merge($result['component'], ['handler' => 'initAdminComponents', 'place' => '#wp-admin-bar-'.$result['id']])),
					],
				];
				$result = $r;
			}
			$register[] = $result;
		}
		foreach ($register as $args) {
			$toolbar->add_node($args);
		}
	}, 100);
};
