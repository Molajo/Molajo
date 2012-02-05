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
class MolajoUsersModel extends MolajoModel
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
    public function __construct(JRegistry $config = null)
    {
        $this->_table = '#__users';
        $this->_primary_key = 'id';

        return parent::__construct($config);
    }

    /**
     * load
     *
     * Method to load a user
     *
     * @param  integer  $id
     * @param  bool     $reset
     *
     * @return  bool    True on success, false on failure.
     * @since   1.0
     */
    public function load($id = null, $reset = true)
    {
        $this->id = $id;

        $row = $this->_query($this->id, $reset);
        foreach ($row as $item) {}
        return $this->bind($item, $ignore=array());
    }

    /**
     * _query
     *
     * Method to query the database for the data requested
     *
     * @param null $id
     * @param bool $reset
     *
     * @return bool
     * @since  1.0
     */
    protected function _query($id = null, $reset = true)
    {
        $row = parent::_query($this->id, $reset);

        /**
         * append additional data elements needed for user to the
         *   $_tableQueryResults object beyond the standard results
         *   provided by the parent query
         */

        /** guest */
        $row[0]['guest'] = 0;

        /** name */
        $row[0]['name'] = $row[0]['first_name'] . ' ' . $row[0]['last_name'];

        /** applications */
        $query = $this->_db->getQuery(true);

        $query->select('a.' . $this->_db->nameQuote('id'));
        $query->select('a.' . $this->_db->nameQuote('name') . ' as title');
        $query->from($this->_db->nameQuote('#__applications') . ' as a');
        $query->from($this->_db->nameQuote('#__user_applications') . ' as b');
        $query->where('a.' . $this->_db->nameQuote('id') .
            ' = b.' . $this->_db->nameQuote('application_id'));
        $query->where('b.' . $this->_db->nameQuote('user_id') .
            ' = ' . (int)$this->id);

        $this->_db->setQuery($query->__toString());

        $row[0]['applications'] = $this->_db->loadAssocList('title', 'id');
        if ($this->_db->getErrorNum()) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        /** groups */
        $query = $this->_db->getQuery(true);

        $query->select('a.' . $this->_db->nameQuote('id'));
        $query->select('a.' . $this->_db->nameQuote('title') . ' as title');
        $query->from($this->_db->nameQuote('#__content') . ' as a');
        $query->from($this->_db->nameQuote('#__user_groups') . ' as b');
        $query->where('a.' . $this->_db->nameQuote('id') .
            ' = b.' . $this->_db->nameQuote('group_id'));
        $query->where('b.' . $this->_db->nameQuote('user_id') .
            ' = ' . (int)$this->id);

        $this->_db->setQuery($query->__toString());

        $row[0]['groups'] = $this->_db->loadAssocList('title', 'id');

        if ($this->_db->getErrorNum()) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        /** view groups */
        $query = $this->_db->getQuery(true);

        $query->select('a.' . $this->_db->nameQuote('id'));
        $query->from($this->_db->nameQuote('#__view_groups') . ' as a');
        $query->from($this->_db->nameQuote('#__user_view_groups') . ' as b');
        $query->where('a.' . $this->_db->nameQuote('id') . ' = b.' . $this->_db->nameQuote('view_group_id'));
        $query->where('b.' . $this->_db->nameQuote('user_id') . ' = ' . (int)$this->id);

        $this->_db->setQuery($query->__toString());

        $row[0]['view_groups'] = $this->_db->loadResultArray();

        if ($this->_db->getErrorNum()) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        return $row;
    }

    /**
     * Validation and filtering
     *
     * @return  boolean  True is satisfactory
     */
    function check()
    {
        if (trim($this->name) == '') {
            $this->setError(TextHelper::_('MOLAJO_DB_ERROR_PLEASE_ENTER_YOUR_NAME'));
            return false;
        }

        if (trim($this->username) == '') {
            $this->setError(TextHelper::_('MOLAJO_DB_ERROR_PLEASE_ENTER_A_USER_NAME'));
            return false;
        }

        if (preg_match("#[<>\"'%;()&]#i", $this->username) || strlen(utf8_decode($this->username)) < 2) {
            $this->setError(TextHelper::sprintf('MOLAJO_DB_ERROR_VALID_AZ09', 2));
            return false;
        }

        if ((trim($this->email) == "") || !MailHelper::isEmailAddress($this->email)) {
            $this->setError(TextHelper::_('MOLAJO_DB_ERROR_VALID_MAIL'));
            return false;
        }

        // Set the registration timestamp
        if ($this->register_datetime == null || $this->register_datetime == $this->_db->getNullDate()) {
            $this->register_datetime = Molajo::Date()->toMySQL();
        }


        // check for existing username
        $query = 'SELECT id'
            . ' FROM #__users '
            . ' WHERE username = ' . $this->_db->Quote($this->username)
            . ' AND id != ' . (int)$this->id;
        ;
        $this->_db->setQuery($query->__toString());
        $xid = intval($this->_db->loadResult());
        if ($xid && $xid != intval($this->id)) {
            $this->setError(TextHelper::_('MOLAJO_DB_ERROR_USERNAME_INUSE'));
            return false;
        }

        // check for existing email
        $query = 'SELECT id'
            . ' FROM #__users '
            . ' WHERE email = ' . $this->_db->Quote($this->email)
            . ' AND id != ' . (int)$this->id;
        $this->_db->setQuery($query->__toString());
        $xid = intval($this->_db->loadResult());
        if ($xid && $xid != intval($this->id)) {
            $this->setError(TextHelper::_('MOLAJO_DB_ERROR_EMAIL_INUSE'));
            return false;
        }

        // Molajo - change this for a check for LAST Administrator in a Group on Delete
        // remove root user

        //			$query = $this->_db->getQuery(true);
        //			$query->select('id');
        //			$query->from($this->_table);
        //			$query->where('username = '.$this->_db->quote($rootUser));
        //			$this->_db->setQuery($query->__toString());
        //			$xid = intval($this->_db->loadResult());
        //			if ($rootUser==$this->username && (!$xid || $xid && $xid != intval($this->id))  || $xid && $xid == intval($this->id) && $rootUser!=$this->username) {
        //				$this->setError( TextHelper::_('MOLAJO_DB_ERROR_USERNAME_CANNOT_CHANGE'));
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
            $return = $this->_db->updateObject($this->_tbl, $this, $this->_tbl_key, $updateNulls);
        }
        else {
            // Don't have a table key, insert the row.
            $return = $this->_db->insertObject($this->_tbl, $this, $this->_tbl_key);
        }

        // Handle error if it exists.
        if ($return) {
        } else {
            $this->setError(TextHelper::sprintf('MOLAJO_DB_ERROR_STORE_FAILED', strtolower(get_class($this)), $this->_db->getErrorMsg()));
            return false;
        }

        // Reset groups to the local object.
        $this->groups = $groups;
        unset($groups);

        // Store the group data if the user data was saved.
        if ($return && is_array($this->groups) && count($this->groups)) {
            // Delete the old user group maps.
            $this->_db->setQuery(
                'DELETE FROM ' . $this->_db->quoteName('#__user_groups') .
                    ' WHERE ' . $this->_db->quoteName('user_id') . ' = ' . (int)$this->id
            );
            $this->_db->query();

            // Check for a database error.
            if ($this->_db->getErrorNum()) {
                $this->setError($this->_db->getErrorMsg());
                return false;
            }

            // Set the new user group maps.
            $this->_db->setQuery(
                'INSERT INTO ' . $this->_db->quoteName('#__user_groups') . ' (' . $this->_db->quoteName('user_id') . ', ' . $this->_db->quoteName('group_id') . ')' .
                    ' VALUES (' . $this->id . ', ' . implode('), (' . $this->id . ', ', $this->groups) . ')'
            );
            $this->_db->query();

            // Check for a database error.
            if ($this->_db->getErrorNum()) {
                $this->setError($this->_db->getErrorMsg());
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
        $this->_db->setQuery(
            'DELETE FROM ' . $this->_db->quoteName($this->_tbl) .
                ' WHERE ' . $this->_db->quoteName($this->_tbl_key) . ' = ' . (int)$this->$k
        );
        $this->_db->query();

        // Check for a database error.
        if ($this->_db->getErrorNum()) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        // Delete the user group maps.
        $this->_db->setQuery(
            'DELETE FROM ' . $this->_db->quoteName('#__user_groups') .
                ' WHERE ' . $this->_db->quoteName('user_id') . ' = ' . (int)$this->$k
        );
        $this->_db->query();

        // Check for a database error.
        if ($this->_db->getErrorNum()) {
            $this->setError($this->_db->getErrorMsg());
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
                jexit(TextHelper::_('MOLAJO_DB_ERROR_SETLASTVISIT'));
            }
        }

        // If no timestamp value is passed to functon, than current time is used.
        $date = Molajo::Date($timeStamp);

        // Update the database row for the user.
        // 			' SET '.$this->_db->quoteName('last_visit_datetime').' = '.$this->_db->Quote($this->_db->toSQLDate($date)) .
        $this->_db->setQuery(
            'UPDATE ' . $this->_db->quoteName($this->_tbl) .
                ' SET ' . $this->_db->quoteName('last_visit_datetime') . ' = ' . $this->_db->Quote($date) .
                ' WHERE ' . $this->_db->quoteName('id') . ' = ' . (int)$id
        );
        $this->_db->query();

        // Check for a database error.
        if ($this->_db->getErrorNum()) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        return true;
    }
}
