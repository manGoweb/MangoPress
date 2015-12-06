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
		$src = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAFIklEQVRYhaXXcazWVR3H8dd9JoKzDGHLyQ0YGBg4bAobMIuuHn87YRiBAGJYkcPW/KdarvVPgDO3Nlv902YLVjXHBAGRUDkcz5JMlqPWWibjBmIFwNwAHEgg4W3b/V338OMhL/O7PXt2ts/5ft+/53x/n+95uvr6+gw2YhWuRg9mYw7GAt7EFiT8LuXyn8Hm7BoMQKzCrbgXo3AKf8c/cBQwEhMxGR/Bv/FUyuWvHwogVmE8VuDjWI2tKZezsQoj0V0X7qtBDqZcjsUqXIU5eACHsCrl8s/LBohVWIpv4mcpl7WxCt1YjvkYj6sBAKfwBjZgdcrlcKzCV+ocP025rBs0QKzCKkzHPLTwYyzDlZd6kkacwRo8jCuwEb9PuTzaFLY6FF+JySmXz2MG9uEbl1EchuEhHMZMzMa0WIXv/1+AWIUlmJ5yWRirsBAF111G4WZsx+mUy3nMw+2xCvcAwPtHEKswButq2h488yEKw6splxkAEKswCpswL+VyGK4AwCP6m+VErMJcbMXb2ItRWI49+A2urz9X4jxOYS9G4wHAS02ilMuhWIUnsAoPvg8Qq3Azrhvo1JTLsjbqmbgdcBK78OeUy7HG092GHgDc3ASIVbgq5fKrWIUlsQqTUi67W4D78etYhaGxCkPbNnwUW/FDwDRsx/ONxB/DNrR3+Z2xCtMaDHfU3vJLLIVWba9j8AxWogcg5XISawEAML3+1QZ0b3fQDcFPYhW6ADACy7EZE2IVhrXwWRxNuZzF/bgHALCxAwAsaaw3dNB8BvMAcA0WplzO4DhmtjAHr8cqXItRuKv9GLAT+zskXxir0N7EO/CvDrplALgJ4+uj7cWdLdxdL0ajC924AyDl8i62oBk3IDZ0WzvoPh2r0ALc1lajF19oYQyOYQQAlgIALuUJn2usN3XQDEVXrMIUTAFcgxOYcJEVA2bXEw9gJ95AM7Y11i/jTbTHkdoJl6IL6u8+dLXqDSNxFADX4m6AlMs5bEZ77NXfO7e26d7Fcw3dDsBcAJzACOxpYQsm4gBOA+A+AMCzjfVaTMLjAIDmW7MpVuEm3Ag4iYP1emurJp6ccjmOvQCYVZsGwE7sA8Am3KV/wIwBwCvYDziKl/F1APSmXE5hAl5s1RtGxCoMcWETDcUigJTLf7EZ0FsXWQhY0KZrfxtK2yQEeLq+NQ3Hq62Uyzt1si/i5zgHgEUNJxsAWI9JGAtomtJGwNOxClMxDnAGv8B87Em5nGkBnsTylMsRrAHALZgGgD/iALZhMQCmxircAoBd2I0/4GsAeKIeYl+ta2pByuU1HIxVWIxv4QgAvgxQH8NKvIV7AdDVDpRyOY3v4TzmAQ7g4fqeuC/lsgdaAPgBvo1hWID3AF+KVRjWlnwNpuB6F8aiuo8GdL/FDHTjHOZjOB7CCrgAIOVyEI9jfcrllXrDexiLAABoDiwYh6buQfRhbsplF57CYymXty4CqCE2YEeswuaUy7OYhWP4TqwCqIfW7A4AsAQgVuEGTMCslMsLsQrPYVudF8BFVpxyeQx/ilXYjr9hDP6CbsACXHspgFiFTwDOYqp+t3xR/7W8aVoXA9QQj2I1EhalXL5bHxFMxIkO247jtQG4lMsB3Ifn9f+5+VGnWh/012w0VmA01iOlXA7U87wbwwHHcSjlcrK++c7BAuzHI23wlwfQBjIFizEO76AXvXVhGIEb8UkMXDbWpVxe/6DcgwJoAxmCHvRgNj6FPuzGC3gJO2q/GFT8D05h6xsz7ZrcAAAAAElFTkSuQmCC';
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
