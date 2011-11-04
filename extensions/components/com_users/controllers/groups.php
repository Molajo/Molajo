<?php
/**
 * @version		$Id: groups.php 20228 2011-01-10 00:52:54Z eddieajau $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * User groups list controller class.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_users
 * * * @since		1.0
 */
class UsersControllerGroups extends JControllerAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.0
	 */
	protected $text_prefix = 'COM_USERS_GROUPS';

	/**
	 * Proxy for getModel.
	 *
	 * @since	1.0
	 */
	public function getModel($name = 'Group', $prefix = 'UsersModel')
	{
		return parent::getModel($name, $prefix, array('ignore_request' => true));
	}

	/**
	 * Removes an item.
	 *
	 * Overrides JControllerAdmin::delete to check the core.admin permission.
	 *
	 * @since	1.0
	 */
	function delete()
	{
		if (!MolajoFactory::getUser()->authorise('core.admin', $this->option)) {
			MolajoError::raiseError(500, MolajoText::_('JERROR_ALERTNOAUTHOR'));
			jexit();
		}

		return parent::delete();
	}

	/**
	 * Method to publish a list of records.
	 *
	 * Overrides JControllerAdmin::publish to check the core.admin permission.
	 *
	 * @since	1.0
	 */
	function publish()
	{
		if (!MolajoFactory::getUser()->authorise('core.admin', $this->option)) {
			MolajoError::raiseError(500, MolajoText::_('JERROR_ALERTNOAUTHOR'));
			jexit();
		}

		return parent::publish();
	}

	/**
	 * Changes the order of one or more records.
	 *
	 * Overrides JControllerAdmin::reorder to check the core.admin permission.
	 *
	 * @since	1.0
	 */
	public function reorder()
	{
		if (!MolajoFactory::getUser()->authorise('core.admin', $this->option)) {
			MolajoError::raiseError(500, MolajoText::_('JERROR_ALERTNOAUTHOR'));
			jexit();
		}

		return parent::reorder();
	}

	/**
	 * Method to save the submitted ordering values for records.
	 *
	 * Overrides JControllerAdmin::saveorder to check the core.admin permission.
	 *
	 * @since	1.0
	 */
	public function saveorder()
	{
		if (!MolajoFactory::getUser()->authorise('core.admin', $this->option)) {
			MolajoError::raiseError(500, MolajoText::_('JERROR_ALERTNOAUTHOR'));
			jexit();
		}

		return parent::saveorder();
	}

	/**
	 * Check in of one or more records.
	 *
	 * Overrides JControllerAdmin::checkin to check the core.admin permission.
	 *
	 * @since	1.0
	 */
	public function checkin()
	{
		if (!MolajoFactory::getUser()->authorise('core.admin', $this->option)) {
			MolajoError::raiseError(500, MolajoText::_('JERROR_ALERTNOAUTHOR'));
			jexit();
		}

		return parent::checkin();
	}
}