<?php

function buildMenuTree(array &$elements, $parentId = 0)
{
	$branch = [];
	foreach ($elements as &$element) {
		if ($element->menu_item_parent == $parentId) {
			$children = buildMenuTree($elements, $element->ID);
			if ($children) {
				$element->wpse_children = $children;
				$element->children = $children;
			} else {
				$element->wpse_children = false;
				$element->children = false;
			}

			$branch[$element->ID] = $element;
			unset($element);
		}
	}
	return $branch;
}

function getMenuTree($location)
{
	$menuLocations = get_nav_menu_locations();
	$items = wp_get_nav_menu_items($menuLocations[$location]) ?: [];
	_wp_menu_item_classes_by_context($items);
	return buildMenuTree($items, 0);
}
