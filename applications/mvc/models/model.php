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
class MolajoModel extends JObject
{
    /**
     * Name
     *
     * @var    string
     * @since  1.0
     */
    protected $_name = 'MolajoModel';

    /**
     * Configuration
     *
     * @var    object
     * @since  1.0
     */
    protected $_config;

    /**
     * Model State
     *
     * @var    object
     * @since  1.0
     */
    protected $_state;

    /**
     * $mvc
     *
     * Current request variables
     *
     * @var    object
     * @since  1.0
     */
    public $mvc;

    /**
     * $parameters
     *
     * Options for current request
     *
     * @var    array
     * @since  1.0
     */
    public $parameters = array();

    /**
     * Database connection
     *
     * @var    string
     * @since  1.0
     */
    protected $_db = '';

    /**
     * Name of the database table
     *
     * @var    string
     * @since  1.0
     */
    protected $_table = '';

    /**
     * Table array
     *
     * @var    array
     * @since  1.0
     */
    public $tableQueryResults = '';

    /**
     * Name of the primary key field in the table.
     *
     * @var    string
     * @since  1.0
     */
    protected $_primary_key = '';

    /**
     * Primary Key Value
     *
     * @var    string
     * @since  1.0
     */
    public $id = 0;

    /**
     * $items
     *
     * @var    array
     * @since  1.0
     */
    public $items = array();

    /**
     * $pagination
     *
     * @var    array
     * @since  1.0
     */
    public $pagination = array();

    /**
     * $task
     *
     * @var    string
     * @since  1.0
     */
    public $task = null;

    /**
     * __construct
     *
     * @param   string  $name
     * @param   string  $prefix
     * @param   array   $config
     *
     * @return  object
     * @since   1.0
     */
    public function __construct(Registry $config = null)
    {
        if ($config instanceof Registry) {
            $this->_config = $config;
        } else {
            $this->_config = new Registry;
        }

        if (array_key_exists('dbo', $this->_config)) {
            $this->_db = $this->_config['dbo'];
        } else {
            $this->_db = Molajo::DB();
        }
    }

    /**
     * get
     *
     * Returns a property of the Model object
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
        return $this->_state->get($key, $default);
    }

    /**
     * set
     *
     * Modifies a property of the Model object, creating it if it does not already exist.
     *
     * @param   string  $key    The name of the property.
     * @param   mixed   $value  The value of the property to set (optional).
     *
     * @return  mixed   Previous value of the property
     *
     * @since   1.0
     */
    public function set($key, $value = null)
    {
        $this->_state->set($key, $value);
    }

    /**
     * populateState
     *
     * Method to auto-populate the model state.
     *
     * @return    void
     * @since    1.0
     */
    protected function populateState()
    {

    }

    /**
     * getMVC
     *
     * @return    array    An empty array
     *
     * @since    1.0
     */
    public function getMVC()
    {
        return $this->mvc;
    }

    /**
     * getParameters
     *
     * @return    array    An empty array
     *
     * @since    1.0
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * getItems
     *
     * @return    array    An empty array
     *
     * @since    1.0
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * getPagination
     *
     * @return    array    An empty array
     *
     * @since    1.0
     */
    public function getPagination()
    {
        return $this->pagination;
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
        $name = $this->_table;
        $fields = $this->_db->getTableColumns($name, false);

        if (empty($fields)) {
            $e = new JException(JText::_('JLIB_DB_ERROR_COLUMNS_NOT_FOUND'));
            $this->setError($e);
            return false;
        }

        return $fields;
    }

    /**
     * getTableName
     *
     * Method to get the database table name for the class.
     *
     * @return  string  The name of the database table being modeled.
     * @since   1.0
     */
    public function getTableName()
    {
        return $this->_table;
    }

    /**
     * getKeyName
     *
     * Method to get the primary key field name for the table.
     *
     * @return  string  Primary key for the table
     * @since   1.0
     */
    public function getKeyName()
    {
        return $this->_primary_key;
    }

