<?php
/**
 * @package     Molajo
 * @subpackage  Menu
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
?>
<ul class="menu<?php echo $class_suffix;?>"<?php
	$tag = '';
	if ($parameters->get('tag_id')!=NULL) {
		$tag = $parameters->get('tag_id').'';
		echo ' id="'.$tag.'"';
	}
?>>
<?php
foreach ($rowset as $i => &$item) :
	$class = '';
	if ($item->extension_instance_id == $item->active_id) {
		$class .= 'current ';
	}

	if (	$item->menu_item_type == 'alias' &&
			in_array($item->menu_item_parameters->get('aliasoptions'), $path)
		||	in_array($item->extension_instance_id, $item->path)) {
	  $class .= 'active ';
	}
	if ($item->menu_item_deeper) {
		$class .= 'deeper ';
	}
	
	if ($item->menu_item_parent) {
		$class .= 'parent ';
	}

	if (!empty($class)) {
		$class = ' class="'.trim($class) .'"';
	}

	echo '<li id="item-'.$item->extension_instance_id.'"'.$class.'>';

	// Render the menu item.
	switch ($item->menu_item_type) :

		case MOLAJO_ASSET_TYPE_MENU_ITEM_SEPARATOR:
			require_once dirname(__FILE__) . '/default_separator.php';
			break;

		case MOLAJO_ASSET_TYPE_MENU_ITEM_COMPONENT:
			require_once dirname(__FILE__) . '/default_component.php';
			break;

		case MOLAJO_ASSET_TYPE_MENU_ITEM_MODULE:
			require_once dirname(__FILE__) . '/default_module.php';
			break;

		default:
			require_once dirname(__FILE__) . '/default_url.php';
			break;
	endswitch;

	// The next item is deeper.
	if ($item->menu_item_deeper) {
		echo '<ul>';
	}
	// The next item is shallower.
	else if ($item->menu_item_shallower) {
		echo '</li>';
		echo str_repeat('</ul></li>', $item->menu_item_level_diff);
	}
	// The next item is on the same level.
	else {
		echo '</li>';
	}
endforeach;
?></ul>
