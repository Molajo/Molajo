<?php
/**
 * @version		$Id:mod_menu.php 2463 2006-02-18 06:05:38Z webImagery $
 * @package		Joomla.Administrator
 * @subpackage	mod_menu
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

$showhelp 	= true;
//
// Site SubMenu
//
$menu->addChild(new JMenuNode(JText::_('MOD_MENU_DASHBOARD'), null, 'disabled'));

//
// Users Submenu
//
if ($user->authorise('manage', 'com_users'))
{
	$menu->addChild(new JMenuNode(JText::_('MOD_MENU_COM_USERS'), null, 'disabled daddy'));
}

//
// Content Submenu
//
if ($user->authorise('manage', 'com_content'))
{
	$menu->addChild(new JMenuNode(JText::_('MOD_MENU_ARTICLES'), null, 'disabled daddy'));
}

//
// Menus Submenu
//
if ($user->authorise('manage', 'com_menus'))
{
	$menu->addChild(new JMenuNode(JText::_('MOD_MENU_MENUS'), null, 'disabled daddy'));
}



//
// Extend Submenu
//

$im = $user->authorise('manage', 'com_installer');
$mm = $user->authorise('manage', 'com_modules');
$pm = $user->authorise('manage', 'com_plugins');
$tm = $user->authorise('manage', 'com_templates');
$lm = $user->authorise('manage', 'com_languages');
$components = ModMenuHelper::getComponents( true );

if ($im || $mm || $pm || $tm || $lm || $components)
{
	$menu->addChild(new JMenuNode(JText::_('MOD_MENU_EXTEND'), null, 'disabled daddy'));
}

if ($user->authorise('admin')) {
	$menu->addChild(new JMenuNode(JText::_('MOD_MENU_CONFIGURE'), null, 'disabled'));
}

//
// Help Submenu
//
if ($showhelp == 1) {
$menu->addChild(new JMenuNode(JText::_('MOD_MENU_HELP'), null,'disabled daddy'));
}

