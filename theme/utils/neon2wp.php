<?php

$filenames = [ 'post_types', 'settings', 'taxonomies', 'meta_fields' ];

if(!defined('NEON_WP_DIR')) {
	define('NEON_WP_DIR', __DIR__ . '/..');
}

add_theme_support('post-thumbnails');

$alternate_posts_per_page = array();

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
			if(isset($data['per_page'])) {
				$alternate_posts_per_page[$data['name']] = $data['per_page'];
			}
		}
		if($filename === 'taxonomies') {
			register_taxonomy($data['name'], $data['post_types'], $data);
			if(isset($data['per_page'])) {
				$alternate_posts_per_page[$data['name']] = $data['per_page'];
			}
			if(!empty($data['terms']) && is_array($data['terms'])) {
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

	if($filename === 'post_types' && !empty($res['remove'])) {
		add_action('admin_menu', function() use ($res) {
			$translate = [
			'dashboard' => 'index.php',
			'posts' => 'edit.php',
			'media' => 'upload.php',
			'pages' => 'edit.php?post_type=page',
			'comments' => 'edit-comments.php',
			'themes' => 'themes.php',
			'appearance' => 'themes.php',
			'tools' => 'tools.php',
			'users' => 'users.php',
			'settings' => 'options-general.php',
			];
			foreach($res['remove'] as $to_remove) {
				remove_menu_page(empty($translate[$to_remove]) ? $to_remove : $translate[$to_remove]);
			}
		});
	}

	if($filename === 'meta_fields') {
		$prefix = isset($res['prefix']) ? $res['prefix'] : '';
		add_filter('rwmb_meta_boxes', function($meta_boxes) use ($prefix, $register){
			foreach($register as $name => $data) {
				if(empty($data['id']) && !empty($name)) {
					$data['id'] = $name;
				}
				$data['id'] = $prefix.$data['id'];
				foreach((array) $data['fields'] as $field_name => $field) {
					if(empty($data['fields'][$field_name]['id'])) {
						$data['fields'][$field_name]['id'] = $prefix.$field_name;
					}
				}
				if(!empty($data['templates']) && (!empty($_GET['post']) || !empty($_POST['post_ID']))) {
					$post_id = !empty($_GET['post']) ? $_GET['post'] : $_POST['post_ID'];
					$template_name = basename(get_post_meta( $post_id, '_wp_page_template', true ), '.php');
					if(in_array($template_name, (array) $data['templates'])) {
						$post = get_post($post_id);
						$data['post_types'][] = $post->post_type;
					}
				}
				$meta_boxes[] = $data;
			}

			return $meta_boxes;
		});
	}

	if($filename === 'settings') {
		$prefix = isset($res['prefix']) ? $res['prefix'] : '';
		add_filter('mb_settings_pages', function($meta_boxes) use ($register, $prefix) {
			foreach($register as $name => $data) {
				if(empty($data['id']) && !empty($name)) {
					$data['id'] = $name;
				}
				if(empty($data['menu_title']) && !empty($data['title'])) {
					$data['menu_title'] = $data['title'];
				}
				if(empty($data['icon_url']) && !empty($data['menu_icon'])) {
					$data['icon_url'] = 'dashicons-'.$data['menu_icon'];
				}
				$data['id'] = $prefix.$data['id'];
				$meta_boxes[] = $data;
			}
			return $meta_boxes;
		});
	}
}

add_action('pre_get_posts', function($query) use ($alternate_posts_per_page) {
	if($query->is_main_query() && !is_admin()) {
		foreach($alternate_posts_per_page as $post_type => $per_page) {
			if($query->is_post_type_archive($post_type) || $query->is_tax($post_type)) {
				if($per_page === 'all') {
					$query->set('nopaging', true);
				} else {
					$query->set('posts_per_page', $per_page);
				}
				return;
			}
		}
	}
});
