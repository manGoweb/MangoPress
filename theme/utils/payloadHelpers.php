<?php

function getPostTitle($id) {
	return Nette\Utils\Strings::trim(html_entity_decode(get_the_title($id), ENT_COMPAT, 'UTF-8'));
}

function getPostContent($id) {
	return Nette\Utils\Strings::trim(apply_filters('the_content', get_post_field('post_content', $id)));
}

function getTemplateName($post_id)
{
	$template_name = basename(get_post_meta($post_id, '_wp_page_template', true), '.php');
	if (!$template_name && get_option('page_for_posts') === $post_id) {
		$template_name = 'home';
	}

	return $template_name;
}

function getThumbnail($id) {
	$thumbnailId = get_post_thumbnail_id($id);
	return payloadSanitize_image($thumbnailId, []);
}


function countImgproxyImageWidth($image, $width, $height, $crop) {
	$hr =  $image[1] / $width;
	$vr = $image[2] / $height;
	$scale = 1;
	if($hr < $vr) {
		$scale = $image[2] / $image[1];
	}
	return (int)ceil($crop ? $width : $width / $scale);
}

function countImgproxyImageHeight($image, $width, $height, $crop) {
	$hr =  $image[1] / $width;
	$vr = $image[2] / $height;
	$scale = 1;
	if($vr < $hr) {
		$scale = $image[1] / $image[2];
	}
	return (int)ceil($crop ? $height : $height / $scale);
}

function payloadSanitize_wysiwyg($val, $field = null) {
	if(empty($val)) {
		return null;
	}
	return apply_filters('the_contents', $val);
}

	function payloadSanitize_image($id, $field = null) {
		if(empty($id)) {
			return null;
		}

	if(is_array($id)) {
		return array_map(function($id) use ($field) { return payloadSanitize_image($id, $field); }, $id);
	}
	$image = wp_get_attachment_image_src($id, 'full');

	if (!$image) {
		return null;
	}

	return [
		'url' => $image[0],
		'width' => $image[1],
		'height' => $image[2],
		'cropped' => $image[3],
		'sizes' => array_map(function($size) use ($image) {
				$width = $size;
				$height = $size;
				$crop = false;
				if(is_string($size)) {
					$crop = Nette\Utils\Strings::endsWith($size, 'c');
					$size = Nette\Utils\Strings::trim($size, 'c');
					$parts = explode('x', $size);
					$width = +$parts[0];
					$height = +($parts[1] ?? $width);
				}
				return [ 'url' => imgproxy_url($image[0], $width, $height, $crop), 'width' => countImgproxyImageWidth($image, $width, $height, $crop), 'height' => countImgproxyImageHeight($image, $width, $height, $crop), 'cropped' => $crop ];
			},
			$field['sizes'] ?? [ 240, 320, 400, 480, 540, 640, 768, 1024, 1280, 1440, 1920, 2560 ]
		),
	];
}

function sanitizeSingleGroup($item, $fields) {
	$result = [];

	foreach($fields as $field) {
		$payload = $field['payload'] ?? [];
		$type = $field['type'] ?? null;
		$currentValue = $item[$field['id']] ?? NULL;
		$customized = false;

		if (($field['payload'] ?? null) === 'ignore' || !empty($payload['ignore'])) {
			continue;
		}

		if (!empty($payload['get'])) {
			$customized = true;
			$currentValue = pipeCall($item, [ $payload['get'] ]);
		}

		if (!empty($payload['pipe'])) {
			$customized = true;
			$currentValue = pipeCall($currentValue, [ $payload['pipe'] ]);
		}

		if (!$customized && function_exists('payloadGroupGetter_'.$type)) {
			$customized = true;
			$currentValue = pipeCall($item, [ [ 'payloadGroupGetter_'.$type, '_', $field ] ]);
		}

		if (!$customized && function_exists('payloadSanitize_'.$type)) {
			$customized = true;
			$currentValue = pipeCall($currentValue, [ [ 'payloadSanitize_'.$type, '_', $field ] ]);
		}

		$to = $payload['key'] ?? $field['id'];
		$result[$to] = $currentValue;
	}

	return $result;
}

function payloadSanitize_group($group, $field = null) {
	if(empty($group)) {
		return null;
	}

	$result = $group;

	$isList = $field['clone'] ?? false;

	if ($isList) {
		$result = array_map(function($item) use ($field) {
			return sanitizeSingleGroup($item, $field['fields']);
		}, $result);
	} else {
		$result = sanitizeSingleGroup($result, $field['fields']);
	}

	return $result;
}

