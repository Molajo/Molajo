<?php
/**
 * @version     $id: installer.php
 * @package     Molajo
 * @subpackage  Text Filter
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Overriden to provide "Create" Submenu
 *
 * @package	Molajo
 * @subpackage	Helper
 * @since	1.6
 */
class InstallerHelper
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param	string	The name of the active view.
	 */
	public static function addSubmenu($vName = 'install')
	{
		JSubMenuHelper::addEntry(
			MolajoText::_('PLG_SYSTEM_CREATE_SUBMENU_CREATE'),
			'index.php?option=com_installer&view=create',
			$vName == 'create'
		);
		JSubMenuHelper::addEntry(
			MolajoText::_('COM_INSTALLER_SUBMENU_INSTALL'),
			'index.php?option=com_installer',
			$vName == 'install'
		);
		JSubMenuHelper::addEntry(
			MolajoText::_('COM_INSTALLER_SUBMENU_UPDATE'),
			'index.php?option=com_installer&view=update',
			$vName == 'update'
		);
		JSubMenuHelper::addEntry(
			MolajoText::_('COM_INSTALLER_SUBMENU_MANAGE'),
			'index.php?option=com_installer&view=manage',
			$vName == 'manage'
		);
		JSubMenuHelper::addEntry(
			MolajoText::_('COM_INSTALLER_SUBMENU_DISCOVER'),
			'index.php?option=com_installer&view=discover',
			$vName == 'discover'
		);
		JSubMenuHelper::addEntry(
			MolajoText::_('COM_INSTALLER_SUBMENU_WARNINGS'),
			'index.php?option=com_installer&view=warnings',
			$vName == 'warnings'
		);
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return	JObject
	 * @since	1.6
	 */
	public static function getActions()
	{
		$user	= MolajoFactory::getUser();
		$result	= new JObject;

		$assetName = 'com_installer';

		$actions = array(
			'administer', 'manage', 'edit.state', 'delete'
		);

		foreach ($actions as $action) {
			$result->set($action,	$user->authorise($action, $assetName));
		}

		return $result;
	}
}