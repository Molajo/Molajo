<?php
/**
 * @version		$Id: select.php 21657 2011-06-23 07:04:38Z chdemko $
 * @package		Joomla.Administrator
 * @subpackage	com_modules
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Module model.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_modules
 * * * @since		1.0
 */
class ModulesModelSelect extends JModelList
{
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.0
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = MolajoFactory::getApplication('administrator');

		// Load the filter state.
		$applicationId = $app->getUserState('com_modules.modules.filter.application_id', 0);
		$this->setState('filter.application_id', (int) $applicationId);

		// Load the parameters.
		$params	= JComponentHelper::getParams('com_modules');
		$this->setState('params', $params);

		// Manually set limits to get all modules.
		$this->setState('list.limit', 0);
		$this->setState('list.start', 0);
		$this->setState('list.ordering', 'a.name');
		$this->setState('list.direction', 'ASC');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string	A prefix for the store id.
	 *
	 * @return	string	A store id.
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.application_id');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.extension_id, a.name, a.element AS module'
			)
		);
		$query->from('`#__extensions` AS a');

		// Filter by module
		$query->where('a.type = '.$db->Quote('module'));

		// Filter by application.
		$applicationId = $this->getState('filter.application_id');
		$query->where('a.application_id = '.(int) $applicationId);

		// Filter by enabled
		$query->where('a.enabled = 1');

		// Add the list ordering clause.
		$query->order($db->getEscaped($this->getState('list.ordering', 'a.ordering')).' '.$db->getEscaped($this->getState('list.direction', 'ASC')));


		return $query;
	}

	/**
	 * Method to get a list of items.
	 *
	 * @return	mixed	An array of objects on success, false on failure.
	 */
	public function &getItems()
	{
		// Get the list of items from the database.
		$items = parent::getItems();

		// Initialise variables.
		$application = JApplicationHelper::getApplicationInfo($this->getState('filter.application_id', 0));
		$lang	= MolajoFactory::getLanguage();

		// Loop through the results to add the XML metadata,
		// and load language support.
		foreach ($items as &$item) {
			$path = JPath::clean($application->path.'/modules/'.$item->module.'/'.$item->module.'.xml');
			if (file_exists($path)) {
				$item->xml = simplexml_load_file($path);
			} else {
				$item->xml = null;
			}

					// 1.5 Format; Core files or language packs then
			// 1.6 3PD Extension Support
				$lang->load($item->module.'.sys', $application->path, null, false, false)
			||	$lang->load($item->module.'.sys', $application->path.'/modules/'.$item->module, null, false, false)
			||	$lang->load($item->module.'.sys', $application->path, $lang->getDefault(), false, false)
			||	$lang->load($item->module.'.sys', $application->path.'/modules/'.$item->module, $lang->getDefault(), false, false);
			$item->name	= MolajoText::_($item->name);

			if (isset($item->xml) && $text = trim($item->xml->description)) {
				$item->desc = MolajoText::_($text);
			}
			else {
				$item->desc = MolajoText::_('COM_MODULES_NODESCRIPTION');
			}
		}
		$items = JArrayHelper::sortObjects($items, 'name', 1, true, $lang->getLocale());

		// TODO: Use the cached XML from the extensions table?

		return $items;
	}
}
