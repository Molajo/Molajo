<?php
/**
 * @version		$Id: view.html.php 21655 2011-06-23 05:43:24Z chdemko $
 * @package		Joomla.Administrator
 * @subpackage	com_installer
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

include_once dirname(__FILE__).'/../default/view.php';

/**
 * Extension Manager Manage View
 *
 * @package		Joomla.Administrator
 * @subpackage	com_installer
 * * * @since		1.0
 */
class InstallerViewManage extends InstallerViewDefault
{
	protected $items;
	protected $pagination;
	protected $form;
	protected $state;

	/**
	 * @since	1.6
	 */
	function display($tpl=null)
	{
		// Get data from the model
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->form			= $this->get('Form');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		//Check if there are no matching items
		if(!count($this->items)){
			MolajoFactory::getApplication()->enqueueMessage(
				JText::_('COM_INSTALLER_MSG_MANAGE_NOEXTENSION')
			);
		}

		// Display the view
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		$canDo	= InstallerHelper::getActions();
		if ($canDo->get('core.edit.state')) {
			MolajoToolbarHelper::publish('manage.publish', 'JTOOLBAR_ENABLE');
			MolajoToolbarHelper::unpublish('manage.unpublish', 'JTOOLBAR_DISABLE');
			MolajoToolbarHelper::divider();
		}
		MolajoToolbarHelper::custom('manage.refresh', 'refresh', 'refresh', 'JTOOLBAR_REFRESH_CACHE',true);
		MolajoToolbarHelper::divider();
		if ($canDo->get('core.delete')) {
			MolajoToolbarHelper::deleteList('', 'manage.remove', 'JTOOLBAR_UNINSTALL');
			MolajoToolbarHelper::divider();
		}
		parent::addToolbar();
		MolajoToolbarHelper::help('JHELP_EXTENSIONS_EXTENSION_MANAGER_MANAGE');
	}
}
