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
 * View class for a list of template styles.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_templates
 * * * @since		1.0
 */
class TemplatesViewStyles extends JView
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
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		$state	= $this->get('State');
		$canDo	= TemplatesHelper::getActions();
		$isSite	= ($state->get('filter.application_id') == 0);

		MolajoToolbarHelper::title(JText::_('COM_TEMPLATES_MANAGER_STYLES'), 'thememanager');

		if ($canDo->get('core.edit.state')) {
			MolajoToolbarHelper::makeDefault('styles.setDefault', 'COM_TEMPLATES_TOOLBAR_SET_HOME');
			MolajoToolbarHelper::divider();
		}

		if ($canDo->get('core.edit')) {
			MolajoToolbarHelper::editList('style.edit');
		}
		if ($canDo->get('core.create') && $isSite) {
			MolajoToolbarHelper::addNew('styles.duplicate', 'JTOOLBAR_DUPLICATE');
			MolajoToolbarHelper::divider();
		}

		if ($canDo->get('core.delete') && $isSite) {
			MolajoToolbarHelper::deleteList('', 'styles.delete');
			MolajoToolbarHelper::divider();
		}

		if ($canDo->get('core.admin')) {
			MolajoToolbarHelper::preferences('com_templates');
			MolajoToolbarHelper::divider();
		}
		MolajoToolbarHelper::help('JHELP_EXTENSIONS_TEMPLATE_MANAGER_STYLES');
	}
}
