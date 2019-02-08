<?php

function imageUrl($id, $size = 'full')
{
	if (is_numeric($id)) {
		$post = lazy_post($id);
		if (!$post) {
			return null;
		}

		return wp_get_attachment_image_src($post->ID, $size)[0] ?? '';
	}

	// probably is direct url
	return $id;
}
