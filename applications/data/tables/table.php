<?php
/**
 * @package     Molajo
 * @subpackage  Table
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Table Class
 *
 * @package     Molajo
 * @subpackage  Table
 * @since       1.0
 */
abstract class MolajoTable extends JObject
{
    /**
     * Name of the database table
     *
     * @var    string
     * @since  1.0
     */
    protected $_table = '';

    /**
     * Name of the primary key field in the table.
     *
     * @var    string
     * @since  1.0
     */
    protected $_primary_key = '';

    /**
     * JDatabase connector object.
     *
     * @var    object
     * @since  1.0
     */
    protected $_database;

    /**
     * Indicator that the tables have been locked.
     *
     * @var    boolean
     * @since  1.0
     */
    protected $_locked = false;

    /**
     * getInstance
     *
     * Static method to get an instance of a MolajoTable class if it can be found in
     * the table include paths.  To add include paths for searching for MolajoTable
     * classes @see MolajoTable::addIncludePath().
     *
     * @param   string   The type (name) of the MolajoTable class to get an instance of.
     * @param   string   An optional prefix for the table class name.
     * @param   array    An optional array of configuration values for the MolajoTable object.
     * @return  mixed    A MolajoTable object if found or boolean false if one could not be found.
     * @since   1.0
     */
    public static function getInstance($type, $prefix = 'MolajoTable', $config = array())
    {
        $type = preg_replace('/[^A-Z0-9_\.-]/i', '', $type);
        $tableClass = $prefix . ucfirst($type);

        if (class_exists($tableClass)) {

        } else {
                MolajoError::raiseWarning(0, MolajoTextHelper::sprintf('MOLAJO_DATABASE_ERROR_NOT_SUPPORTED_FILE_NOT_FOUND', $type));
                return false;
        }

        if (isset($config['databaseo'])) {
        } else {
            $database = MolajoController::getDbo();
        }
        $database = $config['databaseo'];

        return new $tableClass($database);
    }

    /**
     * __construct
     *
     * Object constructor to set table and key fields.  In most cases this will
     * be overridden by child classes to explicitly set the table and key fields
     * for a particular database table.
     *
     * @param   string Name of the table to model.
     * @param   string Name of the primary key field in the table.
     * @param   object JDatabase connector object.
     *
     * @since  1.0
     */
    function __construct($table, $key, $database)
    {
        $this->_table = $table;
        $this->_primary_key = $key;
        $this->_database = $database;

        if ($names = $this->getFields()) {
            foreach ($names as $name => $v) {
                if (property_exists($this, $name)) {
                } else {
                    $this->$name = null;
                }
            }
        }
    }

