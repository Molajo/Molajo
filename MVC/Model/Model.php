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
 * Model
 *
 * Base Molajo Model
 *
 * @package       Molajo
 * @subpackage    Model
 * @since         1.0
 */
class Model
{
	/**
	 * Table registry
	 *
	 * Declared public to retrieve following query in order to identify custom field registries
	 *
	 * @var    object
	 * @since  1.0
	 */
	public $table_registry_name = false;

	/**
	 * Model Name
	 *
	 * Declared public to retrieve following query in order to identify custom field registries
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $model_name = '';

	/**
	 * Database table
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $table_name = '';

	/**
	 * Used internally to retrieve and use names of table fields
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $fields = '';

	/**
	 * Primary key for the database table
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $primary_key = '';

	/**
	 * Value for the primary key
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $id = 0;

	/**
	 * Name key (if existing) for the database table
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $name_key = '';

	/**
	 * Value for the name key
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $id_name = '';

	/**
	 * Primary Prefix
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $primary_prefix;

	/**
	 * Database connection
	 *
	 * Public to access db quoting on query parts
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $db;

	/**
	 * Database query object
	 *
	 * Public to allow setting of partial query values
	 *
	 * @var    object
	 * @since  1.0
	 */
	public $query;

	/**
	 * Used in queries to determine date validity
	 *
	 * Public to access property during query development
	 *
	 * @var    object
	 * @since  1.0
	 */
	public $nullDate;

	/**
	 * Today's CCYY-MM-DD 00:00:00 Used in queries to determine date validity
	 *
	 * Public to access property during query development
	 *
	 * @var    object
	 * @since  1.0
	 */
	public $now;

	/**
	 * Results from queries
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected $query_results;

	/**
	 * Pagination object from display query
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected $pagination;

	/**
	 * Single row for $table
	 *
	 * @var    \stdClass
	 * @since  1.0
	 */
	protected $row;

	/**
	 * List of all data elements in table
	 *
	 * @var    array
	 * @since  1.0
	 */
	protected $table_fields;

	/**
	 * @return  object
	 * @since   1.0
	 */
	public function __construct()
	{
		$this->query_results = array();
		$this->pagination = array();
	}

	/**
	 * Get the current value (or default) of the specified Model property
	 *
	 * @param   string  $key      Property
	 * @param   mixed   $default  Value
	 *
	 * @return  mixed
	 * @since   1.0
	 */
	public function get($key, $default = null)
	{
		return $this->$key;
	}

	/**
	 * Set the value of a Model property
	 *
	 * @param   string  $key    Property
	 * @param   mixed   $value  Value
	 *
	 * @return  mixed
	 * @since   1.0
	 */
	public function set($key, $value = null)
	{
		return $this->$key = $value;
	}

	/**
	 * Return code given message
	 *
	 * @param   string  $code  Numeric value associated with message
	 *
	 * @return  mixed  Array or String
	 * @since   1.0
	 */
	public function getMessageCode($message = null)
	{
		$messageArray = self::get(0);

		$code = array_search($message, $messageArray);

		if ((int)$code == 0) {
			$code = self::DEFAULT_CODE;
		}

		return $code;
	}

	/**
	 * retrieves messages (only available for the Message dbo
	 *
	 * @return  mixed  Array or String or Null
	 * @since   1.0
	 */
	public function getMessages()
	{
		return $this->db->getMessages();
	}

	/**
	 * retrieves JS and CSS assets, metadata for head
	 *
	 * @return  mixed  Array or String or Null
	 * @since   1.0
	 */
	public function getAssets()
	{
		return $this->db->getAssets();
	}

	/**
	 * getFieldNames
	 *
	 * Retrieves column names, only, for the database table
	 *
	 * @return array
	 * @since  1.0
	 */
	public function getFieldNames()
	{
		$fields = array();

		$fieldDefinitions = $this->getFieldDefinitions();

		if (count($fieldDefinitions) > 0) {
			foreach ($fieldDefinitions as $fieldDefinition) {
				$fields[] = $fieldDefinition->Field;
			}
		}

		return $fields;
	}

