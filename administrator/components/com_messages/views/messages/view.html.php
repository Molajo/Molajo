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
 * View class for a list of messages.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_messages
 * * * @since		1.0
 */
class MessagesViewMessages extends JView
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
		$state	= $this->get('State');
		$canDo	= MessagesHelper::getActions();

		MolajoToolbarHelper::title(JText::_('COM_MESSAGES_MANAGER_MESSAGES'), 'inbox.png');

		if ($canDo->get('core.create')) {
			MolajoToolbarHelper::addNew('message.add');
		}

		if ($canDo->get('core.edit.state')) {
			MolajoToolbarHelper::divider();
			MolajoToolbarHelper::publish('messages.publish', 'COM_MESSAGES_TOOLBAR_MARK_AS_READ');
			MolajoToolbarHelper::unpublish('messages.unpublish', 'COM_MESSAGES_TOOLBAR_MARK_AS_UNREAD');
		}

		if ($state->get('filter.state') == -2 && $canDo->get('core.delete')) {
			MolajoToolbarHelper::divider();
			MolajoToolbarHelper::deleteList('', 'messages.delete','JTOOLBAR_EMPTY_TRASH');
		} else if ($canDo->get('core.edit.state')) {
			MolajoToolbarHelper::divider();
			MolajoToolbarHelper::trash('messages.trash');
		}

		//MolajoToolbarHelper::addNew('module.add');
		MolajoToolbarHelper::divider();
		$bar = MolajoToolbar::getInstance('toolbar');
		$bar->appendButton('Popup', 'options', 'COM_MESSAGES_TOOLBAR_MY_SETTINGS', 'index.php?option=com_messages&amp;view=config&amp;tmpl=component', 850, 400);

		if ($canDo->get('core.admin')) {
			MolajoToolbarHelper::preferences('com_messages');
		}

		MolajoToolbarHelper::divider();
		MolajoToolbarHelper::help('JHELP_COMPONENTS_MESSAGING_INBOX');
	}
}
