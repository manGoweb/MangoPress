<?php

function varExport($var, $indent="")
{
	switch (gettype($var)) {
		case 'integer': case 'double': return $var;
		case "string":
			return '"' . addcslashes($var, "\\\$\"\r\n\t\v\f") . '"';
		case "array":
			$indexed = array_keys($var) === range(0, count($var) - 1);
			$r = [];
			foreach ($var as $key => $value) {
				$r[] = "$indent\t"
			. ($indexed ? "" : varExport($key) . " => ")
			. varExport($value, "$indent\t");
			}
			return "[\n" . implode(",\n", $r) . "\n" . $indent . "]";
		case "boolean":
			return $var ? "TRUE" : "FALSE";
		default:
			if ($var instanceof \SafeHtmlString) {
				return 'safe("' . addslashes((string) $var) . '")';
			}
			return var_export($var, true);
	}
}
