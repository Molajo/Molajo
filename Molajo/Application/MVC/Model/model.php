<?php
/**
 * @package     Molajo
 * @subpackage  Model
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application\MVC\Model;

defined('MOLAJO') or die;

/**
 * Model
 *
 * Base Molajo Model
 *
 * @package       Molajo
 * @subpackage    Model
 * @since 1.0
 */
Class Model
{
    /**
     * Model Name
     *
     * @var    string
     * @since  1.0
     */
    protected $name = '';

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
     * $now
     *
     * Used in queries to determine date validity
     *
     * @var    object
     * @since  1.0
     */
    public $now;

    /**
     * $nullDate
     *
     * Used in queries to determine date validity
     *
     * @var    object
     * @since  1.0
     */
    public $nullDate;

    /**
     * $query_results
     *
     * Results from various model queries
     *
     * @var    object
     * @since  1.0
     */
    protected $query_results;

    /**
     * Used by setter/getter to store model state
     *
     * @var    Registry
     * @since  1.0
     */
    protected $state;

    /**
     * Pagination object from display query
     *
     * @var    array
     * @since  1.0
     */
    protected $pagination;

    /**
     * $table_name
     *
     * Name of the database table for the model
     *
     * @var    string
     * @since  1.0
     */
    public $table_name;

    /**
     * $row
     *
     * Single row for $table_name
     *
     * @var    stdclass
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
     * $task_request
     *
     * Processing instructions for the MVC set by the renderer
     *
     * @var    JRegistry
     * @since  1.0
     */
    public $task_request;

    /**
     * $parameters
     *
     * Parameters
     *
     * @var    JRegistry
     * @since  1.0
     */
    public $parameters;

    /**
     * __construct
     *
     * @return  object
     * @since   1.0
     */
    public function __construct($id = null)
    {
        $this->task_request = new Registry();
        $this->state = new Registry();
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

        if (isset($this->db)) {
        } else {
            $this->db = Services::DB();
        }

        $this->query = $this->db->getQuery(true);
        $this->now = Services::Date()->getDate()->toSql();
        $this->nullDate = $this->db->getNullDate();
        $this->primary_prefix = 'a';
    }


    protected function setError()
    {
        if ($this->db->getErrorNum() == 0) {
            echo 'in Model::setE';
            die;
        } else {
            echo 'in Model::setError';
            die;
            Services::Message()
                ->set(
                $message = Services::Language()->translate('ERROR_DATABASE_QUERY') . ' ' .
                    $this->db->getErrorNum() . ' ' .
                    $this->db->getErrorMsg(),
                $type = MOLAJO_MESSAGE_TYPE_ERROR,
                $code = 0,
                $debug_location = $this->name,
                $debug_object = $this->db
            );
            return false;
        }
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
     * getFields
     *
     * Retrieves column names and definitions from the database table
     *
     * @return array
     * @since  1.0
     */
    public function getFields()
    {
        if ($this->table_name == '') {
            return array();
        }
        return $this->db->getTableColumns($this->table_name, false);
    }

    /**
     * getFieldnames
     *
     * Retrieves column names, only, for the database table
     *
     * @return array
     * @since  1.0
     */
    public function getFieldnames()
    {
        $fields = array();
        $fieldDefinitions = $this->getFields();
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
        $fieldDefinitions = $this->getFields();
        if (count($fieldDefinitions) > 0) {
            foreach ($fieldDefinitions as $fieldDefinition) {

                $datatype = '';

                /* basic datatype */
                if (stripos($fieldDefinition->Type, 'int') !== false) {
                    $datatype = 'int';
                } else if (stripos($fieldDefinition->Type, 'date') !== false) {
                    $datatype = 'date';
                } else if (stripos($fieldDefinition->Type, 'text') !== false) {
                    $datatype = 'text';
                } else {
                    $datatype = 'char';
                }

                /* null */
                if ((strtolower($fieldDefinition->Null)) == 'yes') {
                    $datatype .= ',1';
                } else {
                    $datatype .= ',0';
                }

                /* default */
                if ((strtolower($fieldDefinition->Extra)) == 'auto_increment') {
                    $datatype .= ',auto_increment';
                } else if ((strtolower($fieldDefinition->Default)) == ' ') {
                    $datatype .= ', ';
                } else if ((strtolower($fieldDefinition->Default)) == '0') {
                    $datatype .= ',0';
                } else if ($fieldDefinition->Default == NULL) {
                    $datatype .= ',';
                } else {
                    $datatype .= ',' . trim($fieldDefinition->Default);
                }

                /* save it to array */
                $fields[$fieldDefinition->Field] = $datatype;
            }
        }

        return $fields;
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
     * Implemented by LoadModel, can be overridden by child
     *
     * @return  object
     * @since   1.0
     */
    public function load()
    {
        return $this->query_results = array();
    }

    /**
     * _setLoadQuery
     *
     * Method used in load sequence to create a query object for a
     * specific item in a specific model
     *
     * @return  object
     * @since   1.0
     */
    protected function _setLoadQuery()
    {
        $this->query = $this->db->getQuery(true);
    }

    /**
     * _runLoadQuery
     *
     * Method used in load sequence to execute a query statement
     * for a specific item, returning the results
     *
     * @return  object
     * @since   1.0
     */
    protected function _runLoadQuery()
    {
        return $this->query_results = array();
    }

    /**
     * _getLoadAdditionalData
     *
     * Method used in load sequence to optionally append additional
     * data elements to a specific item
     *
     * @return array
     * @since  1.0
     */
    protected function _getLoadAdditionalData()
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
        $this->_setQuery();
        $this->query_results = $this->runQuery();
        if (empty($this->query_results)) {
            return false;
        }
        $this->query_results = $this->_getAdditionalData();

        return $this->query_results;
    }

    /**
     * _setQuery
     *
     * Method to create a query object in preparation of running a query
     *
     * @return  object
     * @since   1.0
     */
    protected function _setQuery()
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
        if ($this->query->select == null) {
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
                $type = MOLAJO_MESSAGE_TYPE_ERROR,
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
     * _getAdditionalData
     *
     * Method to append additional data elements, as needed
     *
     * @param array $data
     *
     * @return array
     * @since  1.0
     */
    protected function _getAdditionalData()
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
