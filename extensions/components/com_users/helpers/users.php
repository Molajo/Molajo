<?php
/**
 * @version		$Id: users.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Administrator
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * Users component helper.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_users
 * * * @since		1.0
 */
class UsersHelper
{
	/**
	 * @var		JObject	A cache for the available actions.
	 * @since	1.0
	 */
	protected static $actions;

	/**
	 * Configure the Linkbar.
	 *
	 * @param	string	The name of the active view.
	 *
	 * @return	void
	 * @since	1.0
	 */
	public static function addSubmenu($vName)
	{
		JSubMenuHelper::addEntry(
			MolajoText::_('COM_USERS_SUBMENU_USERS'),
			'index.php?option=com_users&view=users',
			$vName == 'users'
		);

		// Groups and Levels are restricted to core.admin
		$canDo = self::getActions();

		if ($canDo->get('core.admin')) {
			JSubMenuHelper::addEntry(
				MolajoText::_('COM_USERS_SUBMENU_GROUPS'),
				'index.php?option=com_users&view=groups',
				$vName == 'groups'
			);
			JSubMenuHelper::addEntry(
				MolajoText::_('COM_USERS_SUBMENU_LEVELS'),
				'index.php?option=com_users&view=levels',
				$vName == 'levels'
			);
		}
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return	JObject
	 * @since	1.0
	 */
	public static function getActions()
	{
		if (empty(self::$actions)) {
			$user	= MolajoFactory::getUser();
			self::$actions	= new JObject;

			$actions = array(
				'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
			);

			foreach ($actions as $action) {
				self::$actions->set($action, $user->authorise($action, 'com_users'));
			}
		}

		return self::$actions;
	}

	/**
	 * Get a list of filter options for the blocked state of a user.
	 *
	 * @return	array	An array of MolajoHTMLOption elements.
	 * @since	1.0
	 */
	static function getStateOptions()
	{
		// Build the filter options.
		$options	= array();
		$options[]	= MolajoHTML::_('select.option', '0', MolajoText::_('JENABLED'));
		$options[]	= MolajoHTML::_('select.option', '1', MolajoText::_('JDISABLED'));

		return $options;
	}

	/**
	 * Get a list of filter options for the activated state of a user.
	 *
	 * @return	array	An array of MolajoHTMLOption elements.
	 * @since	1.0
	 */
	static function getActiveOptions()
	{
		// Build the filter options.
		$options	= array();
		$options[]	= MolajoHTML::_('select.option', '0', MolajoText::_('COM_USERS_ACTIVATED'));
		$options[]	= MolajoHTML::_('select.option', '1', MolajoText::_('COM_USERS_UNACTIVATED'));

		return $options;
	}

	/**
	 * Get a list of the user groups for filtering.
	 *
	 * @return	array	An array of MolajoHTMLOption elements.
	 * @since	1.0
	 */
	static function getGroups()
	{
		$db = MolajoFactory::getDbo();
		$db->setQuery(
			'SELECT a.id AS value, a.title AS text, COUNT(DISTINCT b.id) AS level' .
			' FROM #__usergroups AS a' .
			' LEFT JOIN `#__usergroups` AS b ON a.lft > b.lft AND a.rgt < b.rgt' .
			' GROUP BY a.id' .
			' ORDER BY a.lft ASC'
		);
		$options = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum()) {
			MolajoError::raiseNotice(500, $db->getErrorMsg());
			return null;
		}

		foreach ($options as &$option) {
			$option->text = str_repeat('- ',$option->level).$option->text;
		}

		return $options;
	}
}