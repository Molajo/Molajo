<?php
/**
 * @package     Molajo
 * @subpackage  Model
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Users
 *
 * @package     Molajo
 * @subpackage  Model
 * @since       1.0
 */
class MolajoModelUsers extends MolajoModel
{
    /**
     * __construct
     *
     * @param   string  $table
     * @param   string  $key
     * @param   array   $config
     *
     * @return  object
     * @since   1.0
     */
    public function __construct($name = 'Users',
                                $prefix = 'MolajoModel',
                                $config = array())
    {
        $this->_table = '#__users';
        $this->_primary_key = 'id';

        return parent::__construct($name, $prefix, $config);
    }

    /**
     * load
     *
     * Method to load a user, applications, groups, view groups, and any other data
     * from the database to be bound to the user object.
     *
     * @param   integer  $id   An optional user id.
     *
     * @return  bool    True on success, false on failure.
     * @since   1.0
     */
    public function load($id = null, $reset = true)
    {
        parent::load($id, $reset);

        echo '<pre>';
        var_dump($this);
        echo '<pre>';
die;
        if ($return == false) {
        } else {

            $database = MolajoController::getDbo();

            /** guest */
            $this->guest = 0;

            /** applications */
            $query = $database->getQuery(true);
            $query->select('a.' . $database->nameQuote('id'));
            $query->select('a.' . $database->nameQuote('name') . ' as title');
            $query->from($database->nameQuote('#__applications') . ' as a');
            $query->from($database->nameQuote('#__user_applications') . ' as b');
            $query->where('a.' . $database->nameQuote('id') . ' = b.' . $database->nameQuote('application_id'));
            $query->where('b.' . $database->nameQuote('user_id') . ' = ' . (int)$id);

            $database->setQuery($query->__toString());

            $this->applications = $this->_database->loadAssocList('title', 'id');

            if ($this->_database->getErrorNum()) {
                $this->setError($this->_database->getErrorMsg());
                return false;
            }

            /** groups */
            $query = $database->getQuery(true);
            $query->select('a.' . $database->nameQuote('id'));
            $query->select('a.' . $database->nameQuote('title') . ' as title');
            $query->from($database->nameQuote('#__content') . ' as a');
            $query->from($database->nameQuote('#__user_groups') . ' as b');
            $query->where('a.' . $database->nameQuote('id') . ' = b.' . $database->nameQuote('group_id'));
            $query->where('b.' . $database->nameQuote('user_id') . ' = ' . (int)$id);

            $database->setQuery($query->__toString());

            $this->groups = $this->_database->loadAssocList('title', 'id');

            if ($this->_database->getErrorNum()) {
                $this->setError($this->_database->getErrorMsg());
                return false;
            }

            /** view groups */
            $query = $database->getQuery(true);
            $query->select('a.' . $database->nameQuote('id'));
            $query->from($database->nameQuote('#__view_groups') . ' as a');
            $query->from($database->nameQuote('#__user_view_groups') . ' as b');
            $query->where('a.' . $database->nameQuote('id') . ' = b.' . $database->nameQuote('view_group_id'));
            $query->where('b.' . $database->nameQuote('user_id') . ' = ' . (int)$id);

            $database->setQuery($query->__toString());

            $this->view_groups = $this->_database->loadResultArray();

            if ($this->_database->getErrorNum()) {
                $this->setError($this->_database->getErrorMsg());
                return false;
            }
        }

        return $return;
    }

