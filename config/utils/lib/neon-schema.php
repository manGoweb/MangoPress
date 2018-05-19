<?php

namespace Mangoweb;

\Tracy\Debugger::$maxDepth = 8;

use Nette\Neon\Entity;
use Nette\Neon\Neon;
use Nette\Utils\Json;
use Nette\Utils\Strings;

function getCurrentPostId() {
	$post_id = null;
	if (isset($_GET['post'])) {
		$post_id = $_GET['post'];
	} elseif (isset($_GET['post_ID'])) {
		$post_id = $_POST['post_ID'];
	}
	return $post_id;
}

function sanitizeIconKey($item, string $key = 'menu_icon')
{
	$item[$key] = $item[$key] ?? $item['icon'] ?? null;
	$item[$key] = $item[$key] ? 'dashicons-'.$item[$key] : null;

	return $item;
}

function sanitizeEvalValue($val)
{
	$val = Strings::trim($val);
	if (!Strings::startsWith($val, 'return ')) {
		$val = "return $val";
	}
	if (!Strings::endsWith($val, 'return ')) {
		$val = "$val;";
	}

	return $val;
}

function nestedEval($root, array $vars = null)
{
	if ($root instanceof Entity) {
		return call_user_func_array($root->value, nestedInterpolate($root->attributes, $vars));
	}
	if ($root instanceof \Closure) {
		return call_user_func_array($root, $vars);
	}
	if (is_array($root)) {
		if (1 === count($root) && isset($root['eval']) && is_scalar($root['eval'])) {
			return eval(sanitizeEvalValue($root['eval']));
		}
		foreach ($root as $key => $val) {
			$root[$key] = nestedEval($val, $vars);
		}
	}

	return nestedInterpolate($root, $vars);
}

function interpolateVars(string $str, array $vars)
{
	return Strings::replace($str, '~%([a-z0-9_-]+)%~', function ($str) use ($vars) {
		if (isset($vars[$str[1]])) {
			return $vars[$str[1]];
		}

		return $str[0];
	});
}

function nestedInterpolate($root, array $vars = null)
{
	if (!$vars) {
		return $root;
	}
	if (is_array($root)) {
		foreach ($root as $key => $val) {
			$root[$key] = nestedInterpolate($val, $vars);
		}
	} elseif (is_string($root)) {
		return interpolateVars($root, $vars);
	}

	return $root;
}

