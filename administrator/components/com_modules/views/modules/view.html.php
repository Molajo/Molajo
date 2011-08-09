<?php
/**
 * @version		$Id: view.html.php 21656 2011-06-23 05:57:14Z chdemko $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list of modules.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_modules
 * * * @since		1.0
 */
class ModulesViewModules extends JView
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
		$state	= $this->get('State');
		$canDo	= ModulesHelper::getActions();

		MolajoToolbarHelper::title(JText::_('COM_MODULES_MANAGER_MODULES'), 'module.png');

		if ($canDo->get('core.create')) {
			//MolajoToolbarHelper::addNew('module.add');
			$bar = MolajoToolbar::getInstance('toolbar');
			$bar->appendButton('Popup', 'new', 'JTOOLBAR_NEW', 'index.php?option=com_modules&amp;view=select&amp;tmpl=component', 850, 400);
		}

		if ($canDo->get('core.edit')) {
			MolajoToolbarHelper::editList('module.edit');
		}

		if ($canDo->get('core.create')) {
			MolajoToolbarHelper::custom('modules.duplicate', 'copy.png', 'copy_f2.png', 'JTOOLBAR_DUPLICATE', true);
		}

		if ($canDo->get('core.edit.state')) {
			MolajoToolbarHelper::divider();
			MolajoToolbarHelper::publish('modules.publish');
			MolajoToolbarHelper::unpublish('modules.unpublish');
			MolajoToolbarHelper::divider();
			MolajoToolbarHelper::checkin('modules.checkin');
		}

		if ($state->get('filter.state') == -2 && $canDo->get('core.delete')) {
			MolajoToolbarHelper::deleteList('', 'modules.delete', 'JTOOLBAR_EMPTY_TRASH');
			MolajoToolbarHelper::divider();
		} else if ($canDo->get('core.edit.state')) {
			MolajoToolbarHelper::trash('modules.trash');
			MolajoToolbarHelper::divider();
		}

		if ($canDo->get('core.admin')) {
			MolajoToolbarHelper::preferences('com_modules');
			MolajoToolbarHelper::divider();
		}
		MolajoToolbarHelper::help('JHELP_EXTENSIONS_MODULE_MANAGER');
	}
}