    /**
     * bind
     *
     * Method to bind the user, user groups, and any other necessary data.
     *
     * @param   array  $dataArray
     * @param   mixed  $ignore
     *
     * @return  boolean
     * @since   1.0
     */
    function bind($dataArray, $ignore = '')
    {

        if (key_exists('custom_fields', $dataArray)
            && is_array($dataArray['custom_fields'])) {
            $registry = new JRegistry();
            $registry->loadArray($dataArray['custom_fields']);
            $dataArray['custom_fields'] = (string)$registry;
        }

        if (key_exists('parameters', $dataArray)
            && is_array($dataArray['parameters'])) {
            $registry = new JRegistry();
            $registry->loadArray($dataArray['parameters']);
            $dataArray['parameters'] = (string)$registry;
        }

        if (key_exists('metadata', $dataArray)
            && is_array($dataArray['metadata'])) {
            $registry = new JRegistry();
            $registry->loadArray($dataArray['metadata']);
            $dataArray['metadata'] = (string)$registry;
        }

        // Attempt to bind the data.
        $return = parent::bind($dataArray, $ignore);
        if ($return === false) {
            return false;
        }

        // Load the real group data based on the bound ids.
        if (count($this->groups > 0)) {
            // Set the group ids.
            JArrayHelper::toInteger($this->groups);

            // Get the titles for the user groups.
            $this->_database->setQuery(
                'SELECT ' . $this->_database->quoteName('id') . ', ' . $this->_database->quoteName('title') .
                    ' FROM ' . $this->_database->quoteName('#__content') .
                    ' WHERE ' . $this->_database->quoteName('id') . ' = ' . implode(' OR ' . $this->_database->quoteName('id') . ' = ', $this->groups)
            );
            // Set the titles for the user groups.
            $this->groups = $this->_database->loadAssocList('title', 'id');

            // Check for a database error.
            if ($this->_database->getErrorNum()) {
                $this->setError($this->_database->getErrorMsg());
                return false;
            }
        }

        return $return;
    }

    /**
     * Validation and filtering
     *
     * @return  boolean  True is satisfactory
     */
    function check()
    {
        if (trim($this->name) == '') {
            $this->setError(MolajoTextHelper::_('MOLAJO_DATABASE_ERROR_PLEASE_ENTER_YOUR_NAME'));
            return false;
        }

        if (trim($this->username) == '') {
            $this->setError(MolajoTextHelper::_('MOLAJO_DATABASE_ERROR_PLEASE_ENTER_A_USER_NAME'));
            return false;
        }

        if (preg_match("#[<>\"'%;()&]#i", $this->username) || strlen(utf8_decode($this->username)) < 2) {
            $this->setError(MolajoTextHelper::sprintf('MOLAJO_DATABASE_ERROR_VALID_AZ09', 2));
            return false;
        }

        if ((trim($this->email) == "") || !MolajoMailHelper::isEmailAddress($this->email)) {
            $this->setError(MolajoTextHelper::_('MOLAJO_DATABASE_ERROR_VALID_MAIL'));
            return false;
        }

        // Set the registration timestamp
        if ($this->register_datetime == null || $this->register_datetime == $this->_database->getNullDate()) {
            $this->register_datetime = MolajoController::getDate()->toMySQL();
        }


        // check for existing username
        $query = 'SELECT id'
            . ' FROM #__users '
            . ' WHERE username = ' . $this->_database->Quote($this->username)
            . ' AND id != ' . (int)$this->id;
        ;
        $this->_database->setQuery($query->__toString());
        $xid = intval($this->_database->loadResult());
        if ($xid && $xid != intval($this->id)) {
            $this->setError(MolajoTextHelper::_('MOLAJO_DATABASE_ERROR_USERNAME_INUSE'));
            return false;
        }

        // check for existing email
        $query = 'SELECT id'
            . ' FROM #__users '
            . ' WHERE email = ' . $this->_database->Quote($this->email)
            . ' AND id != ' . (int)$this->id;
        $this->_database->setQuery($query->__toString());
        $xid = intval($this->_database->loadResult());
        if ($xid && $xid != intval($this->id)) {
            $this->setError(MolajoTextHelper::_('MOLAJO_DATABASE_ERROR_EMAIL_INUSE'));
            return false;
        }

        // Molajo - change this for a check for LAST Administrator in a Group on Delete
        // remove root user

        //			$query = $this->_database->getQuery(true);
        //			$query->select('id');
        //			$query->from($this->_table);
        //			$query->where('username = '.$this->_database->quote($rootUser));
        //			$this->_database->setQuery($query->__toString());
        //			$xid = intval($this->_database->loadResult());
        //			if ($rootUser==$this->username && (!$xid || $xid && $xid != intval($this->id))  || $xid && $xid == intval($this->id) && $rootUser!=$this->username) {
        //				$this->setError( MolajoTextHelper::_('MOLAJO_DATABASE_ERROR_USERNAME_CANNOT_CHANGE'));
        //				return false;
        //			}

        return true;
    }

