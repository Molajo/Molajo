<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\MVC\Model;

use Molajo\Service\Services;

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
		if ($this->add_acl_check == true) {
			$this->addACLCheck();
		}

		/** Joins */
		if ($this->use_special_joins == true) {
			$this->addSpecialJoins();
		}

		/** Execute Query */
		$this->runLoadQuery();

		/** Load Special Fields in Registry */
		if ($this->get_special_fields == 0) {
		} else {
			$this->addSpecialFields();
		}

		/** Retrieve Child Objects  */
		if ($this->get_item_children == true) {
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
			$columns = $this->getFieldNames();
			for ($i = 0; $i < count($columns); $i++) {
				$this->query->select($this->db->qn('a.' . $columns[$i]));
			}
		}

		if ($this->query->from == null) {
			$this->query->from($this->db->qn($this->table_name) . ' as a');
		}

		if ($this->query->where == null) {
			if ((int)$this->id > 0) {
				$this->query->where(
					$this->db->qn('a.' . $this->primary_key)
						. ' = '
						. $this->db->q($this->id));
			} else {
				$this->query->where(
					$this->db->qn('a.' . $this->name_field)
						. ' = '
						. $this->db->q($this->id_name));
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
			array('join_to_prefix' => 'a',
				'join_to_primary_key' => $this->primary_key,
				'catalog_prefix' => 'b_catalog',
				'select' => true
			)
		);

		return $this;
	}

	/**
	 * addSpecialJoins
	 *
	 * Use joins defined in table xml to extend model
	 *
	 * @return  array
	 * @since   1.0
	 */
	protected function addSpecialJoins()
	{
		$joins = $this->table_xml->joins;

		if (count($joins->join) > 0) {

			foreach ($joins->join as $join) {

				$join_table = (string)$join['table'];
				$alias = (string)$join['alias'];
				$select = (string)$join['select'];
				$joinTo = (string)$join['jointo'];
				$joinWith = (string)$join['joinwith'];

				/* Join to table */
				if (trim($alias) == '') {
					$alias = $join_table;
				}

				$this->query->from($this->db->qn($join_table) . ' as ' . $alias);

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
						$this->query->where($this->db->qn($alias . '.' . $joinToItem) . ' = ' . $this->db->qn($with));
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
			$columns = $this->getFieldNames();

			for ($i = 0; $i < count($columns); $i++) {
				$this->query_results[$columns[$i]] = '';
			}
		}

		return $this;
	}

	/**
	 * addSpecialFields
	 *
	 * Method used in load sequence to optionally expand special fields
	 * for Item, either into the Registry or so that the fields can be used
	 * normally
	 *
	 * @return  array
	 * @since   1.0
	 */
	protected function addSpecialFields()
	{
		$fields = $this->table_xml->fields;
		/**
		echo '<pre>';
		var_dump($fields);
		echo '</pre>';
		 */
		if (count($fields->field) > 0) {

			/** Process each field namespace  */
			foreach ($fields->field as $ns) {

				$field_name = (string)$ns['name'];
				$namespace = (string)$ns['registry'];

				if (isset($this->query_results[$field_name])) {

					/** Extract custom fields from JSON */
					$jsonData = $this->query_results[$field_name];
					$data = json_decode($jsonData);

					$elementArray = array();

					/** Place field names into named pair array */
					$lookup = array();

					if (count($data) > 0) {
						foreach ($data as $key => $value) {
							$lookup[$key] = $value;
						}
					}

					if (count($ns->element) > 0) {

						foreach ($ns->element as $element) {

							$name = (string)$element['name'];
							$name = strtolower($name);
							$dataType = (string)$element['filter'];
							$null = (string)$element['null'];
							$default = (string)$element['default'];
							$values = (string)$element['values'];

							if ($default == '') {
								$default = null;
							}

							/** Use value, if exists, or defined default */
							if (isset($lookup[$name])) {
								$set = $lookup[$name];
							} else {
								$set = $default;
							}

							/** Filter Input and Save the Registry */
							//$set = $this->filterInput($name, $set, $dataType, $null, $default);

							if ($this->get_special_fields == 2) {
								$this->query_results[$name] = $set;
							} else {
								Services::Registry()->set($namespace, $name, $set);
							}
						}
					}
				}
			}
		}
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
		$children = $this->table_xml->children;

		if (count($children->child) > 0) {

			foreach ($children->child as $child) {

				$name = (string)$child['name'];

				$a = Services::Model()->connect($name);

				$join = (string)$child['join'];
				$joinArray = explode(';', $join);

				foreach ($joinArray as $where) {

					$whereArray = explode(':', (string)$where);

					$targetField = $whereArray[1];
					$sourceField = $whereArray[0];

					$a->model->query->where($a->model->db->qn($targetField)
						. ' = '
						. (int)$this->query_results[$sourceField]);
				}

				$this->query_results['Model\\' . $name] = $a->execute('loadObjectList');
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
