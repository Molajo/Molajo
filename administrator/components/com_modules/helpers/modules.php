<?php
/**
 * @version		$Id: modules.php 21670 2011-06-24 08:11:47Z chdemko $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * Modules component helper.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_modules
 * * * @since		1.0
 */
abstract class ModulesHelper
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param	string	The name of the active view.
	 */
	public static function addSubmenu($vName)
	{
		// Not used in this component.
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return	JObject
	 */
	public static function getActions()
	{
		$user	= MolajoFactory::getUser();
		$result	= new JObject;

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action) {
			$result->set($action, $user->authorise($action, 'com_modules'));
		}

		return $result;
	}

	/**
	 * Get a list of filter options for the state of a module.
	 *
	 * @return	array	An array of JHtmlOption elements.
	 */
	public static function getStateOptions()
	{
		// Build the filter options.
		$options	= array();
		$options[]	= JHtml::_('select.option',	'1',	MolajoText::_('JPUBLISHED'));
		$options[]	= JHtml::_('select.option',	'0',	MolajoText::_('JUNPUBLISHED'));
		$options[]	= JHtml::_('select.option',	'-2',	MolajoText::_('JTRASHED'));
		return $options;
	}

	/**
	 * Get a list of filter options for the application applications.
	 *
	 * @return	array	An array of JHtmlOption elements.
	 */
	public static function getApplicationOptions()
	{
		// Build the filter options.
		$options	= array();
		$options[]	= JHtml::_('select.option', '0', MolajoText::_('JSITE'));
		$options[]	= JHtml::_('select.option', '1', MolajoText::_('JADMINISTRATOR'));
		return $options;
	}

	static function getPositions($applicationId)
	{
		jimport('joomla.filesystem.folder');

		$db		= MolajoFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select('DISTINCT(position)');
		$query->from('#__modules');
		$query->where('`application_id` = '.(int) $applicationId);
		$query->order('position');

		$db->setQuery($query);
		$positions = $db->loadResultArray();
		$positions = (is_array($positions)) ? $positions : array();

		if ($error = $db->getErrorMsg()) {
			JError::raiseWarning(500, $error);
			return;
		}

		// Build the list
		$options = array();
		foreach ($positions as $position) {
			$options[]	= JHtml::_('select.option', $position, $position);
		}
		return $options;
	}

	public static function getTemplates($applicationId = 0, $state = '', $template='')
	{
		$db = MolajoFactory::getDbo();
		// Get the database object and a new query object.
		$query	= $db->getQuery(true);

		// Build the query.
		$query->select('element, name, enabled');
		$query->from('#__extensions');
		$query->where('application_id = '.(int) $applicationId);
		$query->where('type = '.$db->quote('template'));
		if ($state!='') {
			$query->where('enabled = '.$db->quote($state));
		}
		if ($template!='') {
			$query->where('element = '.$db->quote($template));
		}

		// Set the query and load the templates.
		$db->setQuery($query);
		$templates = $db->loadObjectList('element');
		return $templates;
	}

	/**
	 * Get a list of the unique modules installed in the application application.
	 *
	 * @param	int		The application id.
	 *
	 * @return	array
	 */
	public static function getModules($applicationId)
	{
		$db		= MolajoFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select('element AS value, name AS text');
		$query->from('#__extensions as e');
		$query->where('e.`application_id` = '.(int)$applicationId);
		$query->where('`type` = '.$db->quote('module'));
		$query->where('`enabled` = 1');
		$query->leftJoin('#__modules as m ON m.module=e.element AND m.application_id=e.application_id');
		$query->where('m.module IS NOT NULL');
		$query->group('element');		

		$db->setQuery($query);
		$modules = $db->loadObjectList();
		$lang = MolajoFactory::getLanguage();
		foreach ($modules as $i=>$module) {
			$extension = $module->value;
			$path = $applicationId ? JPATH_ADMINISTRATOR : JPATH_SITE;
			$source = $path . "/modules/$extension";
				$lang->load("$extension.sys", $path, null, false, false)
			||	$lang->load("$extension.sys", $source, null, false, false)
			||	$lang->load("$extension.sys", $path, $lang->getDefault(), false, false)
			||	$lang->load("$extension.sys", $source, $lang->getDefault(), false, false);
			$modules[$i]->text = MolajoText::_($module->text);
		}
		JArrayHelper::sortObjects($modules, 'text', 1, true, $lang->getLocale());
		return $modules;
	}

	/**
	 * Get a list of the assignment options for modules to menus.
	 *
	 * @param	int		The application id.
	 *
	 * @return	array
	 */
	public static function getAssignmentOptions($applicationId)
	{
		$options = array();
		$options[] = JHtml::_('select.option', '0', 'COM_MODULES_OPTION_MENU_ALL');
		$options[] = JHtml::_('select.option', '-', 'COM_MODULES_OPTION_MENU_NONE');

		if ($applicationId == 0) {
			$options[] = JHtml::_('select.option', '1', 'COM_MODULES_OPTION_MENU_INCLUDE');
			$options[] = JHtml::_('select.option', '-1', 'COM_MODULES_OPTION_MENU_EXCLUDE');
		}

		return $options;
	}
}