    /**
     * getFields
     *
     * Get the columns from database table.
     *
     * @return  mixed  An array of the field names, or false if an error occurs.
     */
    public function getFields()
    {
        static $cache = null;

        if ($cache === null) {
            $name = $this->_table;
            $names = $this->_database->getTableFields($name, false);

            if (isset($names[$name])) {
            } else {
                $e = new MolajoException(MolajoTextHelper::_('MOLAJO_DATABASE_ERROR_COLUMNS_NOT_FOUND'));
                $this->setError($e);
                return false;
            }
            $cache = $names[$name];
        }

        return $cache;
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
     * @return  string  The name of the primary key for the table.
     * @since   1.0
     */
    public function getKeyName()
    {
        return $this->_primary_key;
    }

    /**
     * getDbo
     *
     * Method to get the JDatabase connector object.
     *
     * @return  object
     */
    public function getDbo()
    {
        return $this->_database;
    }

    /**
     * setDbo
     *
     * Method to set the JDatabase connector object.
     *
     * @param   object   A JDatabase connector object to be used by the table object.
     * @return  boolean  True on success.
     * @link    http://docs.molajo.org/MolajoTable/setDbo
     */
    public function setDbo($database)
    {
        if ($database instanceof JDatabase) {
        } else {
            return false;
        }

        $this->_database = $database;

        return true;
    }

    /**
     * load
     *
     * Method to load a row from the database by primary key and bind the fields
     * to the MolajoTable instance properties.
     *
     * @param   mixed  An optional primary key value to load the row by, or an array of fields to match.  If not
     *                 set the instance property value is used.
     * @param   bool   True to reset the default values before loading the new row.
     *
     * @return  bool  True if successful. False if row not found or on error (internal error state set in that case).
     *
     * @since   1.0
     */
    public function load($keys = null, $reset = true)
    {
        if (empty($keys)) {
            $keyName = $this->_primary_key;
            $keyValue = $this->$keyName;

            if (empty($keyValue)) {
                return true;
            }
            $keys = array($keyName => $keyValue);

        } else if (is_array($keys)) {

        } else {
            $keys = array($this->_primary_key => $keys);
        }

        if ($reset) {
            $this->reset();
        }

        $query = $this->_database->getQuery(true);
        $query->select('*');
        $query->from($this->_table);
        $names = array_keys($this->getProperties());

        foreach ($keys as $name => $value)
        {
            if (in_array($name, $names)) {
            } else {
                $e = new MolajoException(MolajoTextHelper::sprintf('MOLAJO_DATABASE_ERROR_CLASS_IS_MISSING_FIELD', get_class($this), $name));
                $this->setError($e);
                return false;
            }
            $query->where($this->_database->quoteName($name) . ' = ' . $this->_database->quote($value));
        }

        $this->_database->setQuery($query);
        $row = $this->_database->loadAssoc();

        if ($this->_database->getErrorNum()) {
            $e = new MolajoException($this->_database->getErrorMsg());
            $this->setError($e);
            return false;
        }

        if (empty($row)) {
            $e = new MolajoException(MolajoTextHelper::_('MOLAJO_DATABASE_ERROR_EMPTY_ROW_RETURNED'));
            $this->setError($e);
            return false;
        }

        return $this->bind($row);
    }

    /**
     * reset
     *
     * Method to reset class properties to the defaults set in the class
     * Ignores primary key and private class properties
     *
     * @return  void
     * @since   1.0
     */
    public function reset()
    {
        foreach ($this->getFields() as $k => $v)
        {
            if ($k == $this->_primary_key
                || (strpos($k, '_') == 0)) {
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
     * @param   mixed   An associative array or object to bind to the MolajoTable instance.
     * @param   string  Filter for the order updating
     * @param   mixed   An optional array or space separated list of properties
     *                    to ignore while binding.
     *
     * @return  boolean  True on success.
     *
     * @link    http://docs.molajo.org/MolajoTable/save
     * @since   1.0
     */
    public function save($source, $orderingFilter = '', $ignore = '')
    {
        // Attempt to bind the source to the instance.
        if ($this->bind($source, $ignore)) {
        } else {
            return false;
        }

        // Run any sanity checks on the instance and verify that it is ready for storage.
        if ($this->check()) {
        } else {
            return false;
        }

        // Attempt to store the properties to the database table.
        if ($this->store()) {
        } else {
            return false;
        }

        // Attempt to check the row in, just in case it was checked out.
        if ($this->checkin()) {
        } else {
            return false;
        }

        // If an ordering filter is set, attempt reorder the rows in the table based on the filter and value.
        if ($orderingFilter) {
            $filterValue = $this->$orderingFilter;
            $this->reorder($orderingFilter
                                   ? $this->_database->quoteName($orderingFilter) . ' = ' . $this->_database->Quote($filterValue)
                                   : '');
        }

        // Set the error to empty and return true.
        $this->setError('');

        return true;
    }

    /**
     * bind
     *
     * Method to bind an associative array or object to the MolajoTable instance. This
     * method only binds properties that are publicly accessible and optionally
     * takes an array of properties to ignore when binding.
     *
     * @param   mixed  Data to bind to the table
     * @param   mixed  Properties to not bind (optional)
     *
     * @return  boolean  True on success.
     *
     * @since   1.0
     */
    public function bind($source, $ignore = array())
    {
        if (is_object($source)
            || is_array($source)) {
        } else {
            $e = new MolajoException(MolajoTextHelper::sprintf('MOLAJO_DATABASE_ERROR_BIND_FAILED_INVALID_SOURCE_ARGUMENT', get_class($this)));
            $this->setError($e);
            return false;
        }

        if (is_object($source)) {
            $source = get_object_vars($source);
        }

        if (is_array($ignore)) {
        } else {
            $ignore = explode(' ', $ignore);
        }

        foreach ($this->getProperties() as $k => $v) {
            if (in_array($k, $ignore)) {
            } else {
                if (isset($source[$k])) {
                    $this->$k = $source[$k];
                }
            }
        }

        return true;
    }

    /**
     * check
     *
     * Method to perform sanity checks on the MolajoTable instance properties to ensure
     * they are safe to store in the database.  Child classes should override this
     * method to make sure the data they are storing in the database is safe and
     * as expected before storage.
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
     *
     * @since   1.0
     */
    public function store($updateNulls = false)
    {
        $k = $this->_primary_key;

        if ($this->$k) {
            $stored = $this->_database->updateObject($this->_table, $this, $this->_primary_key, $updateNulls);
        } else {
            $stored = $this->_database->insertObject($this->_table, $this, $this->_primary_key);
        }

        if ($stored) {
        } else {
            $e = new MolajoException(MolajoTextHelper::sprintf('MOLAJO_DATABASE_ERROR_STORE_FAILED', get_class($this), $this->_database->getErrorMsg()));
            $this->setError($e);
            return false;
        }

        if ($this->_locked) {
            $this->_unlock();
        }

        /** Asset tracking */
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
        $asset = MolajoTable::getInstance('Asset');

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
        //		$grouping = MolajoTable::getInstance('Grouping');

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
        //			$query = $this->_database->getQuery(true);
        //			$query->update($this->_database->quoteName($this->_table));
        //			$query->set('asset_id = '.(int) $this->asset_id);
        //			$query->where($this->_database->quoteName($k).' = '.(int) $this->$k);
        //			$this->_database->setQuery($query);

        //			if ($this->_database->query()) {
        //            } else {
        //				$e = new MolajoException(MolajoTextHelper::sprintf('MOLAJO_DATABASE_ERROR_STORE_FAILED_UPDATE_ASSET_ID', $this->_database->getErrorMsg()));
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
        // Initialise variables.
        $k = $this->_primary_key;
        $pk = (is_null($pk)) ? $this->$k : $pk;

        // If no primary key is given, return false.
        if ($pk === null) {
            $e = new MolajoException(MolajoTextHelper::_('MOLAJO_DATABASE_ERROR_NULL_PRIMARY_KEY'));
            $this->setError($e);
            return false;
        }

        // If tracking assets, remove the asset first.
        if ($this->_trackAssets) {
            // Get and the asset name.
            $this->$k = $pk;
            $name = $this->_getAssetName();
            $asset = MolajoTable::getInstance('Asset');

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
        $query = $this->_database->getQuery(true);
        $query->delete();
        $query->from($this->_table);
        $query->where($this->_primary_key . ' = ' . $this->_database->quote($pk));
        $this->_database->setQuery($query);

        // Check for a database error.
        if (!$this->_database->query()) {
            $e = new MolajoException(MolajoTextHelper::sprintf('MOLAJO_DATABASE_ERROR_DELETE_FAILED', get_class($this), $this->_database->getErrorMsg()));
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
            $e = new MolajoException(MolajoTextHelper::_('MOLAJO_DATABASE_ERROR_NULL_PRIMARY_KEY'));
            $this->setError($e);
            return false;
        }

        // Get the current time in MySQL format.
        $time = MolajoController::getDate()->toMysql();

        // Check the row out by primary key.
        $query = $this->_database->getQuery(true);
        $query->update($this->_table);
        $query->set($this->_database->quoteName('checked_out') . ' = ' . (int)$userId);
        $query->set($this->_database->quoteName('checked_out_time') . ' = ' . $this->_database->quote($time));
        $query->where($this->_primary_key . ' = ' . $this->_database->quote($pk));
        $this->_database->setQuery($query);

        if ($this->_database->query()) {
        } else {
            $e = new MolajoException(MolajoTextHelper::sprintf('MOLAJO_DATABASE_ERROR_CHECKOUT_FAILED', get_class($this), $this->_database->getErrorMsg()));
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
            $e = new MolajoException(MolajoTextHelper::_('MOLAJO_DATABASE_ERROR_NULL_PRIMARY_KEY'));
            $this->setError($e);
            return false;
        }

        // Check the row in by primary key.
        $query = $this->_database->getQuery(true);
        $query->update($this->_table);
        $query->set($this->_database->quoteName('checked_out') . ' = 0');
        $query->set($this->_database->quoteName('checked_out_time') . ' = ' . $this->_database->quote($this->_database->getNullDate()));
        $query->where($this->_primary_key . ' = ' . $this->_database->quote($pk));
        $this->_database->setQuery($query);

        // Check for a database error.
        if ($this->_database->query()) {
        } else {
            $e = new MolajoException(MolajoTextHelper::sprintf('MOLAJO_DATABASE_ERROR_CHECKIN_FAILED', get_class($this), $this->_database->getErrorMsg()));
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
        if (isset($this) && ($this instanceof MolajoTable) && is_null($against)) {
            $against = $this->get('checked_out');
        }

        // The item is not checked out or is checked out by the same user.
        if (!$against || ($against == $with)) {
            return false;
        }

        $database = MolajoController::getDbo();
        $database->setQuery(
            'SELECT COUNT(user_id)' .
            ' FROM ' . $database->quoteName('#__sessions') .
            ' WHERE ' . $database->quoteName('user_id') . ' = ' . (int)$against
        );
        $checkedOut = (boolean)$database->loadResult();

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
            $e = new MolajoException(MolajoTextHelper::sprintf('MOLAJO_DATABASE_ERROR_CLASS_DOES_NOT_SUPPORT_ORDERING', get_class($this)));
            $this->setError($e);
            return false;
        }

        // Get the largest ordering value for a given where clause.
        $query = $this->_database->getQuery(true);
        $query->select('MAX(ordering)');
        $query->from($this->_table);

        if ($where) {
            $query->where($where);
        }

        $this->_database->setQuery($query);
        $max = (int)$this->_database->loadResult();

        // Check for a database error.
        if ($this->_database->getErrorNum()) {
            $e = new MolajoException(
                MolajoTextHelper::sprintf('MOLAJO_DATABASE_ERROR_GET_NEXT_ORDER_FAILED', get_class($this), $this->_database->getErrorMsg())
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
     * @link    http://docs.molajo.org/MolajoTable/reorder
     */
    public function reorder($where = '')
    {
        // If there is no ordering field set an error and return false.
        if (property_exists($this, 'ordering')) {
        } else {
            $e = new MolajoException(MolajoTextHelper::sprintf('MOLAJO_DATABASE_ERROR_CLASS_DOES_NOT_SUPPORT_ORDERING', get_class($this)));
            $this->setError($e);
            return false;
        }

        // Initialise variables.
        $k = $this->_primary_key;

        // Get the primary keys and ordering values for the selection.
        $query = $this->_database->getQuery(true);
        $query->select($this->_primary_key . ', ordering');
        $query->from($this->_table);
        $query->where('ordering >= 0');
        $query->order('ordering');

        // Setup the extra where and ordering clause data.
        if ($where) {
            $query->where($where);
        }

        $this->_database->setQuery($query);
        $rows = $this->_database->loadObjectList();

        // Check for a database error.
        if ($this->_database->getErrorNum()) {
            $e = new MolajoException(MolajoTextHelper::sprintf('MOLAJO_DATABASE_ERROR_REORDER_FAILED', get_class($this), $this->_database->getErrorMsg()));
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
                    $query = $this->_database->getQuery(true);
                    $query->update($this->_table);
                    $query->set('ordering = ' . ($i + 1));
                    $query->where($this->_primary_key . ' = ' . $this->_database->quote($row->$k));
                    $this->_database->setQuery($query);

                    // Check for a database error.
                    if ($this->_database->query()) {
                    } else {
                        $e = new MolajoException(
                            MolajoTextHelper::sprintf(
                                'MOLAJO_DATABASE_ERROR_REORDER_UPDATE_ROW_FAILED', get_class($this), $i, $this->_database->getErrorMsg()
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
     * @link    http://docs.molajo.org/MolajoTable/move
     */
    public function move($delta, $where = '')
    {
        // If there is no ordering field set an error and return false.
        if (property_exists($this, 'ordering')) {
        } else {
            $e = new MolajoException(MolajoTextHelper::sprintf('MOLAJO_DATABASE_ERROR_CLASS_DOES_NOT_SUPPORT_ORDERING', get_class($this)));
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
        $query = $this->_database->getQuery(true);

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
        $this->_database->setQuery($query, 0, 1);
        $row = $this->_database->loadObject();

        // If a row is found, move the item.
        if (empty($row)) {

            // Update the ordering field for this instance.
            $query = $this->_database->getQuery(true);
            $query->update($this->_table);
            $query->set('ordering = ' . (int)$this->ordering);
            $query->where($this->_primary_key . ' = ' . $this->_database->quote($this->$k));
            $this->_database->setQuery($query);

            // Check for a database error.
            if ($this->_database->query()) {
            } else {
                $e = new MolajoException(MolajoTextHelper::sprintf('MOLAJO_DATABASE_ERROR_MOVE_FAILED', get_class($this), $this->_database->getErrorMsg()));
                $this->setError($e);

                return false;
            }

        } else {
            // Update the ordering field for this instance to the row's ordering value.
            $query = $this->_database->getQuery(true);
            $query->update($this->_table);
            $query->set('ordering = ' . (int)$row->ordering);
            $query->where($this->_primary_key . ' = ' . $this->_database->quote($this->$k));
            $this->_database->setQuery($query);

            // Check for a database error.
            if ($this->_database->query()) {
            } else {
                $e = new MolajoException(MolajoTextHelper::sprintf('MOLAJO_DATABASE_ERROR_MOVE_FAILED', get_class($this), $this->_database->getErrorMsg()));
                $this->setError($e);

                return false;
            }

            // Update the ordering field for the row to this instance's ordering value.
            $query = $this->_database->getQuery(true);
            $query->update($this->_table);
            $query->set('ordering = ' . (int)$this->ordering);
            $query->where($this->_primary_key . ' = ' . $this->_database->quote($row->$k));
            $this->_database->setQuery($query);

            // Check for a database error.
            if ($this->_database->query()) {
            } else {
                $e = new MolajoException(MolajoTextHelper::sprintf('MOLAJO_DATABASE_ERROR_MOVE_FAILED', get_class($this), $this->_database->getErrorMsg()));
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
                $e = new MolajoException(MolajoTextHelper::_('MOLAJO_DATABASE_ERROR_NO_ROWS_SELECTED'));
                $this->setError($e);

                return false;
            }
        }

        // Update the publishing state for rows with the given primary keys.
        $query = $this->_database->getQuery(true);
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

        $this->_database->setQuery($query);

        // Check for a database error.
        if ($this->_database->query()) {
        } else {
            $e = new MolajoException(MolajoTextHelper::sprintf('MOLAJO_DATABASE_ERROR_PUBLISH_FAILED', get_class($this), $this->_database->getErrorMsg()));
            $this->setError($e);
            return false;
        }

        // If checkin is supported and all rows were adjusted, check them in.
        if ($checkin && (count($pks) == $this->_database->getAffectedRows())) {
            // Checkin the rows.
            foreach ($pks as $pk)
            {
                $this->checkin($pk);
            }
        }

        // If the MolajoTable instance value is in the list of primary keys that were set, set the instance.
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
            $query = $this->_database->getQuery(true);

            // Setup the basic query.
            $query->select($this->_database->quoteName($this->_primary_key));
            $query->from($this->_database->quoteName($this->_table));
            $query->where($this->_database->quoteName($this->_primary_key) . ' = ' . $this->_database->quote($this->$k));
            $query->group($this->_database->quoteName($this->_primary_key));

            // For each join add the select and join clauses to the query object.
            foreach ($joins as $table) {
                $query->select('COUNT(DISTINCT ' . $table['idfield'] . ') AS ' . $table['idfield']);
                $query->join('LEFT', $table['name'] . ' ON ' . $table['joinfield'] . ' = ' . $k);
            }

            // Get the row object from the query.
            $this->_database->setQuery((string)$query, 0, 1);
            $row = $this->_database->loadObject();

            // Check for a database error.
            if ($this->_database->getErrorNum()) {
                $this->setError($this->_database->getErrorMsg());

                return false;
            }

            $msg = array();
            $i = 0;

            foreach ($joins as $table) {
                $k = $table['idfield'] . $i;
                if ($row->$k) {
                    $msg[] = MolajoTextHelper::_($table['label']);
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
        $this->_database->setQuery('LOCK TABLES ' . $this->_database->quoteName($this->_table) . ' WRITE');
        $this->_database->query();

        // Check for a database error.
        if ($this->_database->getErrorNum()) {
            $this->setError($this->_database->getErrorMsg());

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
        $this->_database->setQuery('UNLOCK TABLES');
        $this->_database->query();

        // Check for a database error.
        if ($this->_database->getErrorNum()) {
            $this->setError($this->_database->getErrorMsg());

            return false;
        }

        $this->_locked = false;

        return true;
    }
}
