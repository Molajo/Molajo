<?php
/**
 * @version		$Id: view.html.php 21655 2011-06-23 05:43:24Z chdemko $
 * @package		Joomla.Administrator
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * Users mail view.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_users
 */
class UsersViewMail extends JView
{
	/**
	 * @var object form object
	 */
	protected $form;

	/**
	 * Display the view
	 */
	function display($tpl = null)
	{
		// Get data from the model
		$this->form = $this->get('Form');

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		JRequest::setVar('hidemainmenu', 1);

		MolajoToolbarHelper::title(JText::_('COM_USERS_MASS_MAIL'), 'massmail.png');
		MolajoToolbarHelper::custom('mail.send', 'send.png', 'send_f2.png', 'COM_USERS_TOOLBAR_MAIL_SEND_MAIL', false);
		MolajoToolbarHelper::cancel('mail.cancel');
		MolajoToolbarHelper::divider();
		MolajoToolbarHelper::preferences('com_users');
		MolajoToolbarHelper::divider();
		MolajoToolbarHelper::help('JHELP_USERS_MASS_MAIL_USERS');
	}
}
