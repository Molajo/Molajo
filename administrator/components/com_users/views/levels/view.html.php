<?php
/**
 * @version		$Id: view.html.php 21655 2011-06-23 05:43:24Z chdemko $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * The HTML Users access levels view.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_users
 * * * @since		1.0
 */
class UsersViewLevels extends JView
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

		parent::display($tpl);
		$this->addToolbar();
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		$canDo	= UsersHelper::getActions();

		MolajoToolbarHelper::title(JText::_('COM_USERS_VIEW_LEVELS_TITLE'), 'levels');

		if ($canDo->get('core.create')) {
			MolajoToolbarHelper::addNew('level.add');
		}
		if ($canDo->get('core.edit')) {
			MolajoToolbarHelper::editList('level.edit');
			MolajoToolbarHelper::divider();
		}
		if ($canDo->get('core.delete')) {
			MolajoToolbarHelper::deleteList('', 'level.delete');
			MolajoToolbarHelper::divider();
		}
		if ($canDo->get('core.admin')) {
			MolajoToolbarHelper::preferences('com_users');
			MolajoToolbarHelper::divider();
		}
		MolajoToolbarHelper::help('JHELP_USERS_ACCESS_LEVELS');
	}
}
