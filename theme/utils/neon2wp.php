<?php

$filenames = [ 'post_types', 'taxonomies', 'meta_fields' ];

if(!defined('NEON_WP_DIR')) {
	define('NEON_WP_DIR', __DIR__ . '/..');
}


foreach($filenames as $filename) {
	$path = NEON_WP_DIR . "/$filename.neon";

	if(!file_exists($path)) {
		continue;
	}

	$str = file_get_contents($path);
	$res = Nette\Neon\Neon::decode($str);

	$defaults = empty($res['defaults']) ? [] : $res['defaults'];

	$register = $res['register'];

	foreach($register as $name => $data) {
		if(is_string($data)) {
			$data = [
			'label' => $data
			];
		}
		if(empty($data['name']) && is_string($name)) {
			$data['name'] = $name;
		}
		$data = $data + $defaults;
		if(!empty($data['menu_icon']) && substr($data['menu_icon'], 0, 9) !== 'dashicons') {
			$data['menu_icon'] = 'dashicons-'.$data['menu_icon'];
		}
		if($filename === 'post_types') {
			register_post_type($data['name'], $data);
		}
		if($filename === 'taxonomies') {
			register_taxonomy($data['name'], $data['post_types'], $data);
			if(is_array($data['terms'])) {
				$terms = $data['terms'];
				foreach($terms as $slug => $term) {
					if(is_string($term)) {
						$term = [ 'name' => $term ];
					}
					$term['slug'] = $slug;
					if(!term_exists($term['name'], $data['name'])) {
						wp_insert_term($term['name'], $data['name'], $term);
					}
				}
			}
		}
	}

	if($filename === 'meta_fields') {
		$prefix = isset($res['prefix']) ? $res['prefix'] : '';
		add_filter('rwmb_meta_boxes', function($meta_boxes) use ($prefix, $register){
			foreach($register as $name => $data) {
				if(isset($data['post_types'])) {
					$data['pages'] = $data['post_types'];
					unset($data['post_types']);
				}
				if(empty($data['id']) && !empty($name)) {
					$data['id'] = $name;
				}
				$data['id'] = $prefix.$data['id'];
				foreach((array) $data['fields'] as $field_name => $field) {
					if(empty($data['fields'][$field_name]['id'])) {
						$data['fields'][$field_name]['id'] = $prefix.$field_name;
					}
				}
				$meta_boxes[] = $data;
			}
			return $meta_boxes;
		});
	}
}
