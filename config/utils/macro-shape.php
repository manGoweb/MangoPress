<?php

require_once __DIR__ . '/templating.php';

function createShapeElement(string $name, $class = null)
{
	return Nette\Utils\Html::el("svg")->addClass(classnames('shape', "shape-$name", $class))->addHtml('<use xlink:href="#shape-'.$name.'" />');
}

$initTheme[] = function ($dir) {
	MangoPressTemplatingMacroSet::$set['shape'] = function ($node, $writer) {
		return $writer->write('echo %escape(createShapeElement(%node.args));');
	};
};
