<?php
/**
 * @version		$Id: view.html.php 21655 2011-06-23 05:43:24Z chdemko $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list of users.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_users
 * * * @since		1.0
 */
class UsersViewUsers extends JView
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.0
	 */
	protected function addToolbar()
	{
		$canDo	= UsersHelper::getActions();

		MolajoToolbarHelper::title(JText::_('COM_USERS_VIEW_USERS_TITLE'), 'user');

		if ($canDo->get('core.create')) {
			MolajoToolbarHelper::addNew('user.add');
		}
		if ($canDo->get('core.edit')) {
			MolajoToolbarHelper::editList('user.edit');
		}

		if ($canDo->get('core.edit.state')) {
			MolajoToolbarHelper::divider();
			MolajoToolbarHelper::publish('users.activate', 'COM_USERS_TOOLBAR_ACTIVATE');
			MolajoToolbarHelper::unpublish('users.block', 'COM_USERS_TOOLBAR_BLOCK');
			MolajoToolbarHelper::custom('users.unblock', 'unblock.png', 'unblock_f2.png', 'COM_USERS_TOOLBAR_UNBLOCK', true);
			MolajoToolbarHelper::divider();
		}

		if ($canDo->get('core.delete')) {
			MolajoToolbarHelper::deleteList('', 'users.delete');
			MolajoToolbarHelper::divider();
		}

		if ($canDo->get('core.admin')) {
			MolajoToolbarHelper::preferences('com_users');
			MolajoToolbarHelper::divider();
		}

		MolajoToolbarHelper::help('JHELP_USERS_USER_MANAGER');
	}
}
