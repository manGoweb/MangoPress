<?php

function addAdminComponent($post_type, $title, $name, $data = [], $position = 'side', $priority = 'high', $dataTransformer = NULL) {
	global $Url;
	if(!$data) {
		$data = [];
	}
	if(!is_array($post_type)) {
		$post_type = [ $post_type ];
	}
	$id = 'adminComponent'.md5((string) microtime());
	$fn = function($post) use ($name, $data, $dataTransformer, $id) {
		$data['post_id'] = $post->ID;
		if($dataTransformer) {
			$data = $dataTransformer($post->ID, $data);
		}
		$component = [
			'name' => $name,
			'data' => $data,
			'place' => '#' . $id,
		];

		view('../admin/adminComponentPlaceholder', [
			'id' => $id,
			'component' => $component,
		]);
	};
	add_action('add_meta_boxes', function() use ($post_type, $title, $name, $data, $position, $priority, $fn) {
		foreach($post_type as $cpt) {
			add_meta_box(
				(string) microtime(),
				$title,
				$fn,
				$cpt,
				$position,
				$priority
			);
		}
	});
}

function addAdminComponentMeta($post_type, $title, $name, $meta_key, $data = [], $position = 'side', $priority = 'high') {
	return addAdminComponent($post_type, $title, $name, $data, $position, $priority, function($post_id, $data) use ($meta_key) {
		$data['meta'] = [
			'key' => $meta_key,
			'value' => get_post_meta($post_id, $meta_key, TRUE),
		];
		return $data;
	});
}

add_action('save_post', function($post_id) {
	global $Req;

	$meta = $Req->getPost('adminComponentSaveMeta');
	if($meta) foreach($meta as $key => $val) {
		update_post_meta($post_id, $key, $val);
	}

	$meta = $Req->getPost('adminComponentSaveMetaJson');
	if($meta) foreach($meta as $key => $val) {
		update_post_meta($post_id, $key, Json::decode($val, Json::FORCE_ARRAY));
	}

	$callback = $Req->getPost('adminComponentCallback');
	if($callback) foreach($callback as $key => $val) {
		call_user_func('adminComponentCallback_'.$key, $post_id, $val);
	}
});
