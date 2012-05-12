<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\MVC\Model;

use Molajo\Application;
use Molajo\Service\Services;
use Molajo\Service\Services\Configuration\ConfigurationService;

defined('MOLAJO') or die;

/**
 * Item
 *
 * Handles basic CRUD operations for a specific type of data
 *
 * Data can be extended within the model through use of fields and children
 *
 * @package     Molajo
 * @subpackage  Model
 * @since       1.0
 */
class ItemModel extends Model
{
	/**
	 * Constructor.
	 *
	 * @since  1.0
	 */
	public function __construct()
	{
		return parent::__construct();
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
		/** Base query */
		$this->setLoadQuery();

		/** Add ACL Checking */
		if (Services::Registry()->get($this->table_registry_name, 'check_view_level_access', 0) == 1) {
			$this->addACLCheck();
		}

		/** Joins */
		if (Services::Registry()->get($this->table_registry_name, 'use_special_joins', 0) == 1) {
			$this->useSpecialJoins();
		}

		/** Execute Query */
		$this->runLoadQuery();

		/** Load Special Fields in Registry */
		if (Services::Registry()->get($this->table_registry_name, 'get_custom_fields', 0) == 0) {
		} else {
			$this->addCustomFields();
		}

		/** Retrieve Child Objects  */
		if (Services::Registry()->get($this->table_registry_name, 'get_item_children', 0) == 1) {
			$this->addItemChildren();
		}

		/** Return Query Results */
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
		if ($this->query->select == null) {
			$columns = Services::Registry()->get($this->table_registry_name, 'Fields');

			foreach ($columns as $column) {
				$this->query->select($this->db->qn($this->primary_prefix . '.' . $column['name']));
			}
		}

		if ($this->query->from == null) {
			$this->query->from(
				$this->db->qn($this->table_name)
					. ' as '
					. $this->db->qn($this->primary_prefix)
			);
		}

		if ($this->query->where == null) {
			if ((int)$this->id > 0) {
				$this->query->where(
					$this->db->qn($this->primary_prefix . '.' . $this->primary_key)
						. ' = ' . $this->db->q($this->id)
				);
			} else {
				$this->query->where(
					$this->db->qn($this->primary_prefix . '.' . $this->name_key)
						. ' = ' . $this->db->q($this->id_name)
				);
			}
		}

		return $this;
	}

	/**
	 * addACLCheck
	 *
	 * Add ACL checking to the Query
	 *
	 * @return  object
	 * @since   1.0
	 */
	protected function addACLCheck()
	{
		Services::Authorisation()
			->setQueryViewAccess(
			$this->query,
			$this->db,
			array('join_to_prefix' => $this->primary_prefix,
				'join_to_primary_key' => $this->primary_key,
				'catalog_prefix' => 'b_catalog',
				'select' => true
			)
		);

		return $this;
	}

	/**
	 * useSpecialJoins
	 *
	 * Use joins defined in table xml to extend model
	 *
	 * @return  array
	 * @since   1.0
	 */
	protected function useSpecialJoins()
	{
		$joins = Services::Registry()->get($this->table_registry_name, 'joins');

		if (count($joins) > 0) {

			foreach ($joins as $join) {

				$join_table = $join['table'];
				$alias = $join['alias'];
				$select = $join['select'];
				$joinTo = $join['jointo'];
				$joinWith = $join['joinwith'];

				/* Join to table */
				if (trim($alias) == '') {
					$alias = $join_table;
				}

				$this->query->from($this->db->qn($join_table) . ' as ' . $this->db->qn($alias));

				/* Select fields */
				$selectArray = explode(',', $select);

				if (count($selectArray) > 0) {
					foreach ($selectArray as $selectItem) {
						$this->query->select($this->db->qn($alias . '.' . $selectItem));
					}
				}

				/* joinTo and joinWith Fields */
				$joinToArray = explode(',', $joinTo);
				$joinWithArray = explode(',', $joinWith);

				if (count($joinToArray) > 0) {
					$i = 0;
					foreach ($joinToArray as $joinToItem) {
						$with = $joinWithArray[$i];
						$hasAlias = explode('.', $with);
						if (count($hasAlias) == 1) {
							$withJoin = 'a.' . $with;
						} else {
							$withJoin = $with;
						}
						$this->query->where($this->db->qn($alias . '.' . $joinToItem)
							. ' = '
							. $this->db->qn($with));
						$i++;
					}
				}
			}
		}
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
		/** Run the query */
		$this->db->setQuery($this->query->__toString());

		$this->query_results = $this->db->loadAssoc();

		/** Record Not found */
		if (empty($this->query_results)) {

			$this->query_results = array();

			/** Table Columns */
			$columns = Services::Registry()->get($this->table_registry_name, 'Fields');

			foreach ($columns as $column) {
				$this->query_results[$column['name']] = '';
			}
		}

		return $this;
	}

