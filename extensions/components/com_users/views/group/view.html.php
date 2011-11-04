<?php
/**
 * @version		$Id: view.html.php 21655 2011-06-23 05:43:24Z chdemko $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View to edit a user group.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_users
 * * * @since		1.0
 */
class UsersViewGroup extends JView
{
	protected $form;
	protected $item;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			MolajoError::raiseError(500, implode("\n", $errors));
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
		JRequest::setVar('hidemainmenu', 1);

		$user		= MolajoFactory::getUser();
		$isNew		= ($this->item->id == 0);
		$canDo		= UsersHelper::getActions();

		MolajoToolbarHelper::title(MolajoText::_($isNew ? 'COM_USERS_VIEW_NEW_GROUP_TITLE' : 'COM_USERS_VIEW_EDIT_GROUP_TITLE'), 'groups-add');

		if ($canDo->get('core.edit')||$canDo->get('core.create')) {
			MolajoToolbarHelper::apply('group.apply');
			MolajoToolbarHelper::save('group.save');
		}
		if ($canDo->get('core.create')) {
			MolajoToolbarHelper::save2new('group.save2new');
		}
		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create')) {
			MolajoToolbarHelper::save2copy('group.save2copy');
		}

		if (empty($this->item->id))  {
			MolajoToolbarHelper::cancel('group.cancel');
		} else {
			MolajoToolbarHelper::cancel('group.cancel', 'JTOOLBAR_CLOSE');
		}

		MolajoToolbarHelper::divider();
		MolajoToolbarHelper::help('JHELP_USERS_GROUPS_EDIT');
	}
}