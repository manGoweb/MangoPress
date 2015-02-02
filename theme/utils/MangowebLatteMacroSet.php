<?php

use Latte\PhpWriter,
    Latte\Macros\MacroSet,
    Latte\MacroNode;

class MangowebLatteMacroSet extends MacroSet {

	public static function install(Latte\Compiler $compiler) {
		$me = new static($compiler);

		$me->addMacro('loop', array($me, 'macroLoop'), array($me, 'macroLoopEnd'));
	}

	public function macroLoop(MacroNode $node, PhpWriter $writer) {
		$query = empty($node->args) ? '$GLOBALS["wp_query"]' : $node->args;
		return $writer->write('while('.$query.'->have_posts()){ '.$query.'->the_post();');
	}

	public function macroLoopEnd(MacroNode $node, PhpWriter $writer) {
		return $writer->write('}');
	}

}
