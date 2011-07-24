<?php
/**
 * @version		$Id: styles.php 21650 2011-06-23 05:29:17Z chdemko $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of template style records.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_templates
 * @since		1.6
 */
class TemplatesModelStyles extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'a.id',
				'title', 'a.title',
				'application_id', 'a.application_id',
				'template', 'a.template',
				'home', 'a.home',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$template = $this->getUserStateFromRequest($this->context.'.filter.template', 'filter_template', '0', 'cmd');
		$this->setState('filter.template', $template);

		$applicationId = $this->getUserStateFromRequest($this->context.'.filter.application_id', 'filter_application_id', null);
		$this->setState('filter.application_id', $applicationId);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_templates');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.template', 'asc');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 *
	 * @return	string		A store id.
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.template');
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
				'a.id, a.template, a.title, a.home, a.application_id, l.title AS language_title, l.image as image'
			)
		);
		$query->from('`#__template_styles` AS a');

		// Join on menus.
		$query->select('COUNT(m.template_style_id) AS assigned');
		$query->leftjoin('#__menu AS m ON m.template_style_id = a.id');
		$query->group('a.id');

		// Join over the language
		$query->join('LEFT', '`#__languages` AS l ON l.lang_code = a.home');

		// Filter by extension enabled
		$query->join('LEFT', '`#__extensions` AS e ON e.element = a.template');
		$query->where('e.enabled = 1');

		// Filter by template.
		if ($template = $this->getState('filter.template')) {
			$query->where('a.template = '.$db->quote($template));
		}

		// Filter by application.
		$applicationId = $this->getState('filter.application_id');
		if (is_numeric($applicationId)) {
			$query->where('a.application_id = '.(int) $applicationId);
		}

		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = '.(int) substr($search, 3));
			} else {
				$search = $db->Quote('%'.$db->getEscaped($search, true).'%');
				$query->where('a.template LIKE '.$search.' OR a.title LIKE '.$search);
			}
		}

		// Add the list ordering clause.
		$query->order($db->getEscaped($this->getState('list.ordering', 'a.name')).' '.$db->getEscaped($this->getState('list.direction', 'ASC')));


		return $query;
	}
}
