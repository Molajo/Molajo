<?php
/**
 * @version		$Id: view.html.php 21655 2011-06-23 05:43:24Z chdemko $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// Check to ensure this file is included in Molajo
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * HTML Languages View class for the Languages component
 *
 * @package		Joomla.Administrator
 * @subpackage	com_languages
 * * * @since		1.0
 */
class LanguagesViewLanguages extends JView
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	function display($tpl = null)
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
	 * @since	1.0
	 */
	protected function addToolbar()
	{
		require_once JPATH_COMPONENT.'/helpers/languages.php';
		$canDo	= LanguagesHelper::getActions();

		MolajoToolbarHelper::title(JText::_('COM_LANGUAGES_VIEW_LANGUAGES_TITLE'), 'langmanager.png');

		if ($canDo->get('core.create')) {
			MolajoToolbarHelper::addNew('language.add');
		}

		if ($canDo->get('core.edit')) {
			MolajoToolbarHelper::editList('language.edit');
			MolajoToolbarHelper::divider();
		}

		if ($canDo->get('core.edit.state')) {
			if ($this->state->get('filter.published') != 2) {
				MolajoToolbarHelper::publishList('languages.publish');
				MolajoToolbarHelper::unpublishList('languages.unpublish');
			}
		}

		if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete')) {
			MolajoToolbarHelper::deleteList('', 'languages.delete','JTOOLBAR_EMPTY_TRASH');
			MolajoToolbarHelper::divider();
		} else if ($canDo->get('core.edit.state')) {
			MolajoToolbarHelper::trash('languages.trash');
			MolajoToolbarHelper::divider();
		}

		if ($canDo->get('core.admin')) {
			MolajoToolbarHelper::preferences('com_languages');
			MolajoToolbarHelper::divider();
		}

		MolajoToolbarHelper::help('JHELP_EXTENSIONS_LANGUAGE_MANAGER_CONTENT');
	}
}