    /**
     * getDbo
     *
     * Method to get the Database connector object.
     *
     * @return  object
     * @since   1.0
     */
    public function getDbo()
    {
        return $this->_db;
    }

    /**
     * setDbo
     *
     * Method to set the Database connector object.
     *
     * @param   object   Database connection object
     * @return  boolean  True on success.
     * @since   1.0
     */
    public function setDbo($db)
    {
        if ($db instanceof JDatabase) {
        } else {
            return false;
        }

        $this->_db = $db;

        return true;
    }

    /**
     * load
     *
     * Method to load a row from the database by primary key and bind its fields
     *
     * @param   string  $id
     * @param   bool    $reset
     *
     * @return  bool
     * @since   1.0
     */
    public function load($id = null, $reset = true)
    {
        $this->id = $id;

        /** initialize */
        if ($reset === true) {
            $this->reset();
        }

        $row = $this->_query($this->id, $reset);

        return $this->bind($row, array());
    }

    /**
     * _query
     *
     * @param   null  $id
     * @param   bool  $reset
     * @return  array|bool
     */
    protected function _query($id = null, $reset = true)
    {
        /** load query */
        $query = $this->_db->getQuery(true);

        $query->select('*');
        $query->from($this->_db->quoteName($this->_table));
        $query->where($this->_primary_key . ' = ' . $this->_db->quote($this->id));

        $this->_db->setQuery($query->__toString());

        $row = $this->_db->loadAssocList();

        if ($this->_db->getErrorNum()) {
            $e = new MolajoException($this->_db->getErrorMsg());
            $this->setError($e);
            return false;
        }

        if (empty($row)) {
            $e = new MolajoException(TextHelper::_('MOLAJO_DB_ERROR_EMPTY_ROW_RETURNED'));
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
     * reset
     *
     * Method to reset class properties to the defaults
     * Ignores primary key and private class properties
     *
     * @return  void
     * @since   1.0
     */
    public function reset()
    {
        foreach ($this->getFields() as $k => $v) {
            if ($k == $this->_primary_key || (strpos($k, '_') == 0)) {
            } else {
                $this->$k = $v->Default;
            }
        }
    }

    /**
     * save
     *
     * Method to provide a shortcut to binding, checking and storing data
     *
     * @param   mixed   Array or object to bind to table
     * @param   string  Filter for the order updating
     * @param   mixed   An optional array or space separated list of properties to ignore for binding
     *
     * @return  boolean
     * @since   1.0
     */
    public function save($source, $orderingFilter = '', $ignore = '')
    {
        if ($this->bind($source, $ignore)) {
        } else {
            return false;
        }

        if ($this->check()) {
        } else {
            return false;
        }

        if ($this->store()) {
        } else {
            return false;
        }

        if ($this->checkin()) {
        } else {
            return false;
        }

        if ($orderingFilter) {
            $filterValue = $this->$orderingFilter;
            $this->reorder($orderingFilter
                ? $this->_db->quoteName($orderingFilter) . ' = ' . $this->_db->Quote($filterValue)
                : '');
        }

        $this->setError('');

        return true;
    }

    /**
     * bind
     *
     * Method to bind an associative array or object to the Table instance. This
     * method only binds properties that are publicly accessible and optionally
     * takes an array of properties to ignore when binding.
     *
     * @param  $source
     * @param  array $ignore
     *
     * @return bool
     * @since  1.0
     */
    public function bind($source, $ignore = array())
    {
        if (is_object($source)
            || is_array($source)) {
        } else {
            $e = new MolajoException(TextHelper::sprintf('MOLAJO_DB_ERROR_BIND_FAILED_INVALID_SOURCE_ARGUMENT', get_class($this)));
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
        foreach ($source as $key=>$value) {
            if (in_array($key, $ignore)) {
            } else {
                $this->tableQueryResults[$key] = $value;
            }
        }

        return $this->tableQueryResults;
    }

    /**
     * check
     *
     * Method to perform editing to ensure correctness before storing in database
     * Child classes should override method to implement specific business rules for table.
     *
     * @return  boolean  True if the instance is sane and able to be stored in the database.
     * @since   1.0
     */
    public function check()
    {
        return true;
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
        $k = $this->_primary_key;

        if ($this->$k) {
            $stored = $this->_db->updateObject($this->_table, $this, $this->_primary_key, $updateNulls);
        } else {
            $stored = $this->_db->insertObject($this->_table, $this, $this->_primary_key);
        }

        if ($stored) {
        } else {
            $e = new MolajoException(TextHelper::sprintf('MOLAJO_DB_ERROR_STORE_FAILED', get_class($this), $this->_db->getErrorMsg()));
            $this->setError($e);
            return false;
        }

        if ($this->_locked) {
            $this->_unlock();
        }

        if (isset($this->_table->asset_type_id)) {
            $this->_storeAsset();
        }

        return true;
    }

    /**
     * _storeAsset
     *
     * Method to store a row (insert: no PK; update: PK) in the assets table
     *
     * @return  boolean  True on success.
     *
     * @return bool
     * @since   1.0
     */
    private function _storeAsset()
    {
        $asset = new MolajoAssetModel();

        $asset->asset_type_id = $this->_table->asset_type_id;

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
        //            $asset->content_table = $this->_table;
        //            $this->asset_id = $asset->save();
        //        } else {
        //            $asset->load();
        //        }

        //        if ($asset->getError()) {
        //            $this->setError($asset->getError());
        //            return false;
        //       }

        //        if ((int) $this->asset_id == 0) {
        //			$query = $this->_db->getQuery(true);
        //			$query->update($this->_db->quoteName($this->_table));
        //			$query->set('asset_id = '.(int) $this->asset_id);
        //			$query->where($this->_db->quoteName($k).' = '.(int) $this->$k);
        //			$this->_db->setQuery($query->__toString());

        //			if ($this->_db->query()) {
        //            } else {
        //				$e = new MolajoException(TextHelper::sprintf('MOLAJO_DB_ERROR_STORE_FAILED_UPDATE_ASSET_ID', $this->_db->getErrorMsg()));
        //				$this->setError($e);
        //				return false;
        //			}
        //        }
    }

    /**
     * delete
     *
     * Method to delete a row from the database table by primary key value.
     *
     * @param   mixed    An optional primary key value to delete.  If not set the
     *                    instance property value is used.
     * @return  boolean  True on success.
     * @since   1.0
     */
    public function delete($pk = null)
    {
        $k = $this->_primary_key;
        $pk = (is_null($pk)) ? $this->$k : $pk;

        if ($pk === null) {
            $e = new MolajoException(TextHelper::_('MOLAJO_DB_ERROR_NULL_PRIMARY_KEY'));
            $this->setError($e);
            return false;
        }

        // If tracking assets, remove the asset first.
        if ($this->_trackAssets) {
            // Get and the asset name.
            $this->$k = $pk;
            $name = $this->_getAssetName();
            $asset = new MolajoAssetModel();

            if ($asset->loadByName($name)) {
                if (!$asset->delete()) {
                    $this->setError($asset->getError());
                    return false;
                }
            }
            else {
                $this->setError($asset->getError());
                return false;
            }
        }

        // Delete the row by primary key.
        $query = $this->_db->getQuery(true);
        $query->delete();
        $query->from($this->_table);
        $query->where($this->_primary_key . ' = ' . $this->_db->quote($pk));
        $this->_db->setQuery($query->__toString());

        // Check for a database error.
        if (!$this->_db->query()) {
            $e = new MolajoException(TextHelper::sprintf('MOLAJO_DB_ERROR_DELETE_FAILED', get_class($this), $this->_db->getErrorMsg()));
            $this->setError($e);
            return false;
        }

        return true;
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
        $k = $this->_primary_key;
        $pk = (is_null($pk)) ? $this->$k : $pk;

        // If no primary key is given, return false.
        if ($pk === null) {
            $e = new MolajoException(TextHelper::_('MOLAJO_DB_ERROR_NULL_PRIMARY_KEY'));
            $this->setError($e);
            return false;
        }

        // Get the current time in MySQL format.
        $time = Molajo::Date()->toMysql();

        // Check the row out by primary key.
        $query = $this->_db->getQuery(true);
        $query->update($this->_table);
        $query->set($this->_db->quoteName('checked_out') . ' = ' . (int)$userId);
        $query->set($this->_db->quoteName('checked_out_time') . ' = ' . $this->_db->quote($time));
        $query->where($this->_primary_key . ' = ' . $this->_db->quote($pk));
        $this->_db->setQuery($query->__toString());

        if ($this->_db->query()) {
        } else {
            $e = new MolajoException(TextHelper::sprintf('MOLAJO_DB_ERROR_CHECKOUT_FAILED', get_class($this), $this->_db->getErrorMsg()));
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
        $k = $this->_primary_key;
        $pk = (is_null($pk)) ? $this->$k : $pk;

        // If no primary key is given, return false.
        if ($pk === null) {
            $e = new MolajoException(TextHelper::_('MOLAJO_DB_ERROR_NULL_PRIMARY_KEY'));
            $this->setError($e);
            return false;
        }

        // Check the row in by primary key.
        $query = $this->_db->getQuery(true);
        $query->update($this->_table);
        $query->set($this->_db->quoteName('checked_out') . ' = 0');
        $query->set($this->_db->quoteName('checked_out_time') . ' = ' . $this->_db->quote($this->_db->getNullDate()));
        $query->where($this->_primary_key . ' = ' . $this->_db->quote($pk));
        $this->_db->setQuery($query->__toString());

        // Check for a database error.
        if ($this->_db->query()) {
        } else {
            $e = new MolajoException(TextHelper::sprintf('MOLAJO_DB_ERROR_CHECKIN_FAILED', get_class($this), $this->_db->getErrorMsg()));
            $this->setError($e);
            return false;
        }

        // Set table values in the object.
        $this->checked_out = 0;
        $this->checked_out_time = '';

        return true;
    }

    /**
     * TODO: This either needs to be static or not.
     *
     * isCheckedOut
     *
     * Method to determine if a row is checked out and therefore uneditable by
     * a user.  If the row is checked out by the same user, then it is considered
     * not checked out -- as the user can still edit it.
     *
     * @param   integer  The user_id to preform the match with, if an item is checked
     *                    out by this user the function will return false.
     * @param   integer  The user_id to perform the match against when the function
     *                    is used as a static function.
     * @return  boolean  True if checked out.
     * @since   1.0
     */
    public function isCheckedOut($with = 0, $against = null)
    {
        // Handle the non-static case.
        if (isset($this) && ($this instanceof MolajoModel) && is_null($against)) {
            $against = $this->get('checked_out');
        }

        // The item is not checked out or is checked out by the same user.
        if (!$against || ($against == $with)) {
            return false;
        }

        $db = Molajo::DB();
        $db->setQuery(
            'SELECT COUNT(user_id)' .
                ' FROM ' . $db->quoteName('#__sessions') .
                ' WHERE ' . $db->quoteName('user_id') . ' = ' . (int)$against
        );
        $checkedOut = (boolean)$db->loadResult();

        // If a session exists for the user then it is checked out.
        return $checkedOut;
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
            $e = new MolajoException(TextHelper::sprintf('MOLAJO_DB_ERROR_CLASS_DOES_NOT_SUPPORT_ORDERING', get_class($this)));
            $this->setError($e);
            return false;
        }

        // Get the largest ordering value for a given where clause.
        $query = $this->_db->getQuery(true);
        $query->select('MAX(ordering)');
        $query->from($this->_table);

        if ($where) {
            $query->where($where);
        }

        $this->_db->setQuery($query->__toString());
        $max = (int)$this->_db->loadResult();

        // Check for a database error.
        if ($this->_db->getErrorNum()) {
            $e = new MolajoException(
                TextHelper::sprintf('MOLAJO_DB_ERROR_GET_NEXT_ORDER_FAILED', get_class($this), $this->_db->getErrorMsg())
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
            $e = new MolajoException(TextHelper::sprintf('MOLAJO_DB_ERROR_CLASS_DOES_NOT_SUPPORT_ORDERING', get_class($this)));
            $this->setError($e);
            return false;
        }

        // Initialise variables.
        $k = $this->_primary_key;

        // Get the primary keys and ordering values for the selection.
        $query = $this->_db->getQuery(true);
        $query->select($this->_primary_key . ', ordering');
        $query->from($this->_table);
        $query->where('ordering >= 0');
        $query->order('ordering');

        // Setup the extra where and ordering clause data.
        if ($where) {
            $query->where($where);
        }

        $this->_db->setQuery($query->__toString());
        $rows = $this->_db->loadObjectList();

        // Check for a database error.
        if ($this->_db->getErrorNum()) {
            $e = new MolajoException(TextHelper::sprintf('MOLAJO_DB_ERROR_REORDER_FAILED', get_class($this), $this->_db->getErrorMsg()));
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
                    $query = $this->_db->getQuery(true);
                    $query->update($this->_table);
                    $query->set('ordering = ' . ($i + 1));
                    $query->where($this->_primary_key . ' = ' . $this->_db->quote($row->$k));
                    $this->_db->setQuery($query->__toString());

                    // Check for a database error.
                    if ($this->_db->query()) {
                    } else {
                        $e = new MolajoException(
                            TextHelper::sprintf(
                                'MOLAJO_DB_ERROR_REORDER_UPDATE_ROW_FAILED', get_class($this), $i, $this->_db->getErrorMsg()
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
            $e = new MolajoException(TextHelper::sprintf('MOLAJO_DB_ERROR_CLASS_DOES_NOT_SUPPORT_ORDERING', get_class($this)));
            $this->setError($e);
            return false;
        }

        // If the change is none, do nothing.
        if (empty($delta)) {
            return true;
        }

        // Initialise variables.
        $k = $this->_primary_key;
        $row = null;
        $query = $this->_db->getQuery(true);

        // Select the primary key and ordering values from the table.
        $query->select($this->_primary_key . ', ordering');
        $query->from($this->_table);

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
        $this->_db->setQuery($query, 0, 1);
        $row = $this->_db->loadObject();

        // If a row is found, move the item.
        if (empty($row)) {

            // Update the ordering field for this instance.
            $query = $this->_db->getQuery(true);
            $query->update($this->_table);
            $query->set('ordering = ' . (int)$this->ordering);
            $query->where($this->_primary_key . ' = ' . $this->_db->quote($this->$k));
            $this->_db->setQuery($query->__toString());

            // Check for a database error.
            if ($this->_db->query()) {
            } else {
                $e = new MolajoException(TextHelper::sprintf('MOLAJO_DB_ERROR_MOVE_FAILED', get_class($this), $this->_db->getErrorMsg()));
                $this->setError($e);

                return false;
            }

        } else {
            // Update the ordering field for this instance to the row's ordering value.
            $query = $this->_db->getQuery(true);
            $query->update($this->_table);
            $query->set('ordering = ' . (int)$row->ordering);
            $query->where($this->_primary_key . ' = ' . $this->_db->quote($this->$k));
            $this->_db->setQuery($query->__toString());

            // Check for a database error.
            if ($this->_db->query()) {
            } else {
                $e = new MolajoException(TextHelper::sprintf('MOLAJO_DB_ERROR_MOVE_FAILED', get_class($this), $this->_db->getErrorMsg()));
                $this->setError($e);

                return false;
            }

            // Update the ordering field for the row to this instance's ordering value.
            $query = $this->_db->getQuery(true);
            $query->update($this->_table);
            $query->set('ordering = ' . (int)$this->ordering);
            $query->where($this->_primary_key . ' = ' . $this->_db->quote($row->$k));
            $this->_db->setQuery($query->__toString());

            // Check for a database error.
            if ($this->_db->query()) {
            } else {
                $e = new MolajoException(TextHelper::sprintf('MOLAJO_DB_ERROR_MOVE_FAILED', get_class($this), $this->_db->getErrorMsg()));
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
        $k = $this->_primary_key;

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
                $e = new MolajoException(TextHelper::_('MOLAJO_DB_ERROR_NO_ROWS_SELECTED'));
                $this->setError($e);

                return false;
            }
        }

        // Update the publishing state for rows with the given primary keys.
        $query = $this->_db->getQuery(true);
        $query->update($this->_table);
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

        $this->_db->setQuery($query->__toString());

        // Check for a database error.
        if ($this->_db->query()) {
        } else {
            $e = new MolajoException(TextHelper::sprintf('MOLAJO_DB_ERROR_PUBLISH_FAILED', get_class($this), $this->_db->getErrorMsg()));
            $this->setError($e);
            return false;
        }

        // If checkin is supported and all rows were adjusted, check them in.
        if ($checkin && (count($pks) == $this->_db->getAffectedRows())) {
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
        $k = $this->_primary_key;
        $pk = (is_null($pk)) ? $this->$k : $pk;

        // If no primary key is given, return false.
        if ($pk === null) {
            return false;
        }

        if (is_array($joins)) {
            // Get a query object.
            $query = $this->_db->getQuery(true);

            // Setup the basic query.
            $query->select($this->_db->quoteName($this->_primary_key));
            $query->from($this->_db->quoteName($this->_table));
            $query->where($this->_db->quoteName($this->_primary_key) . ' = ' . $this->_db->quote($this->$k));
            $query->group($this->_db->quoteName($this->_primary_key));

            // For each join add the select and join clauses to the query object.
            foreach ($joins as $table) {
                $query->select('COUNT(DISTINCT ' . $table['idfield'] . ') AS ' . $table['idfield']);
                $query->join('LEFT', $table['name'] . ' ON ' . $table['joinfield'] . ' = ' . $k);
            }

            // Get the row object from the query.
            $this->_db->setQuery((string)$query, 0, 1);
            $row = $this->_db->loadObject();

            // Check for a database error.
            if ($this->_db->getErrorNum()) {
                $this->setError($this->_db->getErrorMsg());

                return false;
            }

            $msg = array();
            $i = 0;

            foreach ($joins as $table) {
                $k = $table['idfield'] . $i;
                if ($row->$k) {
                    $msg[] = TextHelper::_($table['label']);
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

    /**
     * Method to lock the database table for writing.
     *
     * @return  boolean  True on success.
     * @since   1.0
     */
    protected function _lock()
    {
        // Lock the table for writing.
        $this->_db->setQuery('LOCK TABLES ' . $this->_db->quoteName($this->_table) . ' WRITE');
        $this->_db->query();

        // Check for a database error.
        if ($this->_db->getErrorNum()) {
            $this->setError($this->_db->getErrorMsg());

            return false;
        }

        $this->_locked = true;

        return true;
    }

    /**
     * _unlock
     *
     * Method to unlock the database table for writing.
     *
     * @return  boolean  True on success.
     * @since   1.0
     */
    protected function _unlock()
    {
        // Unlock the table.
        $this->_db->setQuery('UNLOCK TABLES');
        $this->_db->query();

        // Check for a database error.
        if ($this->_db->getErrorNum()) {
            $this->setError($this->_db->getErrorMsg());

            return false;
        }

        $this->_locked = false;

        return true;
    }
}
