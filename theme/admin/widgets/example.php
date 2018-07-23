<?php

return !SHOW_EXAMPLES ?: [
	'title' => 'From custom render function',
	'render' => function () { echo date('c'); },
	'renderControl' => function () { echo date('c'); },
];
