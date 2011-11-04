<?php
/**
 * @version		$Id: view.html.php 21655 2011-06-23 05:43:24Z chdemko $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * The HTML Menus Menu Menus View.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_menus
 * @version		1.6
 */
class MenusViewMenus extends JView
{
	protected $items;
	protected $modules;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->items		= $this->get('Items');
		$this->modules		= $this->get('Modules');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			MolajoError::raiseError(500, implode("\n", $errors));
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
		require_once JPATH_COMPONENT.'/helpers/menus.php';

		$canDo	= MenusHelper::getActions($this->state->get('filter.parent_id'));

		MolajoToolbarHelper::title(MolajoText::_('COM_MENUS_VIEW_MENUS_TITLE'), 'menumgr.png');

		if ($canDo->get('core.create')) {
			MolajoToolbarHelper::addNew('menu.add');
		}
		if ($canDo->get('core.edit')) {
			MolajoToolbarHelper::editList('menu.edit');
		}
		if ($canDo->get('core.delete')) {
			MolajoToolbarHelper::divider();
			MolajoToolbarHelper::deleteList('', 'menus.delete');
		}

		MolajoToolbarHelper::custom('menus.rebuild', 'refresh.png', 'refresh_f2.png', 'JTOOLBAR_REBUILD', false);
		if ($canDo->get('core.admin')) {
			MolajoToolbarHelper::divider();
			MolajoToolbarHelper::preferences('com_menus');
		}
		MolajoToolbarHelper::divider();
		MolajoToolbarHelper::help('JHELP_MENUS_MENU_MANAGER');
	}
}
