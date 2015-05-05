<?php

class WordPressSqlPanel extends Nette\Object implements Nette\Diagnostics\IBarPanel {
	public function __construct() {
		define('SAVEQUERIES', true);
	}
	public function getTab() {
		global $wpdb;
		$span = Nette\Utils\Html::el('span');
		$src = Nette\Templating\DefaultHelpers::dataStream(file_get_contents(dirname(__FILE__) . '/wp-sql-panel-icon.png'));
		$img = Nette\Utils\Html::el('img width=16', array('alt' => 'WordPress icon', 'src' => $src));
		$span->add($img);
		$time = 0;
		foreach($wpdb->queries as $q) {
			$time += $q[1];
		}
		$count = count($wpdb->queries);
		$span->add(($time ? sprintf(' %0.1f ms / ', $time * 1000) : '') . $count);
		return $span;
	}
	public function getPanel() {
		global $wpdb;
		$inner = '';
		foreach($wpdb->queries as $q) {
			$query = $q[0];
			$time = round($q[1]*100000)/1000;
			$files = $q[2];
			$inner .= "<tr><td>$time</td><td><code>$query</code></td></tr>";
		}
		$panelHtml = "
<h1>WordPress SQL</h1>
<div class='tracy-inner nette-DbConnectionPanel'>
<table>
<tr><th>Time&nbsp;ms</th><th>SQL Query</th></tr>
".$inner."
</table>
</div>
";
		return $panelHtml;
	}
}

if(!Tracy\Debugger::$productionMode) {
	Tracy\Debugger::getBar()->addPanel(new WordPressSqlPanel);
}
