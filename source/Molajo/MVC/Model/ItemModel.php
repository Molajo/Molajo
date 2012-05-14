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
		if (Services::Registry()->get($this->table_registry_name, 'get_customfields', 0) == 0) {
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
		$customFieldTypes = Services::Registry()->get($this->table_registry_name, 'CustomFieldGroups');

		$retrieval_method = Services::Registry()->get($this->table_registry_name, 'get_customfields');

		/** Process each field namespace  */
		foreach ($customFieldTypes as $customFieldName) {

			/** For this Custom Field Group (ex. Parameters, metadata, etc.) */
			$customFieldName = strtolower($customFieldName);
			$useRegistryName = $this->model_name . ucfirst($customFieldName);

			/** Retrieve Field Definitions from Registry (XML) */
			$fields = Services::Registry()->get($this->table_registry_name, $customFieldName);

			/** See if there are query results for the Custom Field Group */
			if ((is_array($this->query_results) && isset($this->query_results[$customFieldName]))
				|| (is_object($this->query_results) && isset($this->query_results->$customFieldName))
			) {

				if (is_array($this->query_results)) {
					$jsonData = $this->query_results[$customFieldName];
				} else {
					$jsonData = $this->query_results->$customFieldName;
				}

				$data = json_decode($jsonData);

				/** test for application-specific values */
				if (count($data) > 0
					&& (defined('APPLICATION_ID'))) {
					foreach ($data as $key => $value) {

						if ($key == APPLICATION_ID) {
							$data = $value;
							break;
						}
					}
				}

				/** Place queryresults data for custom field group into named pair array */
				$lookup = array();

				if (count($data) > 0) {
					foreach ($data as $key => $value) {
						$lookup[$key] = $value;
					}
				}

			} else {

				$data = array();
				$lookup = array();
			}

			/** Process each of the Custom Field Group Definitions for Query Results or defaults */
			foreach ($fields as $f) {

				$name = $f['name'];
				$name = strtolower($name);

				$default = null;
				if (isset($f['default'])) {
					$default = $f['default'];
				}

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
//echo $useRegistryName.' '. $name.' '.$setValue.'<br /> ';
					Services::Registry()->set($useRegistryName, $name, $setValue);
				}
			}

			if (is_array($this->query_results) && isset($this->query_results[$customFieldName])) {
				unset($this->query_results[$customFieldName]);

			} else if (is_object($this->query_results) && isset($this->query_results->$customFieldName)) {
				unset($this->query_results->$customFieldName);
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

				$results = $m->getData('loadObjectList');

				$this->query_results[$name] = $results;
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
				$this->table_name, $this->row, $this->primary_key);
		} else {
			$stored = $this->db->updateObject(
				$this->table_name, $this->row, $this->primary_key);
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
