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
 * View class for a list of plugins.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_plugins
 * @since		1.5
 */
class PluginsViewPlugins extends JView
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
		$canDo	= PluginsHelper::getActions();

		MolajoToolbarHelper::title(JText::_('COM_PLUGINS_MANAGER_PLUGINS'), 'plugin');

		if ($canDo->get('core.edit')) {
			MolajoToolbarHelper::editList('plugin.edit');
		}

		if ($canDo->get('core.edit.state')) {
			MolajoToolbarHelper::divider();
			MolajoToolbarHelper::publish('plugins.publish', 'JTOOLBAR_ENABLE');
			MolajoToolbarHelper::unpublish('plugins.unpublish', 'JTOOLBAR_DISABLE');
			MolajoToolbarHelper::divider();
			MolajoToolbarHelper::checkin('plugins.checkin');
		}

		if ($canDo->get('core.admin')) {
			MolajoToolbarHelper::divider();
			MolajoToolbarHelper::preferences('com_plugins');
		}
		MolajoToolbarHelper::divider();
		MolajoToolbarHelper::help('JHELP_EXTENSIONS_PLUGIN_MANAGER');
	}
}