	/**
	 * getFieldDatatypes
	 *
	 * Retrieves column names and brief datatype
	 *
	 * @return array
	 * @since  1.0
	 */
	public function getFieldDatatypes()
	{
		$fields = array();
		$fieldDefinitions = $this->getFieldDefinitions();

		if (count($fieldDefinitions) > 0) {
			foreach ($fieldDefinitions as $fieldDefinition) {

				$dataType = '';

				/* basic datatype */
				if (stripos($fieldDefinition->Type, 'int') !== false) {
					$dataType = 'int';
				} else if (stripos($fieldDefinition->Type, 'date') !== false) {
					$dataType = 'date';
				} else if (stripos($fieldDefinition->Type, 'text') !== false) {
					$dataType = 'text';
				} else {
					$dataType = 'char';
				}

				/* null */
				if ((strtolower($fieldDefinition->Null)) == 'yes') {
					$dataType .= ',1';
				} else {
					$dataType .= ',0';
				}

				/* default */
				if ((strtolower($fieldDefinition->Extra)) == 'auto_increment') {
					$dataType .= ',auto_increment';
				} else if ((strtolower($fieldDefinition->Default)) == ' ') {
					$dataType .= ', ';
				} else if ((strtolower($fieldDefinition->Default)) == '0') {
					$dataType .= ',0';
				} else if ($fieldDefinition->Default == NULL) {
					$dataType .= ',';
				} else {
					$dataType .= ',' . trim($fieldDefinition->Default);
				}

				/* save it to array */
				$fields[$fieldDefinition->Field] = $dataType;
			}
		}

		return $fields;
	}

	/**
	 * getFieldDefinitions
	 *
	 * Retrieves column names and definitions from the database table
	 *
	 * @return array
	 * @since  1.0
	 */
	public function getFieldDefinitions()
	{
		if ($this->table_name == '') {
			return array();
		}
		return $this->db->getTableColumns($this->table_name, false);
	}

	/**
	 *  Read Methods
	 */

	/**
	 * Method to load a specific item from a specific model.
	 * Creates and runs the database query, allow for additional data,
	 * and returns the integrated data for the item requested
	 *
	 * Implemented by ItemModel, can be overridden by child
	 *
	 * @return  object
	 * @since   1.0
	 */
	public function load()
	{
		return $this->query_results = array();
	}