	/**
	 * addCustomFields
	 *
	 * Populate the custom fields defined by the Table xml with query results
	 *
	 * @return  object
	 * @since   1.0
	 */
	protected function addCustomFields()
	{
		$customFields = Services::Configuration()->get($this->table_registry_name, 'Customfields');
		echo '<pre>';
		var_dump($customFields);
		die;
		if (count($customFields) > 0) {
		} else {
			return $this;
		}

		/**
		echo '<pre>';
		var_dump($fields);
		echo '</pre>';
		 */

		$retrieval_method = Services::Configuration()->get($this->table_registry_name, 'get_custom_fields');

		/** Process each field namespace  */
		foreach ($fields as $ns) {

			$field_name = $ns['name'];

			$namespace = $ns['registry'];

			if ((is_array($this->query_results) && isset($this->query_results[$field_name]))
				|| (is_object($this->query_results) && isset($this->query_results->$field_name))
			) {

				if (is_array($this->query_results)) {
					$jsonData = $this->query_results[$field_name];
				} else {
					$jsonData = $this->query_results->$field_name;
				}

				$custom_field = json_decode($jsonData);

				$fieldArray = array();

				/** Place field names into named pair array */
				$lookup = array();

				if (count($custom_field) > 0) {
					foreach ($custom_field as $key => $value) {
						$lookup[$key] = $value;
					}
				}

				if (count($ns->$fieldArray) > 0) {

					foreach ($ns->$fieldArray as $f) {

						$name = $f['name'];
						$name = strtolower($name);
						$dataType = $f['filter'];
						$null = $f['null'];
						$default = $f['default'];
						$values = $f['values'];

						if ($default == '') {
							$default = null;
						}

						/** Use value, if exists, or defined default */
						if (isset($lookup[$name])) {
							$setValue = $lookup[$name];
						} else {
							$setValue = $default;
						}

						/** Filter Input and Save the Registry */
						//$set = $this->filterInput($name, $set, $dataType, $null, $default);

						if ($retrieval_method == 2) {
							if (is_array($this->query_results)) {
								$this->query_results[$name] = $setValue;
							} else {
								$this->query_results->$name = $setValue;
							}
						} else {
							Services::Registry()->set($namespace, $name, $setValue);
						}
					}
				}
			}
		}

		return $this;
	}

	/**
	 * filterInput
	 *
	 * @param   string  $name         Name of input field
	 * @param   string  $field_value  Value of input field
	 * @param   string  $dataType     Datatype of input field
	 * @param   int     $null         0 or 1 - is null allowed
	 * @param   string  $default      Default value, optional
	 *
	 * @return  mixed
	 * @since   1.0
	 */
	protected function filterInput(
		$name, $value, $dataType, $null = null, $default = null)
	{

		try {
			$value = Services::Filter()
				->filter(
				$value,
				$dataType,
				$null,
				$default
			);

		} catch (\Exception $e) {
			//todo: errors
			echo $e->getMessage() . ' ' . $name;
		}

		return $value;
	}

	/**
	 * addItemChildren
	 *
	 * Method to append additional data elements needed to the standard
	 * array of elements provided by the data source
	 *
	 * @return array
	 * @since  1.0
	 */
	protected function addItemChildren()
	{
		$children = Services::Registry()->get($this->table_registry_name, 'Children');

		if (count($children) > 0) {

			foreach ($children as $child) {

				$name = $child['name'];

				$m = Application::Controller()->connect($name);

				$join = $child['join'];
				$joinArray = explode(';', $join);

				foreach ($joinArray as $where) {

					$whereArray = explode(':', $where);

					$targetField = $whereArray[1];
					$sourceField = $whereArray[0];

					$m->model->query->where($m->model->db->qn($targetField)
						. ' = '
						. (int)$this->query_results[$sourceField]);
				}
				$this->query_results['Model\\' . $name] = $m->getData('loadObjectList');
			}
		}

		/** return array containing primary query and additional data elements */
		return $this;
	}

	/**
	 * store
	 *
	 * Method to store a row (insert: no PK; update: PK) in the database.
	 *
	 * @param   boolean True to update fields even if they are null.
	 *
	 * @return  boolean  True on success.
	 * @since   1.0
	 */
	public function store()
	{
		/**
		echo '<pre>';
		var_dump($this->row);
		echo '</pre>';
		 */
		if ((int)$this->id == 0) {
			$stored = $this->db->insertObject(
				Services::Registry()->get($this->table_registry_name, 'Table'), $this->row, Services::Registry()->get($this->table_registry_name, 'primary_key'));
		} else {
			$stored = $this->db->updateObject(
				Services::Registry()->get($this->table_registry_name, 'Table'), $this->row, Services::Registry()->get($this->table_registry_name, 'primary_key'));
		}

		if ($stored) {

		} else {

//			throw new \Exception(
//				. ' '. $this->db->getErrorMsg()
//			);
		}
		/**
		if ($this->_locked) {
		$this->_unlock();
		}
		 */

		return true;
	}
}
