<?php

define('NBSP', "\xC2\xA0");


function expandTypeShortcuts($typeString)
{
	$shortcuts = [
		'boolean' => 'bool',
		'[]' => 'iterable',
		'num' => ['int', 'float'],
		'number' => ['int', 'float'],
		'double' => 'float',
		'real' => 'float',
		'HTMLContent' => ['string', 'Nette\Utils\IHtmlString', 'Latte\Runtime\IHtmlString']
	];

	if (!empty($shortcuts[$typeString])) {
		return $shortcuts[$typeString];
	}

	return $typeString;
}

function normalizeType($typeString)
{
	return trim($typeString);
}

function getNativePhpTypes()
{
	return [ 'int', 'float', 'string', 'bool', 'mixed', 'resource', 'array', 'object', 'null', 'iterable' ];
}

function isInType($value, $types)
{
	$phpTypes = getNativePhpTypes();
	foreach ($types as $type) {
		if (in_array($type, $phpTypes, true) === true) {
			if ($type === 'mixed') {
				return true;
			}
			if (call_user_func('is_'.$type, $value)) {
				return true;
			}
		} elseif (Nette\Utils\Strings::startsWith($type, '"')) {
			if ($value === trim($type, '"')) {
				return true;
			}
		} elseif (Nette\Utils\Strings::startsWith($type, '\'')) {
			if ($value === trim($type, '\'')) {
				return true;
			}
		} elseif (Nette\Utils\Strings::startsWith($type, '@')) {
			if (call_user_func(trim($type, '@'), $value)) {
				return true;
			}
		} else {
			if (is_a($value, $type, true) || is_subclass_of($value, $type, true)) {
				return true;
			}
		}
	}
	return false;
}

function compareTypeNames($a, $b)
{
	if ($a === 'null') {
		return 1;
	}
	if ($b === 'null') {
		return -1;
	}
	if (Nette\Utils\Strings::startsWith($a, '@')) {
		return -1;
	}
	if (Nette\Utils\Strings::startsWith($b, '@')) {
		return 1;
	}
	return strcasecmp($a, $b);
}

class InvalidLattePropException extends \Exception
{
	public $template;

	public function __construct(string $message, $template)
	{
		parent::__construct($message);
		$this->template = $template;
	}
}

	Tracy\Debugger::getBlueScreen()->addPanel(function ($e) { // catched exception
		if ($e instanceof InvalidLattePropException && !empty($e->template)) {
			$stack = buildTemplateReferenceString($e->template);
			return [
				'tab' => 'Template Stack',
				'panel' => '<ul>'.implode('', array_map(function ($item) {
					return '<li>'.$item.'</li>';
				}, array_map('htmlspecialchars', $stack))).'</ul>',
			];
		}
	});

function buildTemplateReferenceString($template)
{
	$items = [];

	$t = $template;

	while ($t) {
		$name = Nette\Utils\Strings::replace($t->getName(), '~^'.preg_quote(THEME_VIEWS_DIR, '~').'~', '');
		$type = $t->getReferenceType();
		$parent = $t->getParentName();
		$items[] = $type ? "$type: $name" : "$name $parent";
		$t = $t->getReferringTemplate();
	}

	foreach ($items as $k => $item) {
		$items[$k] = count($items) - $k . ' ' . $item;
	}

	return $items;
}

function runtimeCheckLatteDeclaration($template, string $serializedDeclaration, bool $assert = false, $runtimeValue = null)
{
	global $latteDeclarations;
	$templateName = $template->getName();
	$latteDeclarations = $latteDeclarations ?? [];
	$latteDeclarations[$templateName] = $latteDeclarations[$templateName] ?? [];

	$declaration = unserialize($serializedDeclaration);

	$templatePath = Nette\Utils\Strings::replace($templateName, '~^'.preg_quote(THEME_VIEWS_DIR, '~').'~', '');

	if ($assert) {
		$actualType = gettype($runtimeValue);
		$actualValue = $actualType === 'NULL' ? '' : ' (' . print_r($runtimeValue, true) . ')';
		if (!isInType($runtimeValue, $declaration['types'])) {
			$typeString = implode(' | ', $declaration['types']);
			$varName = $declaration['varName'];
			throw new InvalidLattePropException("'$templatePath' prop '\$$varName' is '$actualType$actualValue' but must be of type '$typeString'.", $template);
		}
	}

	$latteDeclarations[$templateName][$declaration['varName']] = $declaration;
}

