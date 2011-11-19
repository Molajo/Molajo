<?php
/**
 * @package     Molajo
 * @subpackage  Application
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * @package		Molajo
 * @subpackage	Menu
 * @since		1.6
 */
class modMenuHelper
{
	/**
	 * Get a list of the menu items.
	 *
	 * @param	JRegistry	$parameters	The module options.
	 *
	 * @return	array
	 * @since	1.5
	 */
	static function getList(&$parameters)
	{
		$rowset		= array();
		$db			= MolajoFactory::getDbo();
		$user		= MolajoFactory::getUser();
		$app		= MolajoFactory::getApplication();

		$menu		= $app->getMenu();

        $active	= $menu->getActive();
        if (isset($active)) {
            $active_id = $active->id;
        } else {
            if (isset($menu)) {
                $active_id = $menu->getDefault();
            } else {
                $active_id = null;
            }
        }

        if ($active_id == null) {
            $path = array();
        } else {
            $path   = $active->tree;
        }

        /** Module Parameters */
		$start		= (int) $parameters->get('start_level');
		$end		= (int) $parameters->get('end_level');
		$show_all	= (int) $parameters->get('show_all_children');
		$max_depth	= (int) $parameters->get('max_depth');

        /** Retrieve Menu and Menu Items */
		$items 		= $menu->getItems('extension_instance_id', $parameters->get('menu_id'));

		$lastitem	= 0;

        /** Process Menu Items */
		if ($items) {
			foreach($items as $i => $item) {

                /** save for the layout */
                $item->active_id = $active_id;
                $item->path = $path;

//                $item->template_id =
//                $item->link_target

				if (($start && $start > $item->menu_item_level)
					|| ($end && $item->menu_item_level > $end)
					|| (!$show_all && $item->menu_item_level > 1 && !in_array($item->menu_item_parent_id, $path))
					|| ($max_depth > 0 && $item->menu_item_level > $max_depth)
					|| ($start > 1 && !in_array($item->menu_item_tree[0], $path))
				) {
					unset($items[$i]);
					continue;
				}

				$item->menu_item_deeper = false;
				$item->menu_item_shallower = false;
				$item->menu_item_level_diff = 0;

				if (isset($items[$lastitem])) {
					$items[$lastitem]->menu_item_deeper		= ($item->menu_item_level > $items[$lastitem]->menu_item_level);
					$items[$lastitem]->menu_item_shallower	= ($item->menu_item_level < $items[$lastitem]->menu_item_level);
					$items[$lastitem]->menu_item_level_diff	= ($items[$lastitem]->menu_item_level - $item->menu_item_level);
				}
				
				$item->menu_item_parent = (boolean) $menu->getItems('menu_item_parent_id', (int) $item->extension_instance_id, true);

				$lastitem = $i;
				$item->menu_item_active	= false;
				$item->menu_item_flink = $item->request;

				switch ($item->content_type_id)
				{
					case MOLAJO_CONTENT_TYPE_MENU_ITEM_SEPARATOR:
						continue;

					case MOLAJO_CONTENT_TYPE_MENU_ITEM_LINK:
						$item->menu_item_flink = $item->sef_request;
						break;


                    case MOLAJO_CONTENT_TYPE_MENU_ITEM_MODULE;
                        break;

					default:
						$item->menu_item_flink = $item->sef_request;
						break;
                }

				if (strcasecmp(substr($item->menu_item_flink, 0, 4), 'http') && (strpos($item->menu_item_flink, 'index.php?') !== false)) {
					$item->menu_item_flink = MolajoRoute::_($item->menu_item_flink, true, $item->menu_item_parameters->get('secure'));
				}
				else {
					$item->menu_item_flink = MolajoRoute::_($item->menu_item_flink);
				}
				
				$item->menu_item_title = htmlspecialchars($item->menu_item_title);
				$item->anchor_css = htmlspecialchars($item->menu_item_parameters->get('anchor_css', ''));
				$item->link_title = htmlspecialchars($item->menu_item_parameters->get('link_title', ''));
				$item->anchor_image = $item->menu_item_parameters->get('anchor_image', '') ? htmlspecialchars($item->menu_item_parameters->get('anchor_image', '')) : '';
			}

			if (isset($items[$lastitem])) {
				$items[$lastitem]->menu_item_deeper		= (($start?$start:1) > $items[$lastitem]->menu_item_level);
				$items[$lastitem]->menu_item_shallower	= (($start?$start:1) < $items[$lastitem]->menu_item_level);
				$items[$lastitem]->menu_item_level_diff	= ($items[$lastitem]->menu_item_level - ($start?$start:1));
			}
		}

		return $items;
	}
}
