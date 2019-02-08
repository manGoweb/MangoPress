<?php

namespace MangoPress;

class Components
{
	public static function expandNeonEntity($thing)
	{
		if ($thing instanceof \Nette\Neon\Entity) {
			return call_user_func_array($thing->value, $thing->attributes);
		}
		if (is_array($thing)) {
			return array_map('MangoPress\Components::expandNeonEntity', $thing);
		}
		return $thing;
	}

	public static function findAll()
	{
		$filter = $_GET['sg'] ?? null;

		$dir = realpath(__DIR__ . '/../views/components');
		$files = glob("$dir/*.latte");

		$result = [];

		foreach ($files as $file) {
			$variants = [[ 'props' => [] ]];
			$name = trim(explode($dir, $file)[1], '/');
			if ($filter && !\Nette\Utils\Strings::contains(\Nette\Utils\Strings::lower($name), \Nette\Utils\Strings::lower($filter))) {
				continue;
			}
			$propsFilepath = \Nette\Utils\Strings::replace($file, '~latte$~', 'neon');
			if (file_exists($propsFilepath)) {
				$props = \Nette\Neon\Neon::decode(file_get_contents($propsFilepath));
				if (is_array($props)) {
					$variants = [];
					foreach ($props as $variant) {
						$config = [];
						$variantProps = [];

						foreach ($variant as $key => $val) {
							if (\Nette\Utils\Strings::startsWith($key, '@')) {
								$config[trim($key, '@')] = $val;
							} else {
								$variantProps[$key] = $val;
							}
						}
						$variants[] = array_merge($config, [ 'props' => array_map('MangoPress\Components::expandNeonEntity', $variantProps) ]);
					}
				}
			}
			$result[] = [
				'path' => $file,
				'name' => $name,
				'variants' => $variants,
			];
		}

		return $result;
	}

	public static function createTemplateArgs($template, $name, $params, $locals, $context)
	{
		$name = THEME_VIEWS_DIR . '/components/' . $name . '.latte';
		return [ $name, $params + [ '_context' => $locals ], $context ];
	}
}
