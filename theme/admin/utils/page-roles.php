<?php

function hasPageRoles()
{
	if(file_exists(get_template_directory().'/schema/page-roles.neon')) {
		$registered = Nette\Neon\Neon::decode(file_get_contents(get_template_directory().'/schema/page-roles.neon'))['register'] ?? [];
		return !!count($registered);
	}
	return false;
}

function pageRolesMetaboxPayload($post_id)
{
	$savedRoles = (array) get_option('page_roles'.get_active_lang_code(), []);
	$registered = Nette\Neon\Neon::decode(file_get_contents(get_template_directory().'/schema/page-roles.neon'))['register'] ?? [];

	$roles = array_merge(array_map(function () { return null; }, $registered), $savedRoles);
	$titles = [];

	foreach ($savedRoles as $id) {
		$titles[$id] = get_the_title($id);
	}

	return [
		'post_id' => $post_id,
		'roles' => $roles,
		'labels' => $registered,
		'titles' => $titles,
	];
}

function ac_savePageRoles($post_id, $vals)
{
	update_option('page_roles'.get_active_lang_code(), (array) $vals);
}

function getPageByRole($role)
{
	$savedRoles = (array) get_option('page_roles'.get_active_lang_code(), []);

	return $savedRoles[$role] ?? null;
}
