<?php

use Latte\PhpWriter,
    Latte\Macros\MacroSet,
    Latte\MacroNode;

use Nette\Utils\Strings;

class MangowebLatteMacroSet extends MacroSet {

	public static function install(Latte\Compiler $compiler) {
		$me = new static($compiler);

		$me->addMacro('loop', array($me, 'macroLoop'), array($me, 'macroLoopEnd'));
		$me->addMacro('repeat', array($me, 'macroRepeat'), array($me, 'macroRepeatEnd'));
		$me->addMacro('set', array($me, 'macroSet'));
	}

	public function macroLoop(MacroNode $node, PhpWriter $writer) {
		$query = empty($node->args) ? '$GLOBALS["wp_query"]' : $node->args;
		return $writer->write('@$Posts[]=$Post; while('.$query.'->have_posts()){ '.$query.'->the_post();$Post='.$query.'->post;');
	}

	public function macroLoopEnd(MacroNode $node, PhpWriter $writer) {
		return $writer->write('}wp_reset_postdata(); $Post=array_pop($Posts)');
	}

	public function macroRepeat(MacroNode $node, PhpWriter $writer) {
		$args = explode(',', empty($node->args) ? '5' : $node->args);
		$args = array_map('intval', $args);
		$min = min($args);
		$max = max($args);
		;

		return $writer->write('@$_repeats[]=$_repeat; foreach(range(1, rand('.$min.', '.$max.')) as $_repeat){');
	}

	public function macroRepeatEnd(MacroNode $node, PhpWriter $writer) {
		return $writer->write('}$_repeat=array_pop($_repeats)');
	}

	public function macroSet(MacroNode $node, PhpWriter $writer) {
		$parts = Strings::replace($node->args, '~(\\s*(=>|=)\\s*|\\s+)~', '~~~', 1);
		$parts = Strings::split($parts, '/~~~/');
		$variable = $parts[0];
		$rest = $parts[1];
		return $writer->write($variable . ' = %modify(' . $rest . ')');
	}

}
