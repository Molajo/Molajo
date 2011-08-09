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
 * View class for a list of redirection links.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_redirect
 * * * @since		1.0
 */
class RedirectViewLinks extends JView
{
	protected $enabled;
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 *
	 * @since	1.0
	 */
	public function display($tpl = null)
	{
		$this->enabled		= RedirectHelper::isEnabled();
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
	 * @since	1.0
	 */
	protected function addToolbar()
	{
		$state	= $this->get('State');
		$canDo	= RedirectHelper::getActions();

		MolajoToolbarHelper::title(JText::_('COM_REDIRECT_MANAGER_LINKS'), 'redirect');
		if ($canDo->get('core.create')) {
			MolajoToolbarHelper::addNew('link.add');
		}
		if ($canDo->get('core.edit')) {
			MolajoToolbarHelper::editList('link.edit');
		}
		if ($canDo->get('core.edit.state')) {
			if ($state->get('filter.state') != 2){
				MolajoToolbarHelper::divider();
				MolajoToolbarHelper::publish('links.publish', 'JTOOLBAR_ENABLE');
				MolajoToolbarHelper::unpublish('links.unpublish', 'JTOOLBAR_DISABLE');
			}
			if ($state->get('filter.state') != -1 ) {
				MolajoToolbarHelper::divider();
				if ($state->get('filter.state') != 2) {
					MolajoToolbarHelper::archiveList('links.archive');
				}
				else if ($state->get('filter.state') == 2) {
					MolajoToolbarHelper::unarchiveList('links.publish', 'JTOOLBAR_UNARCHIVE');
				}
			}
		}
		if ($state->get('filter.state') == -2 && $canDo->get('core.delete')) {
			MolajoToolbarHelper::deleteList('', 'links.delete', 'JTOOLBAR_EMPTY_TRASH');
		} else if ($canDo->get('core.edit.state')) {
			MolajoToolbarHelper::trash('links.trash');
			MolajoToolbarHelper::divider();
		}
		if ($canDo->get('core.admin')) {
			MolajoToolbarHelper::preferences('com_redirect');
			MolajoToolbarHelper::divider();
		}
		MolajoToolbarHelper::help('JHELP_COMPONENTS_REDIRECT_MANAGER');
	}
}
