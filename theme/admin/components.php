<?php

use Nette\Utils\Json;

require_once __DIR__ . '/addAdminComponent.php';

$roles = get_page_roles_pairs();

if(!empty($roles) && current_user_can('manage_options')) {
	$props = [ 'roles' => $roles, 'titles' => array_map('get_the_title', $roles), 'nopadding' => TRUE ];
	addAdminComponent('page', 'Page roles', 'page_roles', $props, 'side', 'high');
}

function adminComponentCallback_savePageRoles($post_id, $val) {
	$val = Json::decode($val, Json::FORCE_ARRAY);
	foreach($val as $key => $post_id) {
		set_role_page($key, $post_id);
	}
}
