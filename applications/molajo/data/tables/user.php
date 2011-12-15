<?php
/**
 * @package     Molajo
 * @subpackage  Table
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * User Table Class
 *
 * @package     Molajo
 * @subpackage  Table
 * @since       1.0
 * @link
 */
class MolajoTableUser extends MolajoTable
{
    /**
     * Associative array of user => group ids
     *
     * @since   1.0
     * @var    array
     */
    var $groups;

    /**
     * @param database A database connector object
     */
    function __construct(&$db)
    {
        parent::__construct('#__users', 'id', $db);

        $this->id = 0;
        $this->send_email = 0;
    }

    /**
     * load
     *
     * Method to load a user, user groups, and any other necessary data
     * from the database so that it can be bound to the user object.
     *
     * @param   integer  $user_id   An optional user id.
     *
     * @return  bool    True on success, false on failure.
     * @since   1.0
     */
    function load($user_id = null, $reset = true)
    {
        if ($user_id === null) {
            $user_id = $this->id;
        } else {
            $this->id = $user_id;
        }

        if ($user_id === null) {
            return false;
        }

        $this->reset();

        $this->_database->setQuery(
            'SELECT *' .
            ' FROM #__users' .
            ' WHERE id = ' . (int)$user_id
        );

        $data = (array)$this->_database->loadAssoc();

echo '<pre>';var_dump($data);'</pre>';
        die;
        $this->id = $user_id;
        if ($this->_database->getErrorNum()) {
            $this->setError($this->_database->getErrorMsg());
            return false;
        }

        if (count($data)) {
        } else {
            return false;
        }

        // Bind the data to the table.
        $return = $this->bind($data);

        if ($return == false) {
        } else {
            // Load the user groups.
            $this->_database->setQuery(
                'SELECT g.id, g.title' .
                ' FROM #__content AS g' .
                ' JOIN #__user_groups AS m ON m.group_id = g.id' .
                ' WHERE m.user_id = ' . (int)$user_id
            );
            // Add the groups to the user data.
            $this->groups = $this->_database->loadAssocList('title', 'id');

            // Check for an error message.
            if ($this->_database->getErrorNum()) {
                $this->setError($this->_database->getErrorMsg());
                return false;
            }
        }

        return $return;
    }

    /**
     * Method to bind the user, user groups, and any other necessary data.
     *
     * @param   array  $array        The data to bind.
     * @param   mixed  $ignore        An array or space separated list of fields to ignore.
     *
     * @return  boolean  True on success, false on failure.
     *
     * @since   1.0
     */
    function bind($array, $ignore = '')
    {
        if (key_exists('parameters', $array) && is_array($array['parameters'])) {
            $registry = new JRegistry();
            $registry->loadArray($array['parameters']);
            $array['parameters'] = (string)$registry;
        }

        // Attempt to bind the data.
        $return = parent::bind($array, $ignore);

        // Load the real group data based on the bound ids.
        if ($return && !empty($this->groups)) {
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
            $this->register_datetime = MolajoFactory::getDate()->toMySQL();
        }


        // check for existing username
        $query = 'SELECT id'
                 . ' FROM #__users '
                 . ' WHERE username = ' . $this->_database->Quote($this->username)
                 . ' AND id != ' . (int)$this->id;
        ;
        $this->_database->setQuery($query);
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
        $this->_database->setQuery($query);
        $xid = intval($this->_database->loadResult());
        if ($xid && $xid != intval($this->id)) {
            $this->setError(MolajoTextHelper::_('MOLAJO_DATABASE_ERROR_EMAIL_INUSE'));
            return false;
        }

        // Molajo - change this for a check for LAST Administrator in a Group on Delete
        // remove root user

        //			$query = $this->_database->getQuery(true);
        //			$query->select('id');
        //			$query->from('#__users');
        //			$query->where('username = '.$this->_database->quote($rootUser));
        //			$this->_database->setQuery($query);
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
        if (!$return) {
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
     * @param   integer  $user_id        An optional user id.
     *
     * @return  bool  True on success, false on failure.
     *
     * @since   1.0
     */
    function delete($user_id = null)
    {
        // Set the primary key to delete.
        $k = $this->_tbl_key;
        if ($user_id) {
            $this->$k = intval($user_id);
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
    function setLastVisit($timeStamp = null, $user_id = null)
    {

        // Check for User ID
        if (is_null($user_id)) {
            if (isset($this)) {
                $user_id = $this->id;
            } else {
                // do not translate
                jexit(MolajoTextHelper::_('MOLAJO_DATABASE_ERROR_SETLASTVISIT'));
            }
        }

        // If no timestamp value is passed to functon, than current time is used.
        $date = MolajoFactory::getDate($timeStamp);

        // Update the database row for the user.
        // 			' SET '.$this->_database->quoteName('last_visit_datetime').' = '.$this->_database->Quote($this->_database->toSQLDate($date)) .
        $this->_database->setQuery(
            'UPDATE ' . $this->_database->quoteName($this->_tbl) .
            ' SET ' . $this->_database->quoteName('last_visit_datetime') . ' = ' . $this->_database->Quote($date) .
            ' WHERE ' . $this->_database->quoteName('id') . ' = ' . (int)$user_id
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
