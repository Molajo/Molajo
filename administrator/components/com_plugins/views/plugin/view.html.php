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
 * View to edit a plugin.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_plugins
 * @since		1.5
 */
class PluginsViewPlugin extends JView
{
	protected $item;
	protected $form;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');

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
		$canDo		= PluginsHelper::getActions();

		MolajoToolbarHelper::title(JText::sprintf('COM_PLUGINS_MANAGER_PLUGIN', JText::_($this->item->name)), 'plugin');

		// If not checked out, can save the item.
		if ($canDo->get('core.edit')) {
			MolajoToolbarHelper::apply('plugin.apply');
			MolajoToolbarHelper::save('plugin.save');
		}
		MolajoToolbarHelper::cancel('plugin.cancel', 'JTOOLBAR_CLOSE');
		MolajoToolbarHelper::divider();
		// Get the help information for the plugin item.

		$lang = MolajoFactory::getLanguage();

		$help = $this->get('Help');
		if ($lang->hasKey($help->url)) {
			$debug = $lang->setDebug(false);
			$url = JText::_($help->url);
			$lang->setDebug($debug);
		}
		else {
			$url = null;
		}
		MolajoToolbarHelper::help($help->key, false, $url);
	}
}
