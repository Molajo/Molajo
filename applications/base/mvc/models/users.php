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
     * @param   string  $id
     *
     * @return  object
     * @since   1.0
     */
    public function __construct($id = null)
    {
        $this->name = get_class($this);
        $this->table = '#__users';
        $this->primary_key = 'id';

        return parent::__construct($id);
    }

    /**
     * read
     *
     * Method to read user data
     *
     * @return  bool
     * @since   1.0
     */
    public function read()
    {
        $row = $this->_query();

        if (count($row) > 0) {
            foreach ($row as $item) {
            }
            return $this->bind($item, $ignore = array());
        } else {
            //do an empty row
        }
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
    protected function _query()
    {
        $row = parent::_query();

        /**
         * append additional data elements needed for user to the
         *   $tableQueryResults object beyond the standard results
         *   provided by the parent query
         */

        /** name */
        $row[0]['name'] = $row[0]['first_name'] . ' ' . $row[0]['last_name'];

        /** applications */
        $query = $this->db->getQuery(true);

        $query->select('a.' . $this->db->nameQuote('id'));
        $query->select('a.' . $this->db->nameQuote('name') . ' as title');
        $query->from($this->db->nameQuote('#__applications') . ' as a');
        $query->from($this->db->nameQuote('#__user_applications') . ' as b');
        $query->where('a.' . $this->db->nameQuote('id') .
            ' = b.' . $this->db->nameQuote('application_id'));
        $query->where('b.' . $this->db->nameQuote('user_id') .
            ' = ' . (int)$this->id);

        $this->db->setQuery($query->__toString());

        $row[0]['applications'] = $this->db->loadAssocList('title', 'id');

        if ($this->db->getErrorNum()) {
            $this->setError($this->db->getErrorMsg());
            return false;
        }

        /** groups */
        $query = $this->db->getQuery(true);

        $query->select('a.' . $this->db->nameQuote('id'));
        $query->select('a.' . $this->db->nameQuote('title') . ' as title');
        $query->from($this->db->nameQuote('#__content') . ' as a');
        $query->from($this->db->nameQuote('#__user_groups') . ' as b');
        $query->where('a.' . $this->db->nameQuote('id') .
            ' = b.' . $this->db->nameQuote('group_id'));
        $query->where('b.' . $this->db->nameQuote('user_id') .
            ' = ' . (int)$this->id);

        $this->db->setQuery($query->__toString());

        $row[0]['groups'] = $this->db->loadAssocList('title', 'id');

        if ($this->db->getErrorNum()) {
            $this->setError($this->db->getErrorMsg());
            return false;
        }

        /** roles */
        $row[0]['public'] = 1;
        $row[0]['guest'] = 0;
        $row[0]['registered'] = 1;
        if (in_array(5, $row[0]['groups'])) {
            $row[0]['administrator'] = 1;
        }

        /** view groups */
        $query = $this->db->getQuery(true);

        $query->select('a.' . $this->db->nameQuote('id'));
        $query->from($this->db->nameQuote('#__view_groups') . ' as a');
        $query->from($this->db->nameQuote('#__user_view_groups') . ' as b');
        $query->where('a.' . $this->db->nameQuote('id') . ' = b.' . $this->db->nameQuote('view_group_id'));
        $query->where('b.' . $this->db->nameQuote('user_id') . ' = ' . (int)$this->id);

        $this->db->setQuery($query->__toString());

        $row[0]['view_groups'] = $this->db->loadResultArray();

        if ($this->db->getErrorNum()) {
            $this->setError($this->db->getErrorMsg());
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
            $this->setError(Services::Language()->_('MOLAJO_DB_ERROR_PLEASE_ENTER_YOUR_NAME'));
            return false;
        }

        if (trim($this->username) == '') {
            $this->setError(Services::Language()->_('MOLAJO_DB_ERROR_PLEASE_ENTER_A_USER_NAME'));
            return false;
        }

        if (preg_match("#[<>\"'%;()&]#i", $this->username)
            || strlen(utf8_decode($this->username)) < 2) {
            $this->setError(Services::Language()->sprintf('MOLAJO_DB_ERROR_VALID_AZ09', 2));
            return false;
        }

        if ((trim($this->email) == "")
            || !MailServices::isEmailAddress($this->email)) {
            $this->setError(Services::Language()->_('MOLAJO_DB_ERROR_VALID_MAIL'));
            return false;
        }

        // Set the registration timestamp
        if ($this->register_datetime == null
            || $this->register_datetime == $this->db->getNullDate()) {
            $this->register_datetime = Services::Date()->toSql();
        }

        // check for existing username
        $query = 'SELECT id'
            . ' FROM #__users '
            . ' WHERE username = ' . $this->db->Quote($this->username)
            . ' AND id != ' . (int)$this->id;
        ;
        $this->db->setQuery($query->__toString());
        $xid = intval($this->db->loadResult());
        if ($xid && $xid != intval($this->id)) {
            $this->setError(Services::Language()->_('MOLAJO_DB_ERROR_USERNAME_INUSE'));
            return false;
        }

        // check for existing email
        $query = 'SELECT id'
            . ' FROM #__users '
            . ' WHERE email = ' . $this->db->Quote($this->email)
            . ' AND id != ' . (int)$this->id;
        $this->db->setQuery($query->__toString());
        $xid = intval($this->db->loadResult());
        if ($xid && $xid != intval($this->id)) {
            $this->setError(Services::Language()->_('MOLAJO_DB_ERROR_EMAIL_INUSE'));
            return false;
        }

        // Molajo - change this for a check for LAST Administrator in a Group on Delete
        // remove root user

        //			$query = $this->db->getQuery(true);
        //			$query->select('id');
        //			$query->from($this->table);
        //			$query->where('username = '.$this->db->quote($rootUser));
        //			$this->db->setQuery($query->__toString());
        //			$xid = intval($this->db->loadResult());
        //			if ($rootUser==$this->username && (!$xid || $xid && $xid != intval($this->id))  || $xid && $xid == intval($this->id) && $rootUser!=$this->username) {
        //				$this->setError( Services::Language()->_('MOLAJO_DB_ERROR_USERNAME_CANNOT_CHANGE'));
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
            $return = $this->db->updateObject($this->_tbl, $this, $this->_tbl_key, $updateNulls);
        }
        else {
            // Don't have a table key, insert the row.
            $return = $this->db->insertObject($this->_tbl, $this, $this->_tbl_key);
        }

        // Handle error if it exists.
        if ($return) {
        } else {
            $this->setError(Services::Language()->sprintf('MOLAJO_DB_ERROR_STORE_FAILED', strtolower(get_class($this)), $this->db->getErrorMsg()));
            return false;
        }

        // Reset groups to the local object.
        $this->groups = $groups;
        unset($groups);

        // Store the group data if the user data was saved.
        if ($return && is_array($this->groups) && count($this->groups)) {
            // Delete the old user group maps.
            $this->db->setQuery(
                'DELETE FROM ' . $this->db->quoteName('#__user_groups') .
                    ' WHERE ' . $this->db->quoteName('user_id') . ' = ' . (int)$this->id
            );
            $this->db->query();

            // Check for a database error.
            if ($this->db->getErrorNum()) {
                $this->setError($this->db->getErrorMsg());
                return false;
            }

            // Set the new user group maps.
            $this->db->setQuery(
                'INSERT INTO ' . $this->db->quoteName('#__user_groups') . ' (' . $this->db->quoteName('user_id') . ', ' . $this->db->quoteName('group_id') . ')' .
                    ' VALUES (' . $this->id . ', ' . implode('), (' . $this->id . ', ', $this->groups) . ')'
            );
            $this->db->query();

            // Check for a database error.
            if ($this->db->getErrorNum()) {
                $this->setError($this->db->getErrorMsg());
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
        $this->db->setQuery(
            'DELETE FROM ' . $this->db->quoteName($this->_tbl) .
                ' WHERE ' . $this->db->quoteName($this->_tbl_key) . ' = ' . (int)$this->$k
        );
        $this->db->query();

        // Check for a database error.
        if ($this->db->getErrorNum()) {
            $this->setError($this->db->getErrorMsg());
            return false;
        }

        // Delete the user group maps.
        $this->db->setQuery(
            'DELETE FROM ' . $this->db->quoteName('#__user_groups') .
                ' WHERE ' . $this->db->quoteName('user_id') . ' = ' . (int)$this->$k
        );
        $this->db->query();

        // Check for a database error.
        if ($this->db->getErrorNum()) {
            $this->setError($this->db->getErrorMsg());
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
                jexit(Services::Language()->_('MOLAJO_DB_ERROR_SETLASTVISIT'));
            }
        }

        // If no timestamp value is passed to functon, than current time is used.
        $date = Services::Date()->getDate()->format('Y-m-d-H-i-s');

        // Update the database row for the user.
        // 			' SET '.$this->db->quoteName('last_visit_datetime').' = '.$this->db->Quote($this->db->toSQLDate($date)) .
        $this->db->setQuery(
            'UPDATE ' . $this->db->quoteName($this->_tbl) .
                ' SET ' . $this->db->quoteName('last_visit_datetime') . ' = ' . $this->db->Quote($date) .
                ' WHERE ' . $this->db->quoteName('id') . ' = ' . (int)$id
        );
        $this->db->query();

        // Check for a database error.
        if ($this->db->getErrorNum()) {
            $this->setError($this->db->getErrorMsg());
            return false;
        }

        return true;
    }
}
