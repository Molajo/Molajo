<?php
/**
 * @package     Molajo
 * @subpackage  Menu
 * @copyright    Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

$title = $item->link_title ? 'title="' . $item->link_title . '" ' : '';
if ($item->anchor_image) {
    $item->menu_item_parameters->get('anchor_include_text', 1) ?
            $linktype = '<img src="' . $item->anchor_image . '" alt="' . $item->menu_item_title . '" /><span class="image-title">' . $item->menu_item_title . '</span> '
            :
            $linktype = '<img src="' . $item->anchor_image . '" alt="' . $item->menu_item_title . '" />';
}
else {
    $linktype = $item->menu_item_title;
}

?><span class="separator"><?php echo $title; ?><?php echo $linktype; ?></span>
