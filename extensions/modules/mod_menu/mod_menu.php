<?php
/**
 * @version		$Id: mod_menu.php 20806 2011-02-21 19:44:59Z dextercowley $
 * @package		Joomla.Site
 * @subpackage	mod_menu
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('JPATH_PLATFORM') or die;

// Include the syndicate functions only once
require_once dirname(__FILE__).'/helper.php';

$list	= modMenuHelper::getList($parameters);
$app	= MolajoFactory::getApplication();
$menu	= $app->getMenu();
$active	= $menu->getActive();
$active_id = isset($active) ? $active->id : $menu->getDefault()->id;
$path	= isset($active) ? $active->tree : array();
$showAll	= $parameters->get('showAllChildren');
$class_sfx	= htmlspecialchars($parameters->get('class_sfx'));

if(count($list)) {
	require MolajoModuleHelper::getLayoutPath('mod_menu', $parameters->get('layout', 'default'));
}