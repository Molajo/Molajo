<?php
/**
 * @package     Molajo
 * @subpackage  Login
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

$showhelp = $this->params->get('showhelp', 1);
//
// Site SubMenu
//
$menu->addChild(new MolajoMenuNode(JText::_('JSITE'), null, 'disabled'));

//
// Users Submenu
//
if ($this->user->authorise('core.manage', 'com_users'))
{
	$menu->addChild(new MolajoMenuNode(JText::_('MOD_MENU_COM_USERS'), null, 'disabled'));
}

//
// Menus Submenu
//
if ($this->user->authorise('core.manage', 'com_menus'))
{
	$menu->addChild(new MolajoMenuNode(JText::_('MOD_MENU_MENUS'), null, 'disabled'));
}

//
// Content Submenu
//
if ($this->user->authorise('core.manage', 'com_content'))
{
	$menu->addChild(new MolajoMenuNode(JText::_('MOD_MENU_COM_ARTICLES'), null, 'disabled'));
}

//
// Components Submenu
//

// Get the authorised components and sub-menus.
$components = MolajoLaunchpadHelper::getComponents( true );

// Check if there are any components, otherwise, don't display the components menu item
if ($components) {
	$menu->addChild(new MolajoMenuNode(JText::_('MOD_MENU_COMPONENTS'),  null, 'disabled'));
}

//
// Extensions Submenu
//
$im = $this->user->authorise('core.manage', 'com_installer');
$mm = $this->user->authorise('core.manage', 'com_modules');
$pm = $this->user->authorise('core.manage', 'com_plugins');
$tm = $this->user->authorise('core.manage', 'com_templates');
$lm = $this->user->authorise('core.manage', 'com_languages');

if ($im || $mm || $pm || $tm || $lm)
{
	$menu->addChild(new MolajoMenuNode(JText::_('MOD_MENU_EXTENSIONS_EXTENSIONS'), null, 'disabled'));
}

//
// Help Submenu
//
if ($showhelp == 1) {
$menu->addChild(new MolajoMenuNode(JText::_('MOD_MENU_HELP'), null,'disabled'));
}