function getAdminMenuShortcuts()
{
	return [
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
}

function renderAdminComponent($id, $name, $data = [])
{
	static $idCounter;
	if (!$idCounter) {
		$idCounter = [];
	}

	if (empty($idCounter[$id])) {
		$idCounter[$id] = 1;
	}

	$htmlId = 'ac_'.$id.'_'.($idCounter[$id]++);

	renderLatte(__DIR__.'/views/initAdminComponent.latte', ['id' => $id, 'htmlId' => $htmlId, 'name' => $name, 'data' => $data]);
}

function getLanguagePostfix()
{
	$lang = get_active_lang_code();
	if ('all' === $lang) {
		return '';
	}

	return str_replace('{lang}', $lang, getLanguagePostfixFormat());
}

function getLanguagePostfixFormat()
{
	return '-_{lang}_';
}

function languageMetaFieldsPostfix($data)
{
	$postfix = getLanguagePostfix();
	$postfixedData = [];
	foreach ($data as $value) {
		if (isset($value['fields'])) {
			$value['fields'] = languageMetaFieldsPostfix($value['fields']);
		}
		$id = $value['id'].$postfix;
		$postfixedData[$id] = $value;
		$postfixedData[$id]['id'] = $id;
	}

	return $postfixedData;
}

abstract class NeonDef
{
	protected $dir;

	protected $defaultFilename;

	public function __construct(string $dir)
	{
		$this->dir = $dir;
	}

	public function load(string $filename = null)
	{
		$filename = $filename ?? $this->defaultFilename;
		$filepath = $this->dir.'/'.$filename;
		$raw = file_get_contents($filepath);
		if (false === $raw) {
			return $raw;
		}
		$data = Neon::decode($raw);

		return $data;
	}

	public function sanitize(array $data)
	{
		return $data;
	}

	public function flush(array $data)
	{
		return false;
	}

	public function run(string $filename = null)
	{
		$data = $this->load($filename);
		if (false === $data) {
			return false;
		}
		$data = $this->sanitize($data);
		$result = $this->flush($data);

		return $result;
	}
}

class PostTypesNeonDef extends NeonDef
{
	protected $defaultFilename = 'post-types.neon';

	public function sanitize(array $data)
	{
		$result = $data;

		$defaults = $data['defaults'] ?? [];
		$result['remove'] = $data['remove'] ?? [];
		$register = $data['register'] ?? [];

		$result['register'] = [];

		foreach ($register as $key => $item) {
			if (is_string($item)) {
				$item = ['label' => $item];
			}
			$item['name'] = $item['name'] ?? $key;
			$item = array_merge($defaults, $item);
			$result['register'][] = $this->sanitizeItem($item);
		}

		return $result;
	}

	public function sanitizeItem(array $item)
	{
		$item = sanitizeIconKey($item);

		return $item;
	}

	public function flush(array $data)
	{
		$register = $data['register'];
		foreach ($register as $post_type) {
			register_post_type($post_type['name'], $post_type);
		}

		$remove = $data['remove'];
		$translate = getAdminMenuShortcuts();
		add_action('admin_menu', function () use ($remove, $translate) {
			foreach ($remove as $to_remove) {
				remove_menu_page(empty($translate[$to_remove]) ? $to_remove : $translate[$to_remove]);
			}
		});

		return true;
	}
}

class AdminPagesNeonDef extends NeonDef
{
	protected $defaultFilename = 'admin-pages.neon';

	private $localized = [];

	public function sanitize(array $data)
	{
		$result = $data;

		$defaults = $data['defaults'] ?? [];
		$register = $data['register'] ?? [];

		$result['register'] = [];

		$parents = getAdminMenuShortcuts();

		foreach ($register as $key => $item) {
			if (is_string($item)) {
				$item = ['title' => $item];
			}

			$item['id'] = $item['id'] ?? $key;
			$item['menu_title'] = $item['menu_title'] ?? $item['title'] ?? $item['id'];

			$item['style'] = $item['style'] ?? 'no-boxes';
			$item['columns'] = $item['columns'] ?? 1;

			$item = array_merge($defaults, $item);

			if (isset($item['parent'])) {
				$item['parent'] = empty($parents[$item['parent']]) ? $item['parent'] : $parents[$item['parent']];
			}

			$result['register'][] = $this->sanitizeItem($item);
		}

		return $result;
	}

	public function sanitizeItem(array $item)
	{
		$item = sanitizeIconKey($item, 'icon_url');

		return $item;
	}

	public function flush(array $data)
	{
		$register = $data['register'] ?? [];

		$mbSettings = array_filter($register, function ($item) {
			return empty($item['component']) && empty($item['render']) && empty($item['latte']);
		});

		$componentPages = array_filter($register, function ($item) {
			return !empty($item['component']);
		});

		$renderPages = array_filter($register, function ($item) {
			return !empty($item['render']);
		});

		$lattePages = array_filter($register, function ($item) {
			return !empty($item['latte']);
		});

		foreach ($mbSettings as $page) {
			if (!empty($page['localized'])) {
				$this->addLocalizedPage($page['id']);
			}
		}

		add_filter('mb_settings_pages', function ($meta_boxes) use ($mbSettings) {
			foreach ($mbSettings as $page) {
				$page['option_name'] = $page['option_name'] ?? null;
				$meta_boxes[] = $page;
			}

			return $meta_boxes;
		});

		add_action('admin_menu', function () use ($renderPages, $lattePages) {
			foreach ($renderPages as $page) {
				$fn = function () use ($page) {
					return nestedEval($page['render']);
				};
				add_menu_page($page['title'], $page['menu_title'], $page['capability'] ?? 'manage_options', $page['menu_slug'] ?? $page['id'], $fn, $page['icon_url'], $page['position'] ?? null);
			}
			foreach ($lattePages as $page) {
				$fn = function () use ($page) {
					view($page['latte'], nestedInterpolate(array_merge(['title' => $page['title']], $page['props'])));
				};
				add_menu_page($page['title'], $page['menu_title'], $page['capability'] ?? 'manage_options', $page['menu_slug'] ?? $page['id'], $fn, $page['icon_url'], $page['position'] ?? null);
			}
		});

		add_action('admin_menu', function () use ($componentPages) {
			foreach ($componentPages as $page) {
				$render = function () use ($page) {
					$component = $page['component'];
					$data = nestedEval($page['data'] ?? $page['props'] ?? []);
					$data['component'] = ['id' => $page['id']];
					$id = $page['id'];
					renderAdminComponent($id, $component, $data);
				};

				add_menu_page($page['title'], $page['menu_title'], $page['capability'] ?? 'manage_options', $page['menu_slug'] ?? $page['id'], $render, $page['icon_url'], $page['position'] ?? null);
			}
		});

		return true;
	}

	public function addLocalizedPage($key)
	{
		$this->localized[] = $key;
	}

	public function getLocalizedPages()
	{
		return array_unique($this->localized);
	}
}

class TaxonomiesNeonDef extends NeonDef
{
	protected $defaultFilename = 'taxonomies.neon';

	public function sanitize(array $data)
	{
		$result = $data;

		$defaults = $data['defaults'] ?? [];
		$register = $data['register'] ?? [];
		$result['register'] = [];

		foreach ($register as $key => $item) {
			$item['name'] = $item['name'] ?? $key;
			$item = array_merge($defaults, $item);
			$item['post_types'] = $item['post_types'] ?? $item['post_type'] ?? [];
			if (!is_array($item['post_types'])) {
				$item['post_types'] = [$item['post_types']];
			}
			$result['register'][] = $item;
		}

		return $result;
	}

	public function flush(array $data)
	{
		$items = $data['register'];

		foreach ($items as $tax) {
			register_taxonomy($tax['name'], $tax['post_types'], $tax);
		}

		return true;
	}
}

class ThemeNeonDef extends NeonDef
{
	protected $defaultFilename = 'theme.neon';

	public function sanitize(array $data)
	{
		$result = $data;

		$result['support'] = $data['support'] ?? $data['supports'] ?? [];
		$result['hide'] = $data['hide'] ?? [];

		$result['hide']['editor'] = $data['hide']['editor'] ?? [];
		$result['hide']['thumbnail'] = $data['hide']['thumbnail'] ?? [];

		$result['hide']['editor']['templates'] = $data['hide']['editor']['templates'] ?? $data['hide']['editor']['template'] ?? [];
		$result['hide']['thumbnail']['templates'] = $data['hide']['thumbnail']['templates'] ?? $data['hide']['thumbnail']['template'] ?? [];

		$result['hide']['editor']['post_types'] = $data['hide']['editor']['post_types'] ?? $data['hide']['editor']['post_type'] ?? [];
		$result['hide']['thumbnail']['post_types'] = $data['hide']['thumbnail']['post_types'] ?? $data['hide']['thumbnail']['post_type'] ?? [];

		return $result;
	}

	public function flush(array $data)
	{
		$support = $data['support'];
		foreach ($support as $key => $item) {
			if (is_string($key)) {
				add_theme_support($key, nestedEval($item));
			} elseif (is_string($item)) {
				add_theme_support($item);
			} else {
				throw new \Exception('Invalid theme support definition');
			}
		}

		$post_id = getCurrentPostId();
		$template_name = $post_id ? str_replace('.php', '', get_post_meta($post_id, '_wp_page_template', true)) : null;
		$post_type = $post_id ? get_post_type($post_id) : ($_GET['post_type'] ?? 'post');

		add_action('admin_init', function () use ($data, $template_name, $post_type) {
			foreach ($data['hide'] as $name => $hide) {
				if ($name == 'editor') {
					if (in_array($template_name, $hide['templates'], true) || in_array($post_type, $hide['post_types'])) {
						remove_post_type_support($post_type, 'editor');
					}
				} elseif ($name == 'thumbnail') {
					if (in_array($template_name, $hide['templates'], true) || in_array($post_type, $hide['post_types'])) {
						remove_post_type_support($post_type, 'thumbnail');
					}
				}
			}
		});

		return true;
	}
}

class MetaFieldsNeonDef extends NeonDef
{
	protected $defaultFilename = 'meta-fields.neon';

	private $localizedPages = [];

	public function __construct(string $dir, array $vars = [])
	{
		parent::__construct($dir, $vars);
		$post_id = getCurrentPostId();
		$this->vars = array_merge($vars, ['post_id' => $post_id]);
	}

	public function sanitize(array $data)
	{
		$result = $data;

		$register = $data['register'] ?? [];

		$result['register'] = [];

		$mixins = $data['mixins'] ?? [];

		$mid = 1;
		foreach ($register as $key => $metabox) {
			if (!is_array($metabox)) {
				continue;
			}
			$metabox['id'] = $metabox['id'] ?? $key;
			$metabox['title'] = $metabox['title'] ?? 'Untitled metabox #' . $mid++;

			if (is_int($metabox['id'])) {
				$metabox['id'] = 'untitled_metabox_'.$metabox['id'];
			}

			foreach (['post_types' => 'post_type', 'templates' => 'template', 'not_templates' => 'not_template', 'admin_pages' => 'admin_page', 'taxonomies' => 'taxonomy'] as $plural => $singular) {
				$metabox[$plural] = $metabox[$plural] ?? $metabox[$singular] ?? null;
				if ($metabox[$plural] && !is_array($metabox[$plural])) {
					$metabox[$plural] = [$metabox[$plural]];
				}
			}

			if(!empty($metabox['mixin'])) {
				if(!empty($mixins[$metabox['mixin']])) {
					$metabox = array_merge($mixins[$metabox['mixin']], $metabox);
				} else {
					throw new \Exception('Missing NEON mixin "'.$metabox['mixin'].'" in '.$metabox['id']);
				}
			}

			$metabox['fields'] = $metabox['fields'] ?? [];
			$metabox['fields'] = $this->sanitizeFields($metabox['fields'], [], $mixins);

			$result['register'][] = $metabox;
		}

		return $result;
	}

	public function getTemplateName($post_id)
	{
		$template_name = basename(get_post_meta($post_id, '_wp_page_template', true), '.php');
		if (!$template_name && get_option('page_for_posts') === $post_id) {
			$template_name = 'home';
		}

		return $template_name;
	}

	public function sanitizeFields(array $fields, array $path, array $mixins)
	{
		$result = [];

		if(!empty($fields['mixin'])) {
			if(!empty($mixins[$fields['mixin']])) {
				$fields = array_merge($mixins[$fields['mixin']], $fields);
				unset($fields['mixin']);
			} else {
				throw new \Exception('Missing NEON mixin "'.$fields['mixin'].'" in fields.');
			}
		}

		foreach ($fields as $key => $val) {
			if (is_int($key) && is_null($val)) {
				$val = [
					'type' => 'divider',
				];
			}

			if (is_int($key) && is_string($val)) {
				$val = [
					'name' => $val,
					'type' => 'heading'
				];
			}

			if (!is_array($val)) {
				throw new \Exception('Invalid field definition for field ' . $key);
			}

			$val['id'] = $val['id'] ?? $key;

			if(!empty($val['mixin'])) {
				if(!empty($mixins[$val['mixin']])) {
					$val = array_merge($mixins[$val['mixin']], $val);
					unset($val['mixin']);
				} else {
					throw new \Exception('Missing NEON mixin "'.$val['mixin'].'" in fields.'.$val['id']);
				}
			}

			if (isset($val['type'])) {
				if ('repeater' === $val['type']) {
					$val['type'] = 'group';
					$val['clone'] = true;
					$val['sort_clone'] = true;
				} elseif ('editor' === $val['type']) {
					$val['type'] = 'wysiwyg';
				} elseif ('html' === $val['type']) {
					$val['type'] = 'custom_html';
				}
			}

			$nestPath = $path;
			if (isset($val['fields'])) {
				$nestPath[] = $val['id'];
				$val['fields'] = $this->sanitizeFields($val['fields'], $nestPath, $mixins);
			}

			if (isset($val['max'])) {
				$val['max_file_uploads'] = $val['max'];
			}

			if (isset($val['html']) && 'custom_html' === $val['type']) {
				$val['std'] = $val['html'];
			}

			foreach (['query_args', 'visible', 'hidden', 'options'] as $k) {
				if (isset($val[$k])) {
					$val[$k] = nestedInterpolate($val[$k], $this->getVars());
					$val[$k] = nestedEval($val[$k]);
				}
			}

			$result[] = $val;
		}

		return $result;
	}

	public function flush(array $data)
	{
		add_action('save_post', function ($post_id) {
			global $Req;

			$meta = $Req->getPost('ac-meta');
			$metaJson = $Req->getPost('ac-meta-json');
			$callback = $Req->getPost('ac-callback');
			$callbackJson = $Req->getPost('ac-callback-json');

			if (is_array($meta)) {
				foreach ($meta as $key => $val) {
					update_post_meta($post_id, $key, $val);
				}
			}

			if (is_array($metaJson)) {
				foreach ($metaJson as $key => $val) {
					update_post_meta($post_id, $key, Json::decode($val));
				}
			}

			if (is_array($callback)) {
				foreach ($callback as $key => $val) {
					if (Strings::startsWith($key, 'ac_')) {
						call_user_func_array($key, [$post_id, $val]);
					}
				}
			}

			if (is_array($callbackJson)) {
				foreach ($callbackJson as $key => $val) {
					if (Strings::startsWith($key, 'ac_')) {
						call_user_func_array($key, [$post_id, Json::decode($val)]);
					}
				}
			}
		});

		add_filter('rwmb_meta_boxes', function ($meta_boxes) use ($data) {
			$register = $data['register'];
			foreach ($register as $metabox) {
				if (!empty($metabox['templates']) && getCurrentPostId()) {
					$post_id = getCurrentPostId();
					$template_name = $this->getTemplateName($post_id);
					if (in_array($template_name, $metabox['templates'])) {
						$post = get_post($post_id);
						$metabox['post_types'] = $metabox['post_types'] ?? [];
						$metabox['post_types'][] = $post->post_type;
						$metabox['post_types'] = array_unique($metabox['post_types']);
					}
					unset($metabox['templates']);
				}
				if (!empty($metabox['not_templates']) && getCurrentPostId()) {
					$post_id = getCurrentPostId();
					$template_name = $this->getTemplateName($post_id);
					if (in_array($template_name, $metabox['not_templates'])) {
						continue;
					}
				}
				if (!empty($metabox['if'])) {
					$post_id = getCurrentPostId();
					if (!nestedEval($metabox['if'], $this->getVars(['post_id' => $post_id]))) {
						continue;
					}
				}
				if (isset($metabox['component'])) {
					$fn = function ($post) use ($metabox) {
						$component = $metabox['component'];
						$post_id = $post ? $post->ID : null;
						$vars = $this->getVars(['post_id' => $post_id, 'name' => $metabox['id']]);
						if (!empty($metabox['data'])) {
							$data = nestedEval($metabox['data'] ?? $metabox['props'] ?? [], $vars);
						} else {
							$name = $metabox['name'] ?? $metabox['id'];
							$data = [
								'name' => $name,
								'value' => get_post_meta($post_id, $name, true),
							];
						}

						$data['post_id'] = $post_id;

						$data['metabox'] = ['id' => $metabox['id'], 'wrapper' => '#metabox-'.$metabox['id']];

						$id = $metabox['id'];

						renderAdminComponent($id, $component, $data);
					};

					$id = $metabox['id'];
					$title = $metabox['title'];
					$position = $metabox['context'] ?? 'normal';
					$priority = $metabox['priority'] ?? 'high';
					$post_types = $metabox['post_types'];

					add_action('add_meta_boxes', function () use ($id, $post_types, $title, $position, $priority, $fn) {
						$post_types = (array) $post_types;
						foreach ($post_types as $cpt) {
							add_meta_box(
								'metabox-'.$id,
								$title,
								$fn,
								$cpt,
								$position,
								$priority
							);
						}
					});

					continue;
				}
				if (isset($metabox['render']) || isset($metabox['latte'])) {
					if (isset($metabox['latte'])) {
						$view = $metabox['latte'];

						$fn = function ($post) use ($view, $metabox) {
							$props = $metabox['props'] ?? [];

							return view($view, $this->getVars(array_merge($props, ['post_id' => $post ? $post->ID : null, 'name' => $metabox['id']])));
						};
					} elseif (isset($metabox['render'])) {
						$fn = function ($post) use ($metabox) {
							$props = $metabox['props'] ?? [];

							return nestedEval($metabox['render'], $this->getVars(array_merge($props, ['post_id' => $post ? $post->ID : null, 'name' => $metabox['id']])));
						};
					}

					$title = $metabox['title'];
					$position = $metabox['context'] ?? 'normal';
					$priority = $metabox['priority'] ?? 'high';
					$post_types = $metabox['post_types'];

					add_action('add_meta_boxes', function () use ($post_types, $title, $position, $priority, $fn) {
						$post_types = (array) $post_types;
						foreach ($post_types as $cpt) {
							add_meta_box(
								md5((string) microtime()),
								$title,
								$fn,
								$cpt,
								$position,
								$priority
							);
						}
					});

					continue;
				}
				if (!empty($metabox['admin_pages'])) {
					$metabox['settings_pages'] = $metabox['admin_pages'];
					unset($metabox['admin_pages']);

					if (!empty($_GET['page']) && in_array($_GET['page'], $this->getLocalizedPages(), true)) {
						$metabox['fields'] = languageMetaFieldsPostfix($metabox['fields']);
						$metabox['fields']['postfix'] = [
							'id' => 'postfix-format',
							'type' => 'hidden',
							'std' => getLanguagePostfixFormat(),
						];
					}

					$meta_boxes[] = $metabox;
					continue;
				}
				$meta_boxes[] = $metabox;
			}

			return $meta_boxes;
		});

		return true;
	}

	protected function getVars(array $vars = null)
	{
		if ($vars) {
			return array_merge($this->vars, $vars);
		}

		return $this->vars;
	}

	public function setLocalizedPages(array $localizedPages)
	{
		$this->localizedPages = $localizedPages;
	}

	public function getLocalizedPages()
	{
		return $this->localizedPages;
	}
}

class HideEditorNeonDef extends NeonDef
{
	protected $defaultFilename = 'hide-editor.neon';

	public function sanitize(array $data)
	{
		$data['hide']['editor']['templates'] = $data['hide']['editor']['templates'] ?? [];
		$data['hide']['thumbnail']['templates'] = $data['hide']['thumbnail']['templates'] ?? [];

		return $data;
	}

	public function flush(array $data)
	{
		$post_id = null;
		if (isset($_GET['post'])) {
			$post_id = $_GET['post'];
		} elseif (isset($_GET['post_ID'])) {
			$post_id = $_POST['post_ID'];
		}
		if ($post_id !== null) {
			$template_file = str_replace('.php', '', get_post_meta($post_id, '_wp_page_template', true));

			add_action('admin_init', function () use ($data, $template_file) {
				foreach ($data['hide'] as $name => $data) {
					if ($name == 'editor') {
						if (in_array($template_file, $data['templates'])) {
							remove_post_type_support('page', 'editor');
						}
					} elseif ($name == 'thumbnail') {
						if (in_array($template_file, $data['templates'])) {
							remove_post_type_support('page', 'thumbnail');
						}
					}
				}
			});
		}

		return true;
	}
}

function runNeonConfigs($dir)
{
	\Tracy\Debugger::timer('a');
	$post_types = new PostTypesNeonDef($dir);
	$post_types->run();

	$admin_pages = new AdminPagesNeonDef($dir);
	$admin_pages->run();

	$taxonomies = new TaxonomiesNeonDef($dir);
	$taxonomies->run();

	$theme = new ThemeNeonDef($dir);
	$theme->run();

    $editors = new HideEditorNeonDef($dir);
    $editors->run();

	if (is_user_logged_in() && is_admin()) {
		$meta_fields = new MetaFieldsNeonDef($dir);
		$meta_fields->setLocalizedPages($admin_pages->getLocalizedPages());
		$meta_fields->run();
	}
}
