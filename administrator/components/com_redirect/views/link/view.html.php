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
 * View to edit a redirect link.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_redirect
 * * * @since		1.0
 */
class RedirectViewLink extends JView
{
	protected $item;
	protected $form;
	protected $state;

	/**
	 * Display the view
	 *
	 * @since	1.6
	 */
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
		JRequest::setVar('hidemainmenu', true);

		$user		= MolajoFactory::getUser();
		$isNew		= ($this->item->id == 0);
		$canDo		= RedirectHelper::getActions();

		MolajoToolbarHelper::title(JText::_('COM_REDIRECT_MANAGER_LINK'), 'redirect');

		// If not checked out, can save the item.
		if ($canDo->get('core.edit')) {
			MolajoToolbarHelper::apply('link.apply');
			MolajoToolbarHelper::save('link.save');
		}

		// This component does not support Save as Copy due to uniqueness checks.
		// While it can be done, it causes too much confusion if the user does
		// not change the Old URL.

		if ($canDo->get('core.edit') && $canDo->get('core.create')) {
			MolajoToolbarHelper::save2new('link.save2new');
		}

		if (empty($this->item->id)) {
			MolajoToolbarHelper::cancel('link.cancel');
		} else {
			MolajoToolbarHelper::cancel('link.cancel', 'JTOOLBAR_CLOSE');
		}

		MolajoToolbarHelper::help('JHELP_COMPONENTS_REDIRECT_MANAGER_EDIT');
	}
}