function enhanceSchema($original, $id, $post_type) {
	global $NeonConfigs;

	$result = $original;

	$registered = $NeonConfigs['meta_fields']->getLast()['register'] ?? [];

	$template_name = getTemplateName($id);

	foreach($registered as $metabox) {

		if (in_array($template_name, $metabox['not_templates'] ?? [], true)) {
			continue;
		}

		if (empty($metabox['fields'])) {
			continue;
		}

		$allowed = false;

		if (in_array($post_type, $metabox['post_types'] ?? [], true)) {
			$allowed = true;
		}

		if (!$allowed) {
			continue;
		}

		foreach($metabox['fields'] as $field) {
			$payload = $field['payload'] ?? [];
			$from = $field['id'];
			$type = $field['type'] ?? null;
			$customized = false;

			if (!empty($result[$from])) {
				continue;
			}

			if (($field['payload'] ?? null) === 'ignore' || !empty($payload['ignore'])) {
				continue;
			}

			if (!empty($payload['get'])) {
				$customized = true;
				$from = [ $payload['get'] ];
			}

			if (!empty($payload['pipe'])) {
				$customized = true;
				$from = [ [ 'meta', '_', $field['id'] ], $payload['pipe'] ];
			}

			if (!$customized && function_exists('payloadGetter_'.$type)) {
				$customized = true;
				$from = [ [ 'payloadGetter_'.$type, '_', $field ] ];
			}

			if (!$customized && function_exists('payloadSanitize_'.$type)) {
				$customized = true;
				$from = [ [ 'meta', '_', $field['id'] ], [ 'payloadSanitize_'.$type, '_', $field ] ];
			}

			$to = $payload['key'] ?? $field['id'];
			$result[$to] = $from;
		}

	}

	return $result;
}

function pipeCall($id, $pipeline = null) {
	if(is_string($pipeline)) {
		return meta($id, $pipeline);
	}

	if (is_array($pipeline)) {
		$last = $id;
		foreach($pipeline as $pipeItem) {
			if (is_string($pipeItem)) {
				$last = call_user_func($pipeItem, $last);
			} else if (is_array($pipeItem)) {
				$args = array_slice($pipeItem, 1, count($pipeItem) - 1);
				$addedLast = false;
				foreach($args as $key => $val) {
					if ($val === '_') {
						$args[$key] = $last;
						$addedLast = true;
					}
				}
				if(!$addedLast) {
					$args[] = $last;
				}
				$last = call_user_func_array($pipeItem[0], array_values($args));
			} else {
				throw new \Exception('Unknown pipe item');
			}
		}
		return $last;
	}

	return $pipeline;
}


function getKnownPostFields() {
	return [
		'ID',
		'post_author',
		'post_date',
		'post_date_gmt',
		'post_content' => 'getPostContent',
		'post_content_filtered',
		'post_title' => 'getPostTitle',
		'post_excerpt',
		'post_status',
		'post_type',
		'comment_status',
		'ping_status',
		'post_password',
		'post_name',
		'to_ping',
		'pinged',
		'post_modified',
		'post_modified_gmt',
		'post_parent',
		'menu_order',
		'post_mime_type',
		'guid',
		'post_category',
		'permalink' => 'get_the_permalink',
		'template' => 'getTemplateName',
		'thumbnail' => 'getThumbnail',
	];
}

function generateEntitiesPayload($schemas) {
	$postTypes = [];

	foreach($schemas as $key => $val) {
		$name = is_string($val) ? $val : $key;
		if(is_string($name) && $name !== 'default') {
			$postTypes[] = $name;
		}
	}

	$entities = array_map(function($post_type) use ($schemas) {

		$fetchSchema = $schemas[$post_type] ?? $schemas['default'] ?? [];

		$ids = (new WP_Query([
			'post_type' => $post_type,
			'nopaging' => true,
			'fields' => 'ids',
		]))->posts;

		$entities = array_map(function($id) use ($post_type, $fetchSchema) {
			$knownPostFields = getKnownPostFields();

			$result = [
				'id' => (string) $id,
			];

			$schema = enhanceSchema($fetchSchema, $id, $post_type);

			foreach($schema as $key => $val) {
				$from = $val;
				$to = is_string($key) ? $key : $val;

				$newValSet = false;
				$newVal = null;

				if(is_string($from)) {
					if (!$newValSet && !empty($knownPostFields[$from])) {
						$newValSet = true;
						$newVal = call_user_func($knownPostFields[$from], $id);
					}

					if(!$newValSet) {
						$index = array_search($from, $knownPostFields, true);
						if ($index !== false) {
							$newValSet = true;
							$newVal = get_post_field($knownPostFields[$index], $id);
						}
					}
				}

				if(!$newValSet && $from instanceof \Closure) {
					$newValSet = true;
					$newVal = call_user_func($from, $id);
				}

				if(!$newValSet) {
					$newValSet = true;
					$newVal = pipeCall($id, $from);
				}

				$result[$to] = $newVal;
			}

			return $result;
		}, $ids);

		return $entities;

	}, array_combine($postTypes, $postTypes));

	return $entities;
}
