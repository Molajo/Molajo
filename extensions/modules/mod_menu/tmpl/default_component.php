<?php
/**
 * @package     Molajo
 * @subpackage  Menu
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

$class = $item->anchor_css ? 'class="'.$item->anchor_css.'" ' : '';
$title = $item->link_title ? 'title="'.$item->link_title.'" ' : '';
if ($item->anchor_image) {
		$item->menu_item_add_menu_title == 1) ?
		$linktype = '<img src="'.$item->anchor_image.'" alt="'.$item->menu_item_title.'" /><span class="image-title">'.$item->menu_item_title.'</span> ' :
		$linktype = '<img src="'.$item->anchor_image.'" alt="'.$item->menu_item_title.'" />';
} 
else { $linktype = $item->menu_item_title;
}

switch ($item->menu_item_link_target) :
	default:
	case 0:
?><a <?php echo $class; ?>href="<?php echo $item->menu_item_flink; ?>" <?php echo $title; ?>><?php echo $linktype; ?></a><?php
		break;
	case 1:
		// _blank
?><a <?php echo $class; ?>href="<?php echo $item->menu_item_flink; ?>" target="_blank" <?php echo $title; ?>><?php echo $linktype; ?></a><?php
		break;
	case 2:
	// window.open
?><a <?php echo $class; ?>href="<?php echo $item->menu_item_flink; ?>" onclick="window.open(this.href,'targetWindow','toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes');return false;" <?php echo $title; ?>><?php echo $linktype; ?></a>
<?php
		break;
endswitch;
