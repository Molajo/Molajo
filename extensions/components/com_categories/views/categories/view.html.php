<?php
/**
 * @version		$Id: view.html.php 21655 2011-06-23 05:43:24Z chdemko $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * Categories view class for the Category package.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_categories
 * * * @since		1.0
 */
class CategoriesViewCategories extends JView
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			MolajoError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Preprocess the list of items to find ordering divisions.
		foreach ($this->items as &$item) {
			$this->ordering[$item->parent_id][] = $item->id;
		}

		// Levels filter.
		$options	= array();
		$options[]	= MolajoHTML::_('select.option', '1', MolajoText::_('J1'));
		$options[]	= MolajoHTML::_('select.option', '2', MolajoText::_('J2'));
		$options[]	= MolajoHTML::_('select.option', '3', MolajoText::_('J3'));
		$options[]	= MolajoHTML::_('select.option', '4', MolajoText::_('J4'));
		$options[]	= MolajoHTML::_('select.option', '5', MolajoText::_('J5'));
		$options[]	= MolajoHTML::_('select.option', '6', MolajoText::_('J6'));
		$options[]	= MolajoHTML::_('select.option', '7', MolajoText::_('J7'));
		$options[]	= MolajoHTML::_('select.option', '8', MolajoText::_('J8'));
		$options[]	= MolajoHTML::_('select.option', '9', MolajoText::_('J9'));
		$options[]	= MolajoHTML::_('select.option', '10', MolajoText::_('J10'));

		$this->assign('f_levels', $options);

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
		// Initialise variables.
		$categoryId	= $this->state->get('filter.category_id');
		$component	= $this->state->get('filter.component');
		$section	= $this->state->get('filter.section');
		$canDo		= null;

		// Avoid nonsense situation.
		if ($component == 'com_categories') {
			return;
		}

		// Need to load the menu language file as mod_menu hasn't been loaded yet.
		$lang = MolajoFactory::getLanguage();
			$lang->load($component, JPATH_BASE, null, false, false)
		||	$lang->load($component, JPATH_ADMINISTRATOR.'/components/'.$component, null, false, false)
		||	$lang->load($component, JPATH_BASE, $lang->getDefault(), false, false)
		||	$lang->load($component, JPATH_ADMINISTRATOR.'/components/'.$component, $lang->getDefault(), false, false);

 		// Load the category helper.
		require_once JPATH_COMPONENT.'/helpers/categories.php';

		// Get the results for each action.
		$canDo = CategoriesHelper::getActions($component, $categoryId);

		// If a component categories title string is present, let's use it.
		if ($lang->hasKey($component_title_key = strtoupper($component.($section?"_$section":'')).'_CATEGORIES_TITLE')) {
			$title = MolajoText::_($component_title_key);
		}
		// Else if the component section string exits, let's use it
		elseif ($lang->hasKey($component_section_key = strtoupper($component.($section?"_$section":'')))) {
			$title = MolajoText::sprintf( 'COM_CATEGORIES_CATEGORIES_TITLE', $this->escape(MolajoText::_($component_section_key)));
		}
		// Else use the base title
		else {
			$title = MolajoText::_('COM_CATEGORIES_CATEGORIES_BASE_TITLE');
		}

		// Load specific css component
		MolajoHTML::_('stylesheet',$component.'/administrator/categories.css', array(), true);

		// Prepare the toolbar.
		MolajoToolbarHelper::title($title, 'categories '.substr($component,4).($section?"-$section":'').'-categories');

		if ($canDo->get('core.create')) {
			 MolajoToolbarHelper::addNew('category.add');
		}

		if ($canDo->get('core.edit' ) || $canDo->get('core.edit.own')) {
			MolajoToolbarHelper::editList('category.edit');
			MolajoToolbarHelper::divider();
		}

		if ($canDo->get('core.edit.state')) {
			MolajoToolbarHelper::publish('categories.publish');
			MolajoToolbarHelper::unpublish('categories.unpublish');
			MolajoToolbarHelper::divider();
			MolajoToolbarHelper::archiveList('categories.archive');
		}

		if (MolajoFactory::getUser()->authorise('core.admin')) {
			MolajoToolbarHelper::checkin('categories.checkin');
		}

		if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete', $component)) {
			MolajoToolbarHelper::deleteList('', 'categories.delete', 'JTOOLBAR_EMPTY_TRASH');
		}
		else if ($canDo->get('core.edit.state')) {
			MolajoToolbarHelper::trash('categories.trash');
			MolajoToolbarHelper::divider();
		}

		if ($canDo->get('core.admin')) {
			MolajoToolbarHelper::custom('categories.rebuild', 'refresh.png', 'refresh_f2.png', 'JTOOLBAR_REBUILD', false);
			MolajoToolbarHelper::preferences($component);
			MolajoToolbarHelper::divider();
		}

		// Compute the ref_key if it does exist in the component
		if (!$lang->hasKey($ref_key = strtoupper($component.($section?"_$section":'')).'_CATEGORIES_HELP_KEY')) {
			$ref_key = 'JHELP_COMPONENTS_'.strtoupper(substr($component,4).($section?"_$section":'')).'_CATEGORIES';
		}

		// Get help for the categories view for the component by
		// -remotely searching in a language defined dedicated URL: *component*_HELP_URL
		// -locally  searching in a component help file if helpURL param exists in the component and is set to ''
		// -remotely searching in a component URL if helpURL param exists in the component and is NOT set to ''
		if ($lang->hasKey($lang_help_url = strtoupper($component).'_HELP_URL')) {
			$debug = $lang->setDebug(false);
			$url = MolajoText::_($lang_help_url);
			$lang->setDebug($debug);
		}
		else {
			$url = null;
		}
		MolajoToolbarHelper::help($ref_key, JComponentHelper::getParameters( $component )->exists('helpURL'), $url);
	}
}
