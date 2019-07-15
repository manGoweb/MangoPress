<?php

require_once __DIR__ . '/templating.php';

function is_not_null($value)
{
	return !is_null($value);
}

function createInitComponent($name, $argsOrHandler = null, string $handler = 'initComponents')
{
	if (is_string($argsOrHandler)) {
		$args = [];
		$handler = $argsOrHandler;
	} else {
		$args = $argsOrHandler;
	}

	if (is_array($name)) {
		$args = $name;
	} else {
		$args = ($args ?? null) ?: [];
		$args['name'] = $name;
	}

	$handler = $args['handler'] ?? $handler;
	$name = $args['name'] ?? null;
	$place = $args['place'] ?? null;
	$props = $args['props'] ?? null;

	$component = [
		'name' => $name,
		'place' => $place,
		'props' => $props,
	];

	return Nette\Utils\Html::el("script")->addHtml(
'
;(function(){
	(window.'.$handler.' = window.'.$handler.' || []).push('.Nette\Utils\Json::encode(array_filter($component, 'is_not_null')).')
})();
'
);
}

$initTheme[] = function ($dir) {
	MangoPressTemplatingMacroSet::$set['initComponent'] = function ($node, $writer) {
		return $writer->write('echo %escape(createInitComponent(%node.args));');
	};

	MangoPressTemplatingMacroSet::$set['initAdminComponent'] = function ($node, $writer) {
		return $writer->write('echo %escape(createInitComponent(%node.args, \'initAdminComponents\'));');
	};

	MangoPressTemplatingMacroSet::$set['initStyleguideComponent'] = function ($node, $writer) {
		return $writer->write('echo %escape(createInitComponent(%node.args, \'initStyleguideComponents\'));');
	};
};
