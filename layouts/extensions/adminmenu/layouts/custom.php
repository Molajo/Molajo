<?php
/**
 * @package     Molajo
 * @subpackage  Layouts
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

$menu = new MolajoAdminCSSMenu();

$disableMenu = JRequest::getInt('hidemainmenu');
if ($disableMenu == 1) {
    $enabled = false;
    include dirname(__FILE__).'/menu_disabled.php';
} else {
    $enabled = true;
    include dirname(__FILE__).'/menu_enabled.php';
}

//todo fix the menu
echo $menu->renderMenu('menu', $enabled ? '' : 'disabled');
