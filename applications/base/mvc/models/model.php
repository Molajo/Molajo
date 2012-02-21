<?php
/**
 * @package     Molajo
 * @subpackage  Model
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
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
class MolajoModel
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
     * Results set from display query
     *  and used for create and update operations
     *
     * @var    array
     * @since  1.0
     */
    protected $data;

    /**
     * Used in setter/getter to store model state
     *
     * @var    array
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
     * Database table
     *
     * @var    string
     * @since  1.0
     */
    public $table;

    /**
     * Table fields
     *
     * @var    array
     * @since  1.0
     */
    public $fields;

    /**
     * Primary key field
     *
     * @var    string
     * @since  1.0
     */
    public $primary_key = '';

    /**
     * Primary key value
     *
     * @var    string
     * @since  1.0
     */
    public $id = 0;

    /**
     * Task Request
     *
     * @var    JRegistry
     * @since  1.0
     */
    public $task_request;

    /**
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
        $this->data = array();
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
            echo 'in MolajoModel::setE';
            die;
        } else {
            echo 'in MolajoModel::setError';
            die;
            Services::Message()
                ->set(
                $message = Services::Language()->_('ERROR_DATABASE_QUERY') . ' ' .
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
        if ($this->table == '') {
            return array();
        }
        return $this->db->getTableColumns($this->table, false);
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
                    $datatype .= ',space';
                } else if ((strtolower($fieldDefinition->Default)) == '0') {
                    $datatype .= ',zero';
                } else if ($fieldDefinition->Default == NULL) {
                    $datatype .= ',null';
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
     * @return  object
     * @since   1.0
     */
    public function load()
    {
        $this->set('crud', 'r');
        $this->_setLoadQuery();
        $results = $this->_runLoadQuery();
        if (empty($this->data)) {
            return false;
        }
        $this->data = $this->_getLoadAdditionalData($results);

        return $this->data;
    }

    /**
     * _setLoadQuery
     *
     * Method to create a query object for a specific item in a specific model
     *
     * @return  object
     * @since   1.0
     */
    protected function _setLoadQuery()
    {

    }

    /**
     * _runLoadQuery
     *
     * Method to execute a query statement for a specific item,
     * returning the results
     *
     * @return  object
     * @since   1.0
     */
    protected function _runLoadQuery()
    {
        return array();
    }

    /**
     * _getLoadAdditionalData
     *
     * Method to append additional data elements to a specific item,
     * as needed
     *
     * @param array $data
     *
     * @return array
     * @since  1.0
     */
    protected function _getLoadAdditionalData($data = array())
    {
        return $data;
    }

    /**
     * getData
     *
     * Used for most view access queries for any model. Not for a
     * specific item with intent to update. Use load for that purpose.
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
        $this->set('crud', 'r');
        $this->_setQuery();
        $this->data = $this->runQuery();
        if (empty($this->data)) {
            return false;
        }
        $this->data = $this->_getAdditionalData();

        return $this->data;
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
     * For example, in this method, the result is in $this->data.
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
                $this->db->qn($this->table)
                    . ' as '
                    . $this->db->qn($this->primary_prefix)
            );
        }
        //echo $this->query->__toString();

        $this->db->setQuery($this->query->__toString());
        $this->data = $this->db->loadResult();
        if (empty($this->data)) {
            return false;
        }
        $this->processQueryResults('loadResult');

        return $this->data;
    }

    /**
     * loadResultArray
     *
     * Returns a single column returned in an array
     *
     * $this->data[0] thru $this->data[n]
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
                $this->db->qn($this->table)
                    . ' as '
                    . $this->db->qn($this->primary_prefix)
            );
        }

        $this->db->setQuery($this->query->__toString());
        $this->data = $this->db->loadResultArray();

        $this->processQueryResults('loadResultArray');

        return $this->data;
    }

    /**
     * LoadRow
     *
     * Returns an indexed array from a single record in the table
     *
     * Access results $this->data[0] thru $this->data[n]
     *
     * @return  object
     * @since   1.0
     */
    public function loadRow()
    {
        $this->setQueryDefaults();
        $this->data = $this->db->loadRow();
        $this->processQueryResults('loadRow');

        return $this->data;
    }

    /**
     * loadAssoc
     *
     * Returns an associated array from a single record in the table
     *
     * Access results $this->data['id']
     *
     * @return  object
     * @since   1.0
     */
    public function loadAssoc()
    {
        $this->setQueryDefaults();
        $this->data = $this->db->loadAssoc();
        $this->processQueryResults('loadAssoc');

        return $this->data;
    }

    /**
     * loadObject
     *
     * Returns a PHP object from a single record in the table
     *
     * Access results $this->data->fieldname
     *
     * @return  object
     * @since   1.0
     */
    public function loadObject()
    {
        $this->setQueryDefaults();
        $this->data = $this->db->loadObject();
        $this->processQueryResults('loadObject');

        return $this->data;
    }

    /**
     * loadRowList
     *
     * Returns an indexed array for multiple rows
     *
     * Access results $this->data[0][0] thru $this->data[n][n]
     *
     * @return  object
     * @since   1.0
     */
    public function loadRowList()
    {
        $this->setQueryDefaults();
        $this->data = $this->db->loadRow();
        $this->processQueryResults('loadRowList');

        return $this->data;
    }

    /**
     * loadAssocList
     *
     * Returns an indexed array of associative arrays
     *
     * Access results $this->data[0]['name']
     *
     * @return  object
     * @since   1.0
     */
    public function loadAssocList()
    {
        $this->setQueryDefaults();
        $this->data = $this->db->loadAssocList();
        $this->processQueryResults('loadAssocList');

        return $this->data;
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
        $this->data = $this->db->loadObjectList();
        $this->processQueryResults('loadObjectList');

        return $this->data;
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
                $this->db->qn($this->table)
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
                $message = Services::Language()->_('ERROR_DATABASE_QUERY') . ' ' .
                    $this->db->getErrorNum() . ' ' .
                    $this->db->getErrorMsg(),
                $type = MOLAJO_MESSAGE_TYPE_ERROR,
                $code = 500,
                $debug_location = $this->name . ':' . $location,
                $debug_object = $this->db
            );
        }

        if (count($this->data) == 0) {
            $this->data = null;
        }

        return $this->data;
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
    protected function _getAdditionalData($data = array())
    {
        return $data;
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

    /**
     * create
     *
     * @return  object
     * @since   1.0
     */
    public function create()
    {
        $this->$this->primary_key = 0;
        return $this->update();
    }

    /**
     * update
     *
     * @return
     * @since   1.0
     */
    public function update()
    {
        $this->bind($this->data);

        $results = $this->validate();

        if ($results === false) {
            return false;
        }

        $results = $this->load();

        //      $results->bind($data);

        if ($results === false) {
            $this->db->setError($this->db->getError());
            return false;
        }

        $results = $this->validate();

        $results = $this->store();

        if ($results === false) {
            return false;
        }

        // update it

        return $this;
    }

    /**
     * initialize data array
     *
     * @return
     * @since   1.0
     */
    public function delete()
    {
        $this->set('crud', 'd');

        $results = $this->validate();
        if ($results === false) {
            return false;
        }

        // delete it

        return $this;
    }

    /**
     * bind
     *
     * Unloads the array to class properties for use with the
     * insert / update operation
     *
     * @param  $source
     *
     * @return bool
     * @since  1.0
     */
    public function bind($source)
    {
        foreach ($source as $key => $value) {
            $this->$key = $source[$key];
        }
        return true;
    }

    /**
     * validate
     *
     * Runs custom validation methods
     *
     * @return  object
     * @since   1.0
     */
    public function validate()
    {
        $this->set('valid', true);

        $v = simplexml_load_file(
            MOLAJO_APPLICATIONS_MVC
                . '/models/tables/'
                . substr($this->table, 3, 99)
                . '.xml'
        );
        if (count($v) == 0) {
            return true;
        }

        /** Foreign Keys */
        if (isset($v->fks->fk)) {
            foreach ($v->fks->fk as $f) {

                $name = (string)$f['name'];
                $source_id = (string)$f['source_id'];
                $source_model = (string)$f['source_model'];
                $required = (string)$f['required'];
                $message = (string)$f['message'];

                $this->_validateForeignKey($name, $source_id,
                    $source_model, $required, $message);
            }
        }

        /** Required and specific values */
        if (isset($v->values->value)) {
            foreach ($v->values->value as $r) {

                $name = (string)$r['name'];
                $required = (string)$r['required'];
                $values = (string)$r['values'];
                $default = (string)$r['default'];
                $message = (string)$r['message'];

                $this->_validateValues($name, $required,
                    $values, $default, $message);
            }
        }

        /** Helper Functions */

        /** Required and specific values */
        if (isset($v->helper->function)) {
            foreach ($v->helper->function as $h) {

                $name = (string)$h['name'];

                $this->_validateHelperFunction($name);
            }
        }
        return $this->get('valid');
    }

    /**
     * _validateForeignKey
     *
     * @param $name
     * @param $source_id
     * @param $source_table
     * @param $required
     * @param $message
     *
     * @return  null
     * @since   1.0
     */
    protected function _validateForeignKey($name, $source_id, $source_model,
                                           $required, $message)
    {
        if ($this->$name == 0
            && $required == 0) {
            return;
        }

        if (isset($this->$name)) {
            $m = new $source_model ($source_id);
            $m->query->where($m->db->qn('id')
                . ' = ' . $m->db->q($this->$name));

            $value = $m->loadResult();

            if (empty($value)) {
            } else {
                return;
            }
        } else {
            if ($required == 0) {
                return;
            }
        }

        $this->set('valid', false);

        Services::Message()
            ->set(
            $message = Services::Language()
                ->translate($message) . ' ' .
                $this->db->getErrorNum() . ' ' .
                $this->db->getErrorMsg(),
            $type = MOLAJO_MESSAGE_TYPE_ERROR,
            $code = 500,
            $debug_location = $this->name . ':' . '_validateForeignKey',
            $debug_object = $this->db
        );

        return;
    }

    /**
     * _validateValues
     *
     * @param $name
     * @param null $required
     * @param null $values
     * @param null $default
     * @param null $message
     *
     * @return  null
     * @since   1.0
     */
    protected function _validateValues($name, $required = null, $values = null,
                                       $default = null, $message = null)
    {
        $result = true;

        /** Default */
        if (isset($this->$name)) {
        } else if ($default == null) {
        } else {
            $this->$name = $default;
        }

        /** Required */
        if ($required == 1) {
            if (isset($this->$name)) {
            } else {
                $result = false;
            }
        }
        if ($required == 1
            && isset($this->$name)) {
            if (trim($this->$name) == ''
                && (int)$this->$name == 0
            ) {
                $result = false;
            }
        }

        /** Values */
        if ($values == null) {
        } else {
            $testArray = explode(',', $values);

            if (in_array($this->$name, $testArray)) {
            } else {
                $result = false;
            }
        }

        if ($result === true) {
            return;
        }

        $this->set('valid', false);
echo 'Failed '.$name.' '.$message.'<br />';
        Services::Message()
            ->set(
            $message = Services::Language()
                ->translate($message) . ' ' .
                $this->db->getErrorNum() . ' ' .
                $this->db->getErrorMsg(),
            $type = MOLAJO_MESSAGE_TYPE_ERROR,
            $code = 500,
            $debug_location = $this->name . ':' . '_validateValues',
            $debug_object = $this->db
        );

        return;
    }

    /**
     * _validateHelperFunction
     *
     * @param $method
     *
     * @return  boolean
     * @since   1.0
     */
    protected function _validateHelperFunction($method)
    {
        $class = 'Molajo' . ucfirst($this->table) . 'ModelHelper';
        if (class_exists($class)) {
        } else {
            $class = 'MolajoModelHelper';
        }

        if (method_exists($class, $method)) {
        } else {
            return false;
        }

        $return = '';
        $execute = '$return = ' . $class . '::' . $method .
            '("' . $this->name . '");';
        eval($execute);
        if ($return === false) {
            $method.' Failed';
            $this->set('valid', false);
        }
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
    public function store($updateNulls = false)
    {
        $k = $this->primary_key;

        if ($this->$k) {
            $stored = $this->db->
                updateObject($this->table, $this, $this->primary_key, $updateNulls);
        } else {
            $stored = $this->db->
                insertObject($this->table, $this, $this->primary_key);
        }

        if ($stored) {
        } else {
            $e = new MolajoException(Services::Language()->sprintf('MOLAJO_DB_ERROR_STORE_FAILED', get_class($this), $this->db->getErrorMsg()));
            $this->setError($e);
            return false;
        }
        /**
        if ($this->_locked) {
        $this->_unlock();
        }
         */

        return true;
    }

    /**
     * _storeRelated
     *
     * Method to store a row in the related table
     *
     * @return  boolean  True on success.
     *
     * @return bool
     * @since   1.0
     */
    private function _storeRelated()
    {
        $asset = new MolajoAssetModel();

        $asset->asset_type_id = $this->table->asset_type_id;

        $this->asset_id = $asset->save();

        $asset->load();
        if ($asset->getError()) {
            $this->setError($asset->getError());
            return false;
        }

        //
        // View Access
        //
        //		$grouping = MolajoModel::getInstance('Grouping');

        //       if ((int) $this->access == 0) {
        //            $asset->content_table = $this->table;
        //            $this->asset_id = $asset->save();
        //        } else {
        //            $asset->load();
        //        }

        //        if ($asset->getError()) {
        //            $this->setError($asset->getError());
        //            return false;
        //       }

        //        if ((int) $this->asset_id == 0) {
        //			$this->query = $this->db->getQuery(true);
        //			$this->query->update($this->db->qn($this->table));
        //			$this->query->set('asset_id = '.(int) $this->asset_id);
        //			$this->query->where($this->db->qn($k).' = '.(int) $this->$k);
        //			$this->db->setQuery($this->query->__toString());

        //			if ($this->db->query()) {
        //            } else {
        //				$e = new MolajoException(Services::Language()->sprintf('MOLAJO_DB_ERROR_STORE_FAILED_UPDATE_ASSET_ID', $this->db->getErrorMsg()));
        //				$this->setError($e);
        //				return false;
        //			}
        //        }
    }

    /**
     * checkOut
     *
     * Method to check a row out if the necessary properties/fields exist.  To
     * prevent race conditions while editing rows in a database, a row can be
     * checked out if the fields 'checked_out' and 'checked_out_time' are available.
     * While a row is checked out, any attempt to store the row by a user other
     * than the one who checked the row out should be held until the row is checked
     * in again.
     *
     * @param   integer  The Id of the user checking out the row.
     * @param   mixed    An optional primary key value to check out.  If not set
     *                    the instance property value is used.
     * @return  boolean  True on success.
     * @since   1.0
     */
    public function checkOut($userId, $pk = null)
    {
        // If there is no checked_out or checked_out_time field, just return true.
        if (property_exists($this, 'checked_out')
            && property_exists($this, 'checked_out_time')
        ) {
        } else {
            return true;
        }

        // Initialise variables.
        $k = $this->primary_key;
        $pk = (is_null($pk)) ? $this->$k : $pk;

        // If no primary key is given, return false.
        if ($pk === null) {
            $e = new MolajoException(Services::Language()->_('MOLAJO_DB_ERROR_NULL_PRIMARY_KEY'));
            $this->setError($e);
            return false;
        }

        // Get the current time in MySQL format.
        $time = Services::Date()->toSql();

        // Check the row out by primary key.
        $this->query = $this->db->getQuery(true);
        $this->query->update($this->table);
        $this->query->set($this->db->qn('checked_out') . ' = ' . (int)$userId);
        $this->query->set($this->db->qn('checked_out_time') . ' = ' . $this->db->q($time));
        $this->query->where($this->primary_key . ' = ' . $this->db->q($pk));
        $this->db->setQuery($this->query->__toString());

        if ($this->db->query()) {
        } else {
            $e = new MolajoException(Services::Language()->sprintf('MOLAJO_DB_ERROR_CHECKOUT_FAILED', get_class($this), $this->db->getErrorMsg()));
            $this->setError($e);
            return false;
        }

        // Set table values in the object.
        $this->checked_out = (int)$userId;
        $this->checked_out_time = $time;

        return true;
    }

    /**
     * checkIn
     *
     * Method to check a row in if the necessary properties/fields exist.  Checking
     * a row in will allow other users the ability to edit the row.
     *
     * @param   mixed    An optional primary key value to check out.  If not set
     *                    the instance property value is used.
     * @return  boolean  True on success.
     * @since   1.0
     */
    public function checkIn($pk = null)
    {
        // If there is no checked_out or checked_out_time field, just return true.
        if (property_exists($this, 'checked_out')
            && property_exists($this, 'checked_out_time')
        ) {
        } else {
            return true;
        }

        // Initialise variables.
        $k = $this->primary_key;
        $pk = (is_null($pk)) ? $this->$k : $pk;

        // If no primary key is given, return false.
        if ($pk === null) {
            $e = new MolajoException(Services::Language()->_('MOLAJO_DB_ERROR_NULL_PRIMARY_KEY'));
            $this->setError($e);
            return false;
        }

        // Check the row in by primary key.
        $this->query = $this->db->getQuery(true);
        $this->query->update($this->table);
        $this->query->set($this->db->qn('checked_out') . ' = 0');
        $this->query->set($this->db->qn('checked_out_time') . ' = ' . $this->db->q($this->db->getNullDate()));
        $this->query->where($this->primary_key . ' = ' . $this->db->q($pk));
        $this->db->setQuery($this->query->__toString());

        // Check for a database error.
        if ($this->db->query()) {
        } else {
            $e = new MolajoException(Services::Language()->sprintf('MOLAJO_DB_ERROR_CHECKIN_FAILED', get_class($this), $this->db->getErrorMsg()));
            $this->setError($e);
            return false;
        }

        // Set table values in the object.
        $this->checked_out = 0;
        $this->checked_out_time = '';

        return true;
    }

    /**
     * getNextOrder
     *
     * Method to get the next ordering value for a group of rows defined by an SQL WHERE clause.
     * This is useful for placing a new item last in a group of items in the table.
     *
     * @param   string   WHERE clause to use for selecting the MAX(ordering) for the table.
     * @return  mixed    Boolean false an failure or the next ordering value as an integer.
     * @since   1.0
     */
    public function getNextOrder($where = '')
    {
        // If there is no ordering field set an error and return false.
        if (property_exists($this, 'ordering')) {
        } else {
            $e = new MolajoException(Services::Language()->sprintf('MOLAJO_DB_ERROR_CLASS_DOES_NOT_SUPPORT_ORDERING', get_class($this)));
            $this->setError($e);
            return false;
        }

        // Get the largest ordering value for a given where clause.
        $this->query = $this->db->getQuery(true);
        $this->query->select('MAX(ordering)');
        $this->query->from($this->table);

        if ($where) {
            $this->query->where($where);
        }

        $this->db->setQuery($this->query->__toString());
        $max = (int)$this->db->loadResult();

        // Check for a database error.
        if ($this->db->getErrorNum()) {
            $e = new MolajoException(
                Services::Language()->sprintf('MOLAJO_DB_ERROR_GET_NEXT_ORDER_FAILED', get_class($this), $this->db->getErrorMsg())
            );
            $this->setError($e);

            return false;
        }

        // Return the largest ordering value + 1.
        return ($max + 1);
    }

    /**
     * reorder
     *
     * Method to compact the ordering values of rows in a group of rows
     * defined by an SQL WHERE clause.
     *
     * @param   string   WHERE clause to use for limiting the selection of rows to
     *                    compact the ordering values.
     * @return  mixed    Boolean true on success.
     * @since   1.0
     * @link    http://docs.molajo.org/MolajoModel/reorder
     */
    public function reorder($where = '')
    {
        // If there is no ordering field set an error and return false.
        if (property_exists($this, 'ordering')) {
        } else {
            $e = new MolajoException(Services::Language()->sprintf('MOLAJO_DB_ERROR_CLASS_DOES_NOT_SUPPORT_ORDERING', get_class($this)));
            $this->setError($e);
            return false;
        }

        // Initialise variables.
        $k = $this->primary_key;

        // Get the primary keys and ordering values for the selection.
        $this->query = $this->db->getQuery(true);
        $this->query->select($this->primary_key . ', ordering');
        $this->query->from($this->table);
        $this->query->where('ordering >= 0');
        $this->query->order('ordering');

        // Setup the extra where and ordering clause data.
        if ($where) {
            $this->query->where($where);
        }

        $this->db->setQuery($this->query->__toString());
        $rows = $this->db->loadObjectList();

        // Check for a database error.
        if ($this->db->getErrorNum()) {
            $e = new MolajoException(Services::Language()->sprintf('MOLAJO_DB_ERROR_REORDER_FAILED', get_class($this), $this->db->getErrorMsg()));
            $this->setError($e);
            return false;
        }

        // Compact the ordering values.
        foreach ($rows as $i => $row) {
            // Make sure the ordering is a positive integer.
            if ($row->ordering >= 0) {
                // Only update rows that are necessary.
                if ($row->ordering == $i + 1) {
                } else {
                    // Update the row ordering field.
                    $this->query = $this->db->getQuery(true);
                    $this->query->update($this->table);
                    $this->query->set('ordering = ' . ($i + 1));
                    $this->query->where($this->primary_key . ' = ' . $this->db->q($row->$k));
                    $this->db->setQuery($this->query->__toString());

                    // Check for a database error.
                    if ($this->db->query()) {
                    } else {
                        $e = new MolajoException(
                            Services::Language()->sprintf(
                                'MOLAJO_DB_ERROR_REORDER_UPDATE_ROW_FAILED', get_class($this), $i, $this->db->getErrorMsg()
                            )
                        );
                        $this->setError($e);

                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * move
     *
     * Method to move a row in the ordering sequence of a group of rows defined by an SQL WHERE clause.
     * Negative numbers move the row up in the sequence and positive numbers move it down.
     *
     * @param   integer  The direction and magnitude to move the row in the ordering sequence.
     * @param   string   WHERE clause to use for limiting the selection of rows to compact the
     *                    ordering values.
     * @return  mixed    Boolean true on success.
     * @since   1.0
     * @link    http://docs.molajo.org/MolajoModel/move
     */
    public function move($delta, $where = '')
    {
        // If there is no ordering field set an error and return false.
        if (property_exists($this, 'ordering')) {
        } else {
            $e = new MolajoException(Services::Language()->sprintf('MOLAJO_DB_ERROR_CLASS_DOES_NOT_SUPPORT_ORDERING', get_class($this)));
            $this->setError($e);
            return false;
        }

        // If the change is none, do nothing.
        if (empty($delta)) {
            return true;
        }

        // Initialise variables.
        $k = $this->primary_key;
        $row = null;
        $this->query = $this->db->getQuery(true);

        // Select the primary key and ordering values from the table.
        $this->query->select($this->primary_key . ', ordering');
        $this->query->from($this->table);

        // If the movement delta is negative move the row up.
        if ($delta < 0) {
            $this->query->where('ordering < ' . (int)$this->ordering);
            $this->query->order('ordering DESC');
        }
        // If the movement delta is positive move the row down.
        elseif ($delta > 0) {
            $this->query->where('ordering > ' . (int)$this->ordering);
            $this->query->order('ordering ASC');
        }

        // Add the custom WHERE clause if set.
        if ($where) {
            $this->query->where($where);
        }

        // Select the first row with the criteria.
        $this->db->setQuery($this->query, 0, 1);
        $row = $this->db->loadObject();

        // If a row is found, move the item.
        if (empty($row)) {

            // Update the ordering field for this instance.
            $this->query = $this->db->getQuery(true);
            $this->query->update($this->table);
            $this->query->set('ordering = ' . (int)$this->ordering);
            $this->query->where($this->primary_key . ' = ' . $this->db->q($this->$k));
            $this->db->setQuery($this->query->__toString());

            // Check for a database error.
            if ($this->db->query()) {
            } else {
                $e = new MolajoException(Services::Language()->sprintf('MOLAJO_DB_ERROR_MOVE_FAILED', get_class($this), $this->db->getErrorMsg()));
                $this->setError($e);

                return false;
            }

        } else {
            // Update the ordering field for this instance to the row's ordering value.
            $this->query = $this->db->getQuery(true);
            $this->query->update($this->table);
            $this->query->set('ordering = ' . (int)$row->ordering);
            $this->query->where($this->primary_key . ' = ' . $this->db->q($this->$k));
            $this->db->setQuery($this->query->__toString());

            // Check for a database error.
            if ($this->db->query()) {
            } else {
                $e = new MolajoException(Services::Language()->sprintf('MOLAJO_DB_ERROR_MOVE_FAILED', get_class($this), $this->db->getErrorMsg()));
                $this->setError($e);

                return false;
            }

            // Update the ordering field for the row to this instance's ordering value.
            $this->query = $this->db->getQuery(true);
            $this->query->update($this->table);
            $this->query->set('ordering = ' . (int)$this->ordering);
            $this->query->where($this->primary_key . ' = ' . $this->db->q($row->$k));
            $this->db->setQuery($this->query->__toString());

            // Check for a database error.
            if ($this->db->query()) {
            } else {
                $e = new MolajoException(Services::Language()->sprintf('MOLAJO_DB_ERROR_MOVE_FAILED', get_class($this), $this->db->getErrorMsg()));
                $this->setError($e);

                return false;
            }

            // Update the instance value.
            $this->ordering = $row->ordering;
        }

        return true;
    }

    /**
     * publish
     *
     * Method to set the publishing state for a row or list of rows in the database
     * table.  The method respects checked out rows by other users and will attempt
     * to checkin rows that it can after adjustments are made.
     *
     * @param   mixed    An optional array of primary key values to update.  If not
     *                    set the instance property value is used.
     * @param   integer The publishing state. eg. [0 = unpublished, 1 = published]
     * @param   integer The user id of the user performing the operation.
     * @return  boolean  True on success.
     * @since   1.0
     */
    public function publish($pks = null, $state = 1, $userId = 0)
    {
        // Initialise variables.
        $k = $this->primary_key;

        // Sanitize input.
        JArrayHelper::toInteger($pks);
        $userId = (int)$userId;
        $state = (int)$state;

        // If there are no primary keys set check to see if the instance key is set.
        if (empty($pks)) {
            if ($this->$k) {
                $pks = array($this->$k);
            }
            // Nothing to set publishing state on, return false.
            else {
                $e = new MolajoException(Services::Language()->_('MOLAJO_DB_ERROR_NO_ROWS_SELECTED'));
                $this->setError($e);

                return false;
            }
        }

        // Update the publishing state for rows with the given primary keys.
        $this->query = $this->db->getQuery(true);
        $this->query->update($this->table);
        $this->query->set('published = ' . (int)$state);

        // Determine if there is checkin support for the table.
        if (property_exists($this, 'checked_out') || property_exists($this, 'checked_out_time')) {
            $this->query->where('(checked_out = 0 OR checked_out = ' . (int)$userId . ')');
            $checkin = true;

        } else {
            $checkin = false;
        }

        // Build the WHERE clause for the primary keys.
        $this->query->where($k . ' = ' . implode(' OR ' . $k . ' = ', $pks));

        $this->db->setQuery($this->query->__toString());

        // Check for a database error.
        if ($this->db->query()) {
        } else {
            $e = new MolajoException(Services::Language()->sprintf('MOLAJO_DB_ERROR_PUBLISH_FAILED', get_class($this), $this->db->getErrorMsg()));
            $this->setError($e);
            return false;
        }

        // If checkin is supported and all rows were adjusted, check them in.
        if ($checkin && (count($pks) == $this->db->getAffectedRows())) {
            // Checkin the rows.
            foreach ($pks as $pk)
            {
                $this->checkin($pk);
            }
        }

        // If the MolajoModel instance value is in the list of primary keys that were set, set the instance.
        if (in_array($this->$k, $pks)) {
            $this->published = $state;
        }

        $this->setError('');
        return true;
    }

    /**
     * canDelete
     *
     * Generic check for whether dependancies exist for this object in the database schema
     *
     * Can be overloaded/supplemented by the child class
     *
     * @deprecated
     * @param   mixed    An optional primary key value check the row for.  If not
     *                    set the instance property value is used.
     * @param   array    An optional array to compiles standard joins formatted like:
     *                    [label => 'Label', name => 'table name' , idfield => 'field', joinfield => 'field']
     * @return  boolean  True on success.
     * @since   1.0
     */
    public function canDelete($pk = null, $joins = null)
    {
        // Initialise variables.
        $k = $this->primary_key;
        $pk = (is_null($pk)) ? $this->$k : $pk;

        // If no primary key is given, return false.
        if ($pk === null) {
            return false;
        }

        if (is_array($joins)) {
            // Get a query object.
            $this->query = $this->db->getQuery(true);

            // Setup the basic query.
            $this->query->select($this->db->qn($this->primary_key));
            $this->query->from($this->db->qn($this->table));
            $this->query->where($this->db->qn($this->primary_key) . ' = ' . $this->db->q($this->$k));
            $this->query->group($this->db->qn($this->primary_key));

            // For each join add the select and join clauses to the query object.
            foreach ($joins as $table) {
                $this->query->select('COUNT(DISTINCT ' . $table['idfield'] . ') AS ' . $table['idfield']);
                $this->query->join('LEFT', $table['name'] . ' ON ' . $table['joinfield'] . ' = ' . $k);
            }

            // Get the row object from the query.
            $this->db->setQuery((string)$this->query, 0, 1);
            $row = $this->db->loadObject();

            // Check for a database error.
            if ($this->db->getErrorNum()) {
                $this->setError($this->db->getErrorMsg());

                return false;
            }

            $msg = array();
            $i = 0;

            foreach ($joins as $table) {
                $k = $table['idfield'] . $i;
                if ($row->$k) {
                    $msg[] = Services::Language()->_($table['label']);
                }

                $i++;
            }

            if (count($msg)) {
                $this->setError("noDeleteRecord" . ": " . implode(', ', $msg));

                return false;
            }
            else {
                return true;
            }
        }

        return true;
    }
}
