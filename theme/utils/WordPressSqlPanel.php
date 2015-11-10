<?php

class WordPressSqlPanel implements Nette\Diagnostics\IBarPanel {


	public static function init() {
		if(!Tracy\Debugger::$productionMode) {
			Tracy\Debugger::getBar()->addPanel(new self);
		}
	}


	public function __construct() {
		define('SAVEQUERIES', true);
	}


	public function getTab() {
		global $wpdb;
		$span = Nette\Utils\Html::el('span');
		$src = Nette\Templating\DefaultHelpers::dataStream(file_get_contents(dirname(__FILE__) . '/WordPressSqlPanel.png'));
		$img = Nette\Utils\Html::el('img width=16', array('alt' => 'WordPress icon', 'src' => $src));
		$span->add($img);
		$time = 0;
		foreach($wpdb->queries as $q) {
			$time += $q[1];
		}
		$count = $wpdb->num_queries;
		$span->add(($time ? sprintf(' %0.1f ms / ', $time * 1000) : '') . $count);
		return $span;
	}


	public function getPanel() {
		global $wpdb;
		$inner = '';
		foreach($wpdb->queries as $q) {
			$query = \Nette\Database\Helpers::dumpSql($q[0]);
			$time = round($q[1]*100000)/1000;
			$files = $q[2];
			$callstack = explode(', ', $q[2]);
			array_shift($callstack);
			array_shift($callstack);
			array_shift($callstack);
			array_map($callstack, 'htmlspecialchars');
			$callstack = implode('<br>', $callstack);
			$inner .= "<tr><td rowspan='2'>$time</td><td><code>$query</code></td></tr><tr><td><code style='font-size:.8em'>$callstack</code></td></tr>";
		}
		$panelHtml = "
		<h1>WordPress SQL</h1>
		<div class='tracy-inner nette-DbConnectionPanel'>
			<table>
				<tr><th>Time&nbsp;ms</th><th>SQL Query</th></tr>
					$inner
			</table>
		</div>
		";
		return $panelHtml;
	}


}

WordPressSqlPanel::init();