function globallyDeclare(string $templateName, array $declaration, bool $assert = false)
{
	global $latteDeclarations;
	$latteDeclarations = $latteDeclarations ?? [];
	$latteDeclarations[$templateName] = $latteDeclarations[$templateName] ?? [];

	$cleanDeclaration = [];

	foreach ($declaration as $var => $meta) {
		$types = array_unique(array_filter(array_map('normalizeType', explode('|', $meta['type']))));
		usort($types, 'compareTypenames');

		if ($assert) {
			assert(isInType($meta['runtimeValue'], $types), 'Latte: `$'.$var.'` must be of type `'.implode(' | ', $types).'`. Actually is `' . gettype($meta['runtimeValue']). '(' . print_r($meta['runtimeValue'], true) . ')`.');
		}

		$cleanDeclaration[$var] = [
			'default' => $meta['default'] ?? null,
			'defaultValue' => $meta['defaultValue'] ?? null,
			'types' => $types,
			'nullable' => in_array('null', $types),
			'comment' => $meta['comment'] ?? null,
		];
	}

	$latteDeclarations[$templateName] = array_merge($latteDeclarations[$templateName], $cleanDeclaration);
}

global $_DURING_DECLARATION;
$_DURING_DECLARATION = false;

function getGloballyDeclared(string $templateName)
{
	global $latteDeclarations;
	global $_DURING_DECLARATION;
	$_DURING_DECLARATION = true;
	$latteDeclarations = $latteDeclarations ?? [];

	$result = $latteDeclarations[$templateName] ?? null;

	if (empty($result)) {
		try {
			@renderLatteToString($templateName);
		} catch (\Exception $e) {
			bdump($e);
		}
	}

	$_DURING_DECLARATION = false;
	return $latteDeclarations[$templateName] ?? [];
}

$initTheme[] = function ($dir) {
	define('THEME_VIEWS_DIR', $dir . '/views');
	MangoPressTemplating::init();

	MangoMacros::$set['declare'] = function (Latte\MacroNode $node, Latte\PhpWriter $writer) {
		$str = $node->args . $node->modifiers;

		$match = Nette\Utils\Strings::match($str, "~^([^\\$]*\\s+)?(\\$[a-z0-9]+)\\s*((\\??=)\\s*(.*))?$~ism");

		$nullable = false;

		$types = array_values(array_filter(array_map('trim', explode('|', $match[1] ?? ''))));
		$types = array_map('normalizeType', $types);
		$originalTypes = $types;
		$types = flattenArray(array_map('expandTypeShortcuts', $types));

		$value = trim($match[5] ?? '');
		$right = new Latte\MacroTokens($value);

		$valueCode = $writer->quotingPass($right)->joinAll();

		$varName = trim(trim($match[2] ?? ''), '$');

		$valueCode = $valueCode === '' ? 'null' : $valueCode;

		$nullishValue = Nette\Utils\Strings::match($valueCode, '~^(null)([^a-z0-9_-]+.*)?$~ism');


		if (!empty($value) && !empty($nullishValue) && normalizeType($nullishValue[1]) === 'null') {
			$types[] = 'null';
			$originalTypes[] = 'null';
		}

		$types = array_values(array_unique($types));
		$originalTypes = array_values(array_unique($originalTypes));
		usort($types, 'compareTypenames');
		usort($originalTypes, 'compareTypenames');

		$nullable = in_array('null', $types);

		$originalTypes = array_combine($originalTypes, $originalTypes);
		$originalTypes = array_map('expandTypeShortcuts', $originalTypes);

		$declaration = [
			'types' => $types,
			'originalTypes' => $originalTypes,
			'varName' => $varName,
			'operator' => trim($match[4] ?? ''),
			'defaultValueString' => $value,
			'defaultValue' => $valueCode,
			'comment' => trim(Nette\Utils\Strings::match($match[5] ?? '', '~([^(//)]*)//(.*)~ism')[2] ?? '') ?: null,
			'original' => $str,
			'nullable' => $nullable,
		];

		return $writer->write(
			'/* line ' . $node->startLine . ' */
			extract([ \''.$varName.'\' =>
			'.$valueCode.'
			], EXTR_SKIP);
			MangoPress\Components::declaration($this, ' . var_export($str, true) . ', '.var_export($declaration, true).', $'.$varName.', '.$node->startLine.')'
		);
	};

	MangoMacros::$set['component'] = function (Latte\MacroNode $node, Latte\PhpWriter $writer) {
		$node->replaced = false;
		$noEscape = Latte\Helpers::removeFilter($node->modifiers, 'noescape');
		if (!$noEscape && Latte\Helpers::removeFilter($node->modifiers, 'escape')) {
			trigger_error('Macro {component} provides auto-escaping, remove |escape.');
		}
		if ($node->modifiers && !$noEscape) {
			$node->modifiers .= '|escape';
		}
		return $writer->write(
			'/* line ' . $node->startLine . ' */
			call_user_func_array([$this, "createTemplate"], \MangoPress\Components::createTemplateArgs($this, %node.word, %node.array, $this->params, "component"))->renderToContentType(%raw);',
			$node->modifiers
				? $writer->write('function ($s, $type) { $_fi = new LR\FilterInfo($type); return %modifyContent($s); }')
				: var_export($noEscape ? null : implode($node->context), true)
		);
	};

	add_action('template_redirect', function () {
		global $View;
		$View['Post'] = get_queried_object();
	});
};