    /**
     * store
     *
     * @param bool $updateNulls
     * @return bool
     */
    function store($updateNulls = false)
    {
        // Get the table key and key value.
        $k = $this->_tbl_key;
        $key = $this->$k;

        // TODO: This is a dumb way to handle the groups.
        // Store groups locally so as to not update directly.
        $groups = $this->groups;
        unset($this->groups);

        // Insert or update the object based on presence of a key value.
        if ($key) {
            // Already have a table key, update the row.
            $return = $this->_database->updateObject($this->_tbl, $this, $this->_tbl_key, $updateNulls);
        }
        else {
            // Don't have a table key, insert the row.
            $return = $this->_database->insertObject($this->_tbl, $this, $this->_tbl_key);
        }

        // Handle error if it exists.
        if ($return) {
        } else {
            $this->setError(MolajoTextHelper::sprintf('MOLAJO_DATABASE_ERROR_STORE_FAILED', strtolower(get_class($this)), $this->_database->getErrorMsg()));
            return false;
        }

        // Reset groups to the local object.
        $this->groups = $groups;
        unset($groups);

        // Store the group data if the user data was saved.
        if ($return && is_array($this->groups) && count($this->groups)) {
            // Delete the old user group maps.
            $this->_database->setQuery(
                'DELETE FROM ' . $this->_database->quoteName('#__user_groups') .
                    ' WHERE ' . $this->_database->quoteName('user_id') . ' = ' . (int)$this->id
            );
            $this->_database->query();

            // Check for a database error.
            if ($this->_database->getErrorNum()) {
                $this->setError($this->_database->getErrorMsg());
                return false;
            }

            // Set the new user group maps.
            $this->_database->setQuery(
                'INSERT INTO ' . $this->_database->quoteName('#__user_groups') . ' (' . $this->_database->quoteName('user_id') . ', ' . $this->_database->quoteName('group_id') . ')' .
                    ' VALUES (' . $this->id . ', ' . implode('), (' . $this->id . ', ', $this->groups) . ')'
            );
            $this->_database->query();

            // Check for a database error.
            if ($this->_database->getErrorNum()) {
                $this->setError($this->_database->getErrorMsg());
                return false;
            }
        }

        return true;
    }

    /**
     * Method to delete a user, user groups, and any other necessary
     * data from the database.
     *
     * @param   integer  $id        An optional user id.
     *
     * @return  bool  True on success, false on failure.
     *
     * @since   1.0
     */
    function delete($id = null)
    {
        // Set the primary key to delete.
        $k = $this->_tbl_key;
        if ($id) {
            $this->$k = intval($id);
        }

        // Delete the user.
        $this->_database->setQuery(
            'DELETE FROM ' . $this->_database->quoteName($this->_tbl) .
                ' WHERE ' . $this->_database->quoteName($this->_tbl_key) . ' = ' . (int)$this->$k
        );
        $this->_database->query();

        // Check for a database error.
        if ($this->_database->getErrorNum()) {
            $this->setError($this->_database->getErrorMsg());
            return false;
        }

        // Delete the user group maps.
        $this->_database->setQuery(
            'DELETE FROM ' . $this->_database->quoteName('#__user_groups') .
                ' WHERE ' . $this->_database->quoteName('user_id') . ' = ' . (int)$this->$k
        );
        $this->_database->query();

        // Check for a database error.
        if ($this->_database->getErrorNum()) {
            $this->setError($this->_database->getErrorMsg());
            return false;
        }


        return true;
    }

    /**
     * Updates last visit time of user
     *
     * @param   integer  The timestamp, defaults to 'now'
     *
     * @return  bool  False if an error occurs
     */
    function setLastVisit($timeStamp = null, $id = null)
    {

        // Check for User ID
        if (is_null($id)) {
            if (isset($this)) {
                $id = $this->id;
            } else {
                // do not translate
                jexit(MolajoTextHelper::_('MOLAJO_DATABASE_ERROR_SETLASTVISIT'));
            }
        }

        // If no timestamp value is passed to functon, than current time is used.
        $date = MolajoController::getDate($timeStamp);

        // Update the database row for the user.
        // 			' SET '.$this->_database->quoteName('last_visit_datetime').' = '.$this->_database->Quote($this->_database->toSQLDate($date)) .
        $this->_database->setQuery(
            'UPDATE ' . $this->_database->quoteName($this->_tbl) .
                ' SET ' . $this->_database->quoteName('last_visit_datetime') . ' = ' . $this->_database->Quote($date) .
                ' WHERE ' . $this->_database->quoteName('id') . ' = ' . (int)$id
        );
        $this->_database->query();

        // Check for a database error.
        if ($this->_database->getErrorNum()) {
            $this->setError($this->_database->getErrorMsg());
            return false;
        }

        return true;
    }
}
