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
    protected $name;

    /**
     * Database connection
     *
     * @var    string
     * @since  1.0
     */
    protected $db;

    /**
     * Database table
     *
     * @var    string
     * @since  1.0
     */
    protected $table;

    /**
     * Primary key field
     *
     * @var    string
     * @since  1.0
     */
    protected $primary_key;

    /**
     * Primary key value
     *
     * @var    string
     * @since  1.0
     */
    protected $id;

    /**
     * Used in setter/getter to store model state
     *
     * @var    array
     * @since  1.0
     */
    protected $state;

    /**
     * Results set from display query
     *
     * @var    array
     * @since  1.0
     */
    protected $items;

    /**
     * Pagination object from display query
     *
     * @var    array
     * @since  1.0
     */
    protected $pagination;

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
        $this->id = $id;

        if (isset($this->name)) {
        } else {
            $this->name = $this;
        }

        if (isset($this->db)) {
        } else {
            return $this->db = Services::DB();
        }

        if (isset($this->primary_key)) {
        } else {
            $this->primary_key = 'id';
        }

        if (isset($this->state)) {
        } else {
            $this->state = new Registry();
        }

        if ($this->get('crud', '') == '') {
            $this->set('crud', 'r');
        }

        return $this;
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
     * Create
     *
     * @return  object
     * @since   1.0
     */
    public function create()
    {
        $this->set('crud', 'c');

        $results = $this->validate();

        if ($results === false) {
            return false;
        }

        return $this;
    }

    /**
     * read
     *
     * @return  object
     * @since   1.0
     */
    public function read()
    {
        $this->set('crud', 'r');

        $this->_query();
    }

    /**
     * _query
     *
     * @return  object
     * @since   1.0
     */
    protected function _query()
    {
        $query = $this->db->getQuery(true);

        $query->select(' * ');
        $query->from($this->db->quoteName($this->table));
        $query->where($this->primary_key
            . ' = '
            . $this->db->quote($this->id));

        $this->db->setQuery($query->__toString());

        $row = $this->db->loadAssocList();

        if ($this->db->getErrorNum()) {
            $e = new MolajoException($this->db->getErrorMsg());
            $this->setError($e);
            return false;
        }

        if (empty($row)) {
            $e = new MolajoException(Services::Language()->_('MOLAJO_DB_ERROR_EMPTY_ROW_RETURNED'));
            $this->setError($e);
            return false;
        }

        if (key_exists('custom_fields', $row)
            && is_array($row['custom_fields'])
        ) {
            $registry = new Registry();
            $registry->loadArray($row['custom_fields']);
            $row['custom_fields'] = (string)$registry;
        }

        if (key_exists('parameters', $row)
            && is_array($row['parameters'])
        ) {
            $registry = new Registry();
            $registry->loadArray($row['parameters']);
            $row['parameters'] = (string)$registry;
        }

        if (key_exists('metadata', $row)
            && is_array($row['metadata'])
        ) {
            $registry = new Registry();
            $registry->loadArray($row['metadata']);
            $row['metadata'] = (string)$registry;
        }

        return $row;
    }

    /**
     * bind
     *
     * Method to bind an associative array to the Table
     * Ignores properties not publicly accessible and
     * those defined in the ignore parameter
     *
     * @param  $source
     * @param  $ignore
     *
     * @return bool
     * @since  1.0
     */
    public function bind($source, $ignore = array())
    {
        if (is_object($source)
            || is_array($source)
        ) {
        } else {
            $e = new MolajoException(Services::Language()->sprintf('MOLAJO_DB_ERROR_BIND_FAILED_INVALID_SOURCE_ARGUMENT', get_class($this)));
            $this->setError($e);
            return false;
        }

        if (is_array($ignore)) {
        } else {
            $ignore = explode(' ', $ignore);
        }

        if (is_object($source)) {
            $source = get_object_vars($source);
        }

        /** populate temporary table  */
        $this->items = array();
        foreach ($source as $key => $value) {
            if (in_array($key, $ignore)) {
            } else {
                $this->items[$key] = $value;
            }
        }

        return $this->items;
    }

    /**
     * update
     *
     * @return
     * @since   1.0
     */
    public function update()
    {
        $this->set('crud', 'u');

        $results = $this->validate();
        if ($results === false) {
            return false;
        }

        // update it

        return $this;
    }

    /**
     * delete
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
     * validate
     *
     * Runs custom validation methods define in the table xml
     *
     * @return  object
     * @since   1.0
     */
    public function validate()
    {
        /** will be set to false for error */
        $this->set('valid', true);

        /** Verify row is loaded */
        $results = $this->isLoaded();
        if ($results === false) {
            return false;
        }

        $crudCurrent = $this->get('crud');
        if ($crudCurrent == 'r') {
            return true;
        }

        /** Retrieve custom validations by table */
        $x = simplexml_load_file(
            MOLAJO_APPLICATIONS_MVC . '/models/tables/' . $this->table . '.xml'
        );
        if (count($x) == 0) {
            return true;
        }

        /** Foreign Keys */
        if (in_array($crudCurrent, array('c', 'u'))) {

            foreach ($x->validations->foreignkeys as $f) {

                $key = (string)$f->key;
                $pk = (string)$f->pk;
                $table = (string)$f->table;
                $zero = (string)$f->zero;

                $this->_validateForeignKey($key, $pk, $table);
            }
        }

        /** Values */
        if (in_array($crudCurrent, array('c', 'u'))) {

            foreach ($x->validations->values as $v) {

                $field = (string)$v->field;
                $required = (string)$v->required;
                $values = (string)$v->values;
                $default = (string)$v->default;

                $this->_validateValues($field, $required, $values, $default);
            }
        }

        /** Functions */
        foreach ($x->validations->functions as $f) {

            $class = (string)$f->class;
            $method = (string)$f->method;

            $crudArray = (string)$f->crud;
            if (in_array($crudCurrent, $crudArray)) {
                $this->_validateFunction($class, $method);
            }
        }
        return $this;
    }

    /**
     * isLoaded
     *
     * Checks if the primary key of the object is set.
     *
     * @return  boolean  True if loaded, false otherwise.
     * @since   1.0
     */
    protected function isLoaded()
    {
        return isset($this->primary_key);
    }

    /**
     * _validateForeignKey
     *
     * @return  boolean
     * @since   1.0
     */
    protected function _validateForeignKey($key, $pk, $table)
    {
        $query = $this->db->getQuery(true);

        $query->select($this->db->quoteName($pk));
        $query->from($this->db->quoteName($table));
        $query->where($this->db->quoteName($pk) . ' = ' . (int)$this->table->$key);

        $this->db->setQuery($query->__toString());

        $result = $this->db->loadResult();

        if ($this->db->getErrorNum()) {
            $e = new MolajoException($this->db->getErrorMsg());
            $this->setError($e);
            return false;
        }

        if ($result == (int)$this->table->$key) {
        } else {
            $this->set('valid', false);
        }
    }

    /**
     * _validateValues
     *
     * @return  boolean
     * @since   1.0
     */
    protected function _validateValues($field, $required = null, $values = null, $default = null)
    {
        /** Default */
        if (isset($this->table->$field)) {
        } else if ($default == null) {
        } else {
            $this->table->$field = $default;
        }

        /** Required */
        if ($required === true) {
            if (isset($this->table->$field)) {
            } else {
                $this->set('valid', false);
            }
            if (trim($this->table->$field) == ''
                || (int)$this->table->$field == 0
            ) {
                $this->set('valid', false);
            }
        }

        /** Values */
        if ($values == null) {
        } else {
            $testArray = explode(',', $values);
            if (in_array($this->table->$field, $testArray)) {
            } else {
                $this->set('valid', false);
            }
        }
    }

    /**
     * _validateFunction
     *
     * @return  boolean
     * @since   1.0
     */
    protected function _validateFunction($class, $method)
    {
        if (class_exists($class)) {
        } else {
            return false;
        }

        if (method_exists($class, $method)) {
        } else {
            return false;
        }

        $return = '';
        $execute = '$return = ' . $class . '::' . $method .
            '(' . $this->table . ');';
        eval($execute);
        if ($return === false) {
            $this->set('valid', false);
        }
    }

    /**
     * getItems
     *
     * @return    array
     * @since    1.0
     */
    public function getItems()
    {
        return $this->items;
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
     * Retrieves columns from the database table
     *
     * @return bool
     * @since  1.0
     */
    public function getFields()
    {
        return $this->db->getTableColumns($this->table, false);
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
            $stored = $this->db->updateObject($this->table, $this, $this->primary_key, $updateNulls);
        } else {
            $stored = $this->db->insertObject($this->table, $this, $this->primary_key);
        }

        if ($stored) {
        } else {
            $e = new MolajoException(Services::Language()->sprintf('MOLAJO_DB_ERROR_STORE_FAILED', get_class($this), $this->db->getErrorMsg()));
            $this->setError($e);
            return false;
        }

        if ($this->_locked) {
            $this->_unlock();
        }

        if (isset($this->table->asset_type_id)) {
            $this->_storeRelated();
        }

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
        //			$query = $this->db->getQuery(true);
        //			$query->update($this->db->quoteName($this->table));
        //			$query->set('asset_id = '.(int) $this->asset_id);
        //			$query->where($this->db->quoteName($k).' = '.(int) $this->$k);
        //			$this->db->setQuery($query->__toString());

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
        if (property_exists($this, 'checked_out') && property_exists($this, 'checked_out_time')) {
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
        $time = Services::Date()
            ->toSql();

        // Check the row out by primary key.
        $query = $this->db->getQuery(true);
        $query->update($this->table);
        $query->set($this->db->quoteName('checked_out') . ' = ' . (int)$userId);
        $query->set($this->db->quoteName('checked_out_time') . ' = ' . $this->db->quote($time));
        $query->where($this->primary_key . ' = ' . $this->db->quote($pk));
        $this->db->setQuery($query->__toString());

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
        if (property_exists($this, 'checked_out') && property_exists($this, 'checked_out_time')) {
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
        $query = $this->db->getQuery(true);
        $query->update($this->table);
        $query->set($this->db->quoteName('checked_out') . ' = 0');
        $query->set($this->db->quoteName('checked_out_time') . ' = ' . $this->db->quote($this->db->getNullDate()));
        $query->where($this->primary_key . ' = ' . $this->db->quote($pk));
        $this->db->setQuery($query->__toString());

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
        $query = $this->db->getQuery(true);
        $query->select('MAX(ordering)');
        $query->from($this->table);

        if ($where) {
            $query->where($where);
        }

        $this->db->setQuery($query->__toString());
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
        $query = $this->db->getQuery(true);
        $query->select($this->primary_key . ', ordering');
        $query->from($this->table);
        $query->where('ordering >= 0');
        $query->order('ordering');

        // Setup the extra where and ordering clause data.
        if ($where) {
            $query->where($where);
        }

        $this->db->setQuery($query->__toString());
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
                    $query = $this->db->getQuery(true);
                    $query->update($this->table);
                    $query->set('ordering = ' . ($i + 1));
                    $query->where($this->primary_key . ' = ' . $this->db->quote($row->$k));
                    $this->db->setQuery($query->__toString());

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
        $query = $this->db->getQuery(true);

        // Select the primary key and ordering values from the table.
        $query->select($this->primary_key . ', ordering');
        $query->from($this->table);

        // If the movement delta is negative move the row up.
        if ($delta < 0) {
            $query->where('ordering < ' . (int)$this->ordering);
            $query->order('ordering DESC');
        }
        // If the movement delta is positive move the row down.
        elseif ($delta > 0) {
            $query->where('ordering > ' . (int)$this->ordering);
            $query->order('ordering ASC');
        }

        // Add the custom WHERE clause if set.
        if ($where) {
            $query->where($where);
        }

        // Select the first row with the criteria.
        $this->db->setQuery($query, 0, 1);
        $row = $this->db->loadObject();

        // If a row is found, move the item.
        if (empty($row)) {

            // Update the ordering field for this instance.
            $query = $this->db->getQuery(true);
            $query->update($this->table);
            $query->set('ordering = ' . (int)$this->ordering);
            $query->where($this->primary_key . ' = ' . $this->db->quote($this->$k));
            $this->db->setQuery($query->__toString());

            // Check for a database error.
            if ($this->db->query()) {
            } else {
                $e = new MolajoException(Services::Language()->sprintf('MOLAJO_DB_ERROR_MOVE_FAILED', get_class($this), $this->db->getErrorMsg()));
                $this->setError($e);

                return false;
            }

        } else {
            // Update the ordering field for this instance to the row's ordering value.
            $query = $this->db->getQuery(true);
            $query->update($this->table);
            $query->set('ordering = ' . (int)$row->ordering);
            $query->where($this->primary_key . ' = ' . $this->db->quote($this->$k));
            $this->db->setQuery($query->__toString());

            // Check for a database error.
            if ($this->db->query()) {
            } else {
                $e = new MolajoException(Services::Language()->sprintf('MOLAJO_DB_ERROR_MOVE_FAILED', get_class($this), $this->db->getErrorMsg()));
                $this->setError($e);

                return false;
            }

            // Update the ordering field for the row to this instance's ordering value.
            $query = $this->db->getQuery(true);
            $query->update($this->table);
            $query->set('ordering = ' . (int)$this->ordering);
            $query->where($this->primary_key . ' = ' . $this->db->quote($row->$k));
            $this->db->setQuery($query->__toString());

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
        $query = $this->db->getQuery(true);
        $query->update($this->table);
        $query->set('published = ' . (int)$state);

        // Determine if there is checkin support for the table.
        if (property_exists($this, 'checked_out') || property_exists($this, 'checked_out_time')) {
            $query->where('(checked_out = 0 OR checked_out = ' . (int)$userId . ')');
            $checkin = true;

        } else {
            $checkin = false;
        }

        // Build the WHERE clause for the primary keys.
        $query->where($k . ' = ' . implode(' OR ' . $k . ' = ', $pks));

        $this->db->setQuery($query->__toString());

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
            $query = $this->db->getQuery(true);

            // Setup the basic query.
            $query->select($this->db->quoteName($this->primary_key));
            $query->from($this->db->quoteName($this->table));
            $query->where($this->db->quoteName($this->primary_key) . ' = ' . $this->db->quote($this->$k));
            $query->group($this->db->quoteName($this->primary_key));

            // For each join add the select and join clauses to the query object.
            foreach ($joins as $table) {
                $query->select('COUNT(DISTINCT ' . $table['idfield'] . ') AS ' . $table['idfield']);
                $query->join('LEFT', $table['name'] . ' ON ' . $table['joinfield'] . ' = ' . $k);
            }

            // Get the row object from the query.
            $this->db->setQuery((string)$query, 0, 1);
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
