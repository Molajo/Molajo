<?php
/**
 * @package     Molajo
 * @subpackage  Submenu
 * @copyright   Copyright (C) 2011 Molajo. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;



$request['wrap'] = $module->style;
$request['position'] = $module->position;
$request['layout'] = 'admintitle';
$request['layout_type'] = 'extension';




//if (!JRequest::getInt('hidemainmenu')):
//require_once dirname(__FILE__).'/helper.php';

//$list = modSubmenuHelper::getItems();
//if (count($list) > 0) {
//    require MolajoModuleHelper::getLayoutPath('mod_submenu', $params->get('layout', 'default'));
//}