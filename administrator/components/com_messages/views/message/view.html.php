<?php
/**
 * @version		$Id: view.html.php 21655 2011-06-23 05:43:24Z chdemko $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Messages component
 *
 * @package		Joomla.Administrator
 * @subpackage	com_messages
 * * * @since		1.0
 */
class MessagesViewMessage extends JView
{
	protected $form;
	protected $item;
	protected $state;

	public function display($tpl = null)
	{
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		$this->state	= $this->get('State');

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
		if ($this->getLayout() == 'edit') {
			MolajoToolbarHelper::title(JText::_('COM_MESSAGES_WRITE_PRIVATE_MESSAGE'), 'new-privatemessage.png');
			MolajoToolbarHelper::save('message.save', 'COM_MESSAGES_TOOLBAR_SEND');
			MolajoToolbarHelper::cancel('message.cancel');
			MolajoToolbarHelper::help('JHELP_COMPONENTS_MESSAGING_WRITE');
		} else {
			MolajoToolbarHelper::title(JText::_('COM_MESSAGES_VIEW_PRIVATE_MESSAGE'), 'inbox.png');
			$sender = JUser::getInstance($this->item->user_id_from);
			if ($sender->authorise('core.admin') || $sender->authorise('core.manage','com_messages') && $sender->authorise('core.login.admin')) {
				MolajoToolbarHelper::custom('message.reply', 'restore.png', 'restore_f2.png', 'COM_MESSAGES_TOOLBAR_REPLY', false);
			}
			MolajoToolbarHelper::cancel('message.cancel');
			MolajoToolbarHelper::help('JHELP_COMPONENTS_MESSAGING_READ');
		}
	}
}