	/**
	 * setLoadQuery
	 *
	 * Method used in load sequence to create a query object for a
	 * specific item in a specific model
	 *
	 * @return  object
	 * @since   1.0
	 */
	protected function setLoadQuery()
	{
		$this->query = $this->db->getQuery(true);
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
		$joins = Services::Registry()->get($this->table_registry_name, 'Joins');

		if (count($joins) > 0) {

			foreach ($joins as $join) {

				$join_table = $join['table'];
				$alias = $join['alias'];
				$select = $join['select'];
				$joinTo = $join['jointo'];
				$joinWith = $join['joinwith'];

//echo  'JOIN: ' . $join_table . ' ' . $alias . ' ' . $select . ' ' . $joinTo . ' ' . $joinWith . '<br />';

				/* Join to table */
				if (trim($alias) == '') {
					$alias = substr($join_table, 3, strlen($join_table));
				}

				$this->query->from($this->db->qn($join_table) . ' as ' . $this->db->qn($alias));

				/* Select fields */
				if (trim($select) == '') {
					$selectArray = array();
				} else {
					$selectArray = explode(',', $select);
				}

				if (count($selectArray) > 0) {

					foreach ($selectArray as $selectItem) {

						$this->query->select(
							$this->db->qn(trim($alias) . '.' . trim($selectItem))
								. ' as ' .
								$this->db->qn(trim($alias) . '_' . trim($selectItem))
						);
					}
				}

				/* joinTo and joinWith Fields */
				$joinToArray = explode(',', $joinTo);
				$joinWithArray = explode(',', $joinWith);

				if (count($joinToArray) > 0) {

					$i = 0;
					foreach ($joinToArray as $joinToItem) {

						/** join THIS to that */
						$to = $joinToItem;

						if (substr(trim($to), 0, 1) == '{' && substr(trim($to), strlen(trim($to)) - 1, 1) == '}') {
							$temp = substr(trim($to), 1, strlen(trim($to)) - 2);
							$split = explode(',', $temp);
							$value = Services::Registry()->get($split[0], $split[0]);
							if ($value == (int)$value) {
								$whereLeft = (int)$value;
							} else {
								$whereLeft = $this->db->q($value);
							}

						} else if ($to == 'APPLICATION_ID') {
							$whereLeft = APPLICATION_ID;

						} else if ($to == 'SITE_ID') {
							$whereLeft = SITE_ID;


						} else if (is_numeric($to)) {
							$whereLeft = (int)$to;

						} else {

							$hasAlias = explode('.', $to);

							if (count($hasAlias) > 1) {
								$toJoin = trim($hasAlias[0]) . '.' . trim($hasAlias[1]);
							} else {
								$toJoin = trim($alias) . '.' . trim($to);
							}

							$whereLeft = $this->db->qn($toJoin);
						}

						/** join this to THAT */
						$with = $joinWithArray[$i];

						if (substr(trim($with), 0, 1) == '{' && substr(trim($with), strlen(trim($with)) - 1, 1) == '}') {
							$temp = substr(trim($with), 1, strlen(trim($with)) - 2);
							$split = explode('.', $temp);
							$value = Services::Registry()->get($split[0], $split[0]);

							if ($value == (int)$value) {
								$whereRight = (int)$value;
							} else {
								$whereRight = $this->db->q($value);
							}

						} else if ($with == 'APPLICATION_ID') {
							$whereRight = APPLICATION_ID;

						} else if ($with == 'SITE_ID') {
							$whereRight = SITE_ID;

						} else if (is_numeric($with)) {
							$whereRight = (int)$with;

						} else {

							$hasAlias = explode('.', $with);

							if (count($hasAlias) > 1) {
								$withJoin = trim($hasAlias[0]) . '.' . trim($hasAlias[1]);
							} else {
								$withJoin = trim($this->primary_prefix) . '.' . trim($with);
							}

							$whereRight = $this->db->qn($withJoin);
						}

						/** put the where together */
						$this->query->where($whereLeft . ' = ' . $whereRight);

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
	 * Method used in load sequence to execute a query statement
	 * for a specific item, returning the results
	 *
	 * @return  object
	 * @since   1.0
	 */
	protected function runLoadQuery()
	{
		return $this->query_results = array();
	}

	/**
	 * populateCustomFields
	 *
	 * Method used in load sequence to optionally expand special fields
	 * for Item, either into the Registry or so that the fields can be used
	 * normally
	 *
	 * @return array
	 * @since  1.0
	 */
	protected function populateCustomFields()
	{
		return $this->query_results = array();
	}

	/**
	 * addItemChildren
	 *
	 * Method used in load sequence to optionally append additional
	 * query result objects to to a specific item
	 *
	 * @return array
	 * @since  1.0
	 */
	protected function addItemChildren()
	{
		return $this->query_results = array();
	}

	/**
	 * getData
	 *
	 * Used for most view access queries for any model.
	 *
	 * Use load for retrieving a specific item with intent to update.
	 *
	 * Method to establish query criteria, formulate a database query,
	 * run the database query, allow for the addition of more data, and
	 * return the integrated data
	 *
	 * @return  object
	 * @since   1.0
	 */
	public function getData()
	{
		$this->setQuery();

		$this->query_results = $this->runQuery();

		if (empty($this->query_results)) {
			return false;
		}

		$this->query_results = $this->getAdditionalData();

		return $this->query_results;
	}

	/**
	 * setQuery
	 *
	 * Method to create a query object in preparation of running a query
	 *
	 * @return  object
	 * @since   1.0
	 */
	protected function setQuery()
	{
		return;
	}

	/**
	 * runQuery
	 *
	 * Method to execute a prepared and set query statement,
	 * returning the results
	 *
	 * @return  object
	 * @since   1.0
	 */
	public function runQuery()
	{
		return array();
	}

	/**
	 * loadResult
	 *
	 * Single Value Result
	 *
	 * Access by referencing the query results field, directly
	 *
	 * For example, in this method, the result is in $this->query_results.
	 *
	 * @return  object
	 * @since   1.0
	 */
	public function loadResult()
	{
		if ($this->query->select == null) {
			$this->query->select(
				$this->db->qn($this->primary_prefix)
					. '.'
					. $this->db->qn($this->primary_key));
		}

		if ($this->query->from == null) {
			$this->query->from(
				$this->db->qn($this->table_name)
					. ' as '
					. $this->db->qn($this->primary_prefix)
			);
		}

		$this->db->setQuery($this->query->__toString());

		$this->query_results = $this->db->loadResult();

		if (empty($this->query_results)) {
			return false;
		}
		$this->processQueryResults('loadResult');

		return $this->query_results;
	}

	/**
	 * loadResultArray
	 *
	 * Returns a single column returned in an array
	 *
	 * $this->query_results[0] thru $this->query_results[n]
	 *
	 * @return  object
	 * @since   1.0
	 */
	public function loadResultArray()
	{
		if ($this->query->select == null) {
			$this->query->select(
				$this->db->qn($this->primary_prefix)
					. '.'
					. $this->db->qn($this->primary_key));
		}

		if ($this->query->from == null) {
			$this->query->from(
				$this->db->qn($this->table_name)
					. ' as '
					. $this->db->qn($this->primary_prefix)
			);
		}

		$this->db->setQuery($this->query->__toString());

		$this->query_results = $this->db->loadResultArray();

		$this->processQueryResults('loadResultArray');

		return $this->query_results;
	}

	/**
	 * LoadRow
	 *
	 * Returns an indexed array from a single record in the table
	 *
	 * Access results $this->query_results[0] thru $this->query_results[n]
	 *
	 * @return  object
	 * @since   1.0
	 */
	public function loadRow()
	{
		$this->setQueryDefaults();

		$this->query_results = $this->db->loadRow();

		$this->processQueryResults('loadRow');

		return $this->query_results;
	}

	/**
	 * loadAssoc
	 *
	 * Returns an associated array from a single record in the table
	 *
	 * Access results $this->query_results['id']
	 *
	 * @return  object
	 * @since   1.0
	 */
	public function loadAssoc()
	{
		$this->setQueryDefaults();

		$this->query_results = $this->db->loadAssoc();

		$this->processQueryResults('loadAssoc');

		return $this->query_results;
	}

	/**
	 * loadObject
	 *
	 * Returns a PHP object from a single record in the table
	 *
	 * Access results $this->query_results->fieldname
	 *
	 * @return  object
	 * @since   1.0
	 */
	public function loadObject()
	{
		$this->setQueryDefaults();

		$this->query_results = $this->db->loadObject();

		$this->processQueryResults('loadObject');

		return $this->query_results;
	}

	/**
	 * loadRowList
	 *
	 * Returns an indexed array for multiple rows
	 *
	 * Access results $this->query_results[0][0] thru $this->query_results[n][n]
	 *
	 * @return  object
	 * @since   1.0
	 */
	public function loadRowList()
	{
		$this->setQueryDefaults();

		$this->query_results = $this->db->loadRowList();

		$this->processQueryResults('loadRowList');

		return $this->query_results;
	}

	/**
	 * loadAssocList
	 *
	 * Returns an indexed array of associative arrays
	 *
	 * Access results $this->query_results[0]['name']
	 *
	 * @return  object
	 * @since   1.0
	 */
	public function loadAssocList()
	{
		$this->setQueryDefaults();

		$this->query_results = $this->db->loadAssocList();

		$this->processQueryResults('loadAssocList');

		return $this->query_results;
	}

	/**
	 * loadObjectList
	 *
	 * Returns an indexed array of PHP objects
	 * from the table records returned by the query.
	 *
	 * Results are generally processed in a loop or
	 * can be directly accessed using the row index
	 * and column name
	 *
	 * $row['index']->name
	 *
	 * @return  object
	 * @since   1.0
	 */
	public function loadObjectList()
	{
		/** Establish primary select values and table, if not provided */
		$this->setQueryDefaults();

		/** Joins */
		if (Services::Registry()->get($this->table_registry_name, 'use_special_joins', 0) == 1) {
			$this->useSpecialJoins();
		}

		/** Set the DB Query */
		$this->db->setQuery($this->query->__toString());
		/**
		echo '<pre>';
		var_dump($this->query->__toString());
		echo '</pre>';
		 */
		/** Execute the query */
		$this->query_results = $this->db->loadObjectList();

		/** error handling */
		$this->processQueryResults('loadObjectList');

		/** return results */
		return $this->query_results;
	}

	/**
	 * setQueryDefaults
	 *
	 * sets default select and from values on query,
	 * if not established
	 *
	 * @return mixed
	 * @since  1.0
	 */
	protected function setQueryDefaults()
	{
		if ($this->query->select === null) {
			$this->fields = $this->getFieldDatatypes();

			while (list($name, $value) = each($this->fields)) {
				$this->query->select(
					$this->db->qn($this->primary_prefix)
						. '.'
						. $this->db->qn($name));
			}
		}

		if ($this->query->from == null) {
			$this->query->from(
				$this->db->qn($this->table_name)
					. ' as '
					. $this->db->qn($this->primary_prefix)
			);
		}

		return;
	}

	/**
	 * processQueryResults
	 *
	 * Processes the query, handles possible errors,
	 * returns results
	 *
	 * @param $location
	 *
	 * @return array
	 * @since  1.0
	 */
	protected function processQueryResults($location)
	{
		if ($this->db->getErrorNum() == 0) {

		} else {

			Services::Log()->set('Broken');
		}

		if (count($this->query_results) == 0) {
			$this->query_results = null;
		}

		return $this->query_results;
	}

	/**
	 * getAdditionalData
	 *
	 * Method to append additional data elements, as needed
	 *
	 * @param array $data
	 *
	 * @return array
	 * @since  1.0
	 */
	protected function getAdditionalData()
	{
		return array();
	}


	/**
	 * getPagination
	 *
	 * @return   array
	 * @since    1.0
	 */
	public function getPagination()
	{
		return $this->pagination;
	}
}
