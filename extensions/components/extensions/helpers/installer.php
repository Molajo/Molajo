<?php
/**
 * @version        $Id: installer.php 20196 2011-01-09 02:40:25Z ian $
 * @package        Joomla.Administrator
 * @subpackage    installer
 * @copyright    Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * Installer helper.
 *
 * @package        Joomla.Administrator
 * @subpackage    installer
 * * * @since        1.0
 */
class InstallerHelper
{
    /**
     * Configure the Linkbar.
     *
     * @param    string    The name of the active view.
     */
    public static function addSubmenu($vName = 'install')
    {
        JSubMenuHelper::addEntry(
            MolajoTextHelper::_('INSTALLER_SUBMENU_INSTALL'),
            'index.php?option=installer',
            $vName == 'install'
        );
        JSubMenuHelper::addEntry(
            MolajoTextHelper::_('INSTALLER_SUBMENU_UPDATE'),
            'index.php?option=installer&view=update',
            $vName == 'update'
        );
        JSubMenuHelper::addEntry(
            MolajoTextHelper::_('INSTALLER_SUBMENU_MANAGE'),
            'index.php?option=installer&view=manage',
            $vName == 'manage'
        );
        JSubMenuHelper::addEntry(
            MolajoTextHelper::_('INSTALLER_SUBMENU_DISCOVER'),
            'index.php?option=installer&view=discover',
            $vName == 'discover'
        );
        JSubMenuHelper::addEntry(
            MolajoTextHelper::_('INSTALLER_SUBMENU_WARNINGS'),
            'index.php?option=installer&view=warnings',
            $vName == 'warnings'
        );
    }

    /**
     * Gets a list of the actions that can be performed.
     *
     * @return    JObject
     * @since    1.0
     */
    public static function getActions()
    {
        $user = MolajoController::getUser();
        $result = new JObject;

        $assetName = 'installer';

        $actions = array(
            'core.admin', 'core.manage', 'core.edit.state', 'core.delete'
        );

        foreach ($actions as $action) {
            $result->set($action, $user->authorise($action, $assetName));
        }

        return $result;
    }
}