<?php

function addCustomColumn($post_types, $id, $title, $render)
{
	if (!is_array($post_types)) {
		$post_types = [$post_types];
	}

	foreach ($post_types as $pt) {
		add_filter('manage_'.$pt.'_posts_columns', function ($columns) use ($pt, $id, $title) {
			return array_merge($columns, [$id => $title]);
		});

		add_action('manage_pages_custom_column', function ($column, $post_id) use ($id, $render) {
			if ($column === $id) {
				call_user_func($render, $post_id, $id);
			}
		}, 10, 2);
	}
}
