<?php

function flattenArray($items)
{
	$all = new RecursiveIteratorIterator(new RecursiveArrayIterator($items));
	$result = [];
	foreach ($all as $a) {
		$result[] = $a;
	}
	return array_filter($result);
}

function classnames()
{
	$classes = flattenArray(func_get_args());
	return implode(' ', $classes);
}


function viewPrefix()
{
	$classes = flattenArray(func_get_args());
	foreach ($classes as $i => $name) {
		$classes[$i] = "view-$name";
	}
	return implode(' ', $classes);
}



function dataPrefix(array $keyvals = null)
{
	$result = [];
	if ($keyvals) {
		foreach ($keyvals as $key => $val) {
			$result["data-$key"] = $val;
		}
	}
	return $result;
}

function merge()
{
	$arrays = func_get_args();
	$result = [];

	foreach ($arrays as $array) {
		if (is_array($array)) {
			foreach ($array as $key => $val) {
				$result[$key] = $val;
			}
		}
	}

	return $result;
}
