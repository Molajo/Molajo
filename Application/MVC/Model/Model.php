<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application\MVC\Model;

use Joomla\database\JDatabaseFactory;
use Molajo\Application\Services; //Date, DB, Language, Message, Registry

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
	 * Name-spaced Model Name
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $name = '';

	/**
	 * Model Name
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $model_name = '';

	/**
	 * Database connection
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $db;

	/**
	 * Primary Prefix
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $primary_prefix;

	/**
	 * Database query object
	 *
	 * @var    object
	 * @since  1.0
	 */
	public $query;

	/**
	 * Used in queries to determine date validity
	 *
	 * @var    object
	 * @since  1.0
	 */
	public $nullDate;

	/**
	 * Today's CCYY-MM-DD 00:00:00 Used in queries to determine date validity
	 *
	 * @var    object
	 * @since  1.0
	 */
	public $now;

	/**
	 * Results from various model queries
	 *
	 * @var        object
	 * @since      1.0
	 */
	protected $query_results;

	/**
	 * Used by setter/getter to store model state
	 *
	 * @var        object
	 * @since      1.0
	 */
	protected $state;

	/**
	 * Pagination object from display query
	 *
	 * @var        array
	 * @since      1.0
	 */
	protected $pagination;

	/**
	 * Name of the database table for the model
	 *
	 * @var        string
	 * @since      1.0
	 */
	public $table_name;

	/**
	 * $table_xml
	 *
	 * Name of the table definitions for the model
	 *
	 * @var        string
	 * @since      1.0
	 */
	public $table_xml;

	/**
	 * $row
	 *
	 * Single row for $table_name
	 *
	 * @var    \stdClass
	 * @since  1.0
	 */
	public $row;

	/**
	 * $table_fields
	 *
	 * List of all data elements in table
	 *
	 * @var    array
	 * @since  1.0
	 */
	public $table_fields;

	/**
	 * $primary_key
	 *
	 * Name of the primary key for the model table
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $primary_key = '';

	/**
	 * $id
	 *
	 * Value for the primary key of the model table
	 *
	 * @var    string
	 * @since  1.0
	 */
	public $id = 0;

	/**
	 * Default code if lookup value does not exist
	 *
	 * @var    integer  constant
	 * @since  1.0
	 */
	const DEFAULT_CODE = 300000;

	/**
	 * Default message if no message is provided
	 *
	 * @var    string  Constant
	 * @since  12.1
	 */
	const DEFAULT_MESSAGE = 'Undefined Message';

	/**
	 * __construct
	 *
	 * @return  object
	 * @since   1.0
	 */
	public function __construct($table = null, $id = null, $path = null)
	{
		/** Retrieve XML for Table */
		if (trim($table) === null) {
			$table = 'Content';
		}

		if ($path === null) {
			$path = APPLICATIONS_MVC . '/Model/Table';
		}

		$file = $path . '/' . ucfirst(strtolower($table)) . '.xml';

		if (is_file($file)) {
		} else {
			throw new \InvalidArgumentException('$table:  ' . $table . ' $id: ' . $id . ' $path: ' . $path);
		}

		$this->table_xml = simplexml_load_file($file);

		$this->model_name = (string)$this->table_xml['name'];
		if ($this->model_name === '') {
			throw new \RuntimeException('No model name in XML: ' . $file);
		}
		$this->table_name = (string)$this->table_xml['table'];

		/** todo: default table name from model, adding underscores in front of uppercase letters, lowercasing all */
		if ($this->table_name === '') {
			$this->table_name = '#__content';
		}

		$this->primary_key = (string)$this->table_xml['primary_key'];
		if ($this->primary_key === '') {
			$this->primary_key = 'id';
		}

		$this->task_request = Services::Registry()->initialise();
		$this->state = Services::Registry()->initialise();
		$this->query_results = array();
		$this->pagination = array();

		if ((int)$this->id == 0) {
			$this->id = $id;
		}
		if (trim($this->name) == '') {
			$this->name = get_class($this);
		}
		if (trim($this->primary_key) == '') {
			$this->primary_key = 'id';
		}

		$this->db = Services::Database()->get('db');

		$this->query = Services::Database()->getQuery();
		$this->query->clear();

		$this->nullDate = $this->db->getNullDate();
		$this->now = date("Y-m-d") . ' 00:00:00';
		$this->primary_prefix = 'a';

		$this->model_name = substr($this->name, strlen('Molajo\\Application\\MVC\\Model\\'), strlen($this->name) - strlen('Molajo\\Application\\MVC\\Model\\') - strlen('Model'));

		/**
		if ((int) Molajo::Services()->get('DebugService', 0) == 0) {
		} else {
		Services::Debug()->set('Model Construct '.$this->name. ' '.$this->model_name);
		}
		 */
		return $this;
	}

	/**
	 * Return message given message code
	 *
	 * @param   string  $code  Numeric value associated with message
	 *
	 * @return  mixed  Array or String
	 *
	 * @since   12.1
	 */
	public function getMessage($code = 0)
	{
		$message = array(
			300100 => 'Invalid key of type. Expected simple.',
			300200 => 'The mcrypt extension is not available.',
			300300 => 'Invalid JCryptKey used with Mcrypt decryption.',
			300400 => 'Invalid JCryptKey used with Mcrypt encryption.',
			300500 => 'Invalid JCryptKey used with Simple decryption.',
			300600 => 'Invalid JCryptKey used with Simple encryption.',
		);

		if ($code == 0) {
			return $message;
		}

		if (isset($message[$code])) {
			return $message[$code];
		}

		return self::DEFAULT_MESSAGE;
	}

	/**
	 * Return code given message
	 *
	 * @param   string  $code  Numeric value associated with message
	 *
	 * @return  mixed  Array or String
	 *
	 * @since   12.1
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
	 * get
	 *
	 * Returns property of the Model object
	 * or the default value if the property is not set.
	 *
	 * @param   string  $key      The name of the property.
	 * @param   mixed   $default  The default value (optional) if none is set.
	 *
	 * @return  mixed   The value of the configuration.
	 *
	 * @since   1.0
	 */
	public function get($key, $default = null)
	{
		return $this->state->get($key, $default);
	}

	/**
	 * set
	 *
	 * Modifies a property of the Model object,
	 * creating it if it does not already exist.
	 *
	 * @param   string  $key    The name of the property.
	 * @param   mixed   $value  The value of the property to set (optional).
	 *
	 * @return  mixed   Value of the property
	 *
	 * @since   1.0
	 */
	public function set($key, $value = null)
	{
		return $this->state->set($key, $value);
	}

	/**
	 * getState
	 *
	 * @return    array
	 * @since    1.0
	 */
	public function getState()
	{
		return $this->state;
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
	 * getCustomfieldFieldNames
	 *
	 * Retrieves custom_field column names for specific model
	 *
	 * @return array
	 * @since  1.0
	 */
	public function getCustomfieldFieldNames()
	{
		if ($this->name == '') {
			return array();
		}
	}

	/**
	 * getMetadataFieldNames
	 *
	 * Retrieves metadata column names for specific model
	 *
	 * @return array
	 * @since  1.0
	 */
	public function getMetadataFieldNames()
	{
		if ($this->name == '') {
			return array();
		}
	}

	/**
	 * getParameterFieldNames
	 *
	 * Retrieves parameter column names for specific model
	 *
	 * @return array
	 * @since  1.0
	 */
	public function getParameterFieldNames()
	{
		if ($this->name == '') {
			return array();
		}
	}

	/**
	 * getProperties
	 *
	 * Returns an associative array of object properties.
	 *
	 * @return  array
	 * @since   1.0
	 */
	public function getProperties()
	{
		return get_object_vars($this);
	}

	/**
	 *  Read Methods
	 */

	/**
	 * load
	 *
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
	 * getLoadAdditionalData
	 *
	 * Method used in load sequence to optionally append additional
	 * data elements to a specific item
	 *
	 * @return array
	 * @since  1.0
	 */
	protected function getLoadAdditionalData()
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
		$this->query_results = $this->db->loadRow();
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
		$this->setQueryDefaults();
		$this->query_results = $this->db->loadObjectList();
		$this->processQueryResults('loadObjectList');

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

		$this->db->setQuery($this->query->__toString());
		/**
		echo '<pre>';
		var_dump($this->query->__toString());
		echo '</pre>';
		 */
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

			Services::Message()
				->set(
				$message = Services::Language()
					->_('ERROR_DATABASE_QUERY') . ' ' .
					$this->db->getErrorNum() . ' ' .
					$this->db->getErrorMsg(),
				$type = MESSAGE_TYPE_ERROR,
				$code = 500,
				$debug_location = $this->name . ':' . $location,
				$debug_object = $this->db
			);
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
	 * @return    array
	 * @since    1.0
	 */
	public function getPagination()
	{
		return $this->pagination;
	}
}
