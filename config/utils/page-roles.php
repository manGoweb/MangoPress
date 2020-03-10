<?php

function isPostPublished($id) {
	if(empty($id)) {
		return null;
	}
	return get_post_status($id) === 'publish';
}

function getPageRolesConfigPath()
{
	return get_template_directory().'/schema/page-roles.neon';
}


function getPageRolesOptionKey($lang = null)
{
	$lang = $lang ?: get_active_lang_code();
	return 'page_roles'.$lang;
}


function parsePageRolesConfig($path)
{
	return Nette\Neon\Neon::decode(file_get_contents($path));
}


function hasPageRoles()
{
	if(file_exists(getPageRolesConfigPath())) {
		$registered = Nette\Neon\Neon::decode(file_get_contents(getPageRolesConfigPath()))['register'] ?? [];
		return !!count($registered);
	}
	return false;
}


function pageRolesMetaboxPayload($post_id)
{
	$savedRoles = (array) get_option(getPageRolesOptionKey(), []);
	$registered = parsePageRolesConfig(getPageRolesConfigPath())['register'] ?? [];

	$roles = array_merge(array_map(function () { return null; }, $registered), $savedRoles);
	$titles = [];

	foreach ($savedRoles as $id) {
		if(!empty($id)) {
			$append = '';
			$published = isPostPublished($id);

			if($published === false) {
				$status = get_post_status($id);
				if (empty($status)) {
					$append = ' (deleted)';
				} else {
					$append = ' ('.$status.')';
				}
			}

			$titles[$id] = get_the_title($id) . $append;
		}
	}

	return [
		'post_id' => $post_id,
		'roles' => $roles,
		'labels' => $registered,
		'titles' => $titles,
	];
}


function getPageByRole($role, $allowRaw = false)
{
	$savedRoles = (array) get_option(getPageRolesOptionKey(), []);

	if ($allowRaw) {
		return $savedRoles[$role] ?? null;
	}

	$id = $savedRoles[$role] ?? null;
	return isPostPublished($id) ? $id : null;
}

// Callback after post is saved
function ac_savePageRoles($post_id, $vals)
{
	update_option('page_roles'.get_active_lang_code(), array_filter((array) $vals));
}
