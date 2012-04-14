<?php
/**
 * @package   Molajo
 * @copyright     Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application\MVC\Model;

use Molajo\Application\Services;

defined('MOLAJO') or die;

/**
 * Load
 *
 * @package   Molajo
 * @subpackage  Model
 * @since       1.0
 */
Class LoadModel extends Model
{
	/**
	 * __construct
	 *
	 * Constructor.
	 *
	 * @param  $id
	 * @since  1.0
	 */
	public function __construct($table = null, $id = null, $path = null)
	{
		return parent::__construct($table, $id, $path);
	}

	/**
	 * load
	 *
	 * Method to load a specific item from a specific model.
	 * Creates and runs the database query, allows for additional data,
	 * and returns integrated data as the item requested
	 *
	 * @return  object
	 * @since   1.0
	 */
	public function load()
	{
		$this->setLoadQuery()
			->runLoadQuery()
			->getLoadAdditionalData();

		return $this->query_results;
	}

	/**
	 * setLoadQuery
	 *
	 * Retrieve all elements of the specific table for a specific item
	 *
	 * @return  object
	 * @since   1.0
	 */
	protected function setLoadQuery()
	{
		$this->query = $this->db->getQuery(true);

		$this->query->select(' * ');
		$this->query->from($this->db->qn($this->table_name));
		$this->query->where($this->primary_key
			. ' = '
			. $this->db->q($this->id));

		$this->db->setQuery($this->query->__toString());

		return $this;
	}

	/**
	 * runLoadQuery
	 *
	 * Execute query and returns an associative array of data elements
	 *
	 * @return  array
	 * @since   1.0
	 */
	protected function runLoadQuery()
	{
		$this->query_results = $this->db->loadAssoc();

		if (empty($this->query_results)) {

			$this->query_results = array();

			/** User Table Columns */
			$columns = $this->getFieldNames();

			for ($i = 0; $i < count($columns); $i++) {
				$this->query_results[$columns[$i]] = '';
			}
		}

		if (key_exists('custom_fields', $this->query_results)
			&& is_array($this->query_results['custom_fields'])
		) {
			$registry = Services::Registry()->initialise();
			$registry->loadString($this->query_results['custom_fields']);
			$this->query_results['custom_fields'] = (string)$registry;
		}

		if (key_exists('parameters', $this->query_results)
			&& is_array($this->query_results['parameters'])
		) {
			$registry = Services::Registry()->initialise();
			$registry->loadString($this->query_results['parameters']);
			$this->query_results['parameters'] = (string)$registry;
		}

		if (key_exists('metadata', $this->query_results)
			&& is_array($this->query_results['metadata'])
		) {
			$registry = Services::Registry()->initialise();
			$registry->loadString($this->query_results['metadata']);
			$this->query_results['metadata'] = (string)$registry;
		}

		return $this;
	}

	/**
	 * getAdditionalData
	 *
	 * Method to append additional data elements needed to the standard
	 * array of elements provided by the data source
	 *
	 * @return array
	 * @since  1.0
	 */
	protected function getLoadAdditionalData()
	{
		$children = $this->table_xml->children;

		if (count($children->child) > 0) {

			foreach ($children->child as $child) {

				$name = (string)$child['name'];

				$a = new TableModel($name);

				$join = (string)$child['join'];
				$joinArray = explode(';', $join);

				foreach ($joinArray as $where) {

					$whereArray = explode(':', (string)$where);

					$targetField = $whereArray[1];
					$sourceField = $whereArray[0];

					$a->query->where($a->db->qn($targetField)
						. ' = '
						. (int)$this->query_results[$sourceField]);
				}

				$this->query_results['Model\\' . $name] = $a->loadObjectList();
			}
		}

		/** return array of primary query and additional data elements */
		return $this;
	}
}

