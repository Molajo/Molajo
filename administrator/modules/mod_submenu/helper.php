<<?php
/**
 * @package     Molajo
 * @subpackage  Submenu
 * @copyright   Copyright (C) 2011 Molajo. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

abstract class modSubmenuHelper
{
	/**
	 * getItems()
     *
     * Get the member items of the submenu.
	 *
	 * @return	mixed	An arry of menu items, or false on error.
     * @version 1.0
	 */
	public static function getItems()
	{
//		$menu = MolajoToolbar::getInstance('submenu');
//var_dump($menu);
//
//		$list = $menu->getItems();
//var_dump($list);
//die();
		if (is_array($list) && count($list) > 0) {
        } else {
			return false;
		}
//$submenu = new MolajoSubmenuHelper ();
//$submenu->add();
		return $list;
	}
}