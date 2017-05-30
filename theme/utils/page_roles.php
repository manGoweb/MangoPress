<?php

function set_role_page($key, $id) {
	update_option("role_page_" . $key, $id);
}

function get_role_page($key) {
	$id = get_option("role_page_" . $key, NULL);
	if(!$id) {
		return NULL;
	}
	return icl_object_id($id, 'page', FALSE);
}

function get_page_roles_pairs() {
	global $App;

	$pageRoles = $App->parameters['pageRoles'] ?? [];

	$roles = [];

	foreach($pageRoles as $role) {
		$roles[$role] = get_role_page($role);
	}

	return $roles;
}
