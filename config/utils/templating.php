<?php

define('NBSP', "\xC2\xA0");

$initTheme[] = function ($dir) {
	define('THEME_VIEWS_DIR', $dir . '/views');
	MangoPressTemplating::init();

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
			call_user_func_array([$this,"createTemplate"], \MangoPress\Components::createTemplateArgs($this, %node.word, %node.array, $this->params, "component"))->renderToContentType(%raw);',
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
