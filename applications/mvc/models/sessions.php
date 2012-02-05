<?php
/**
 * @package     Molajo
 * @subpackage  Model
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Extension Instances
 *
 * @package     Molajo
 * @subpackage  Model
 * @since       1.0
 * @link
 */
class MolajoSessionsModel extends MolajoModel
{
    /**
     * Constructor
     * @param database A database connector object
     */
    function __construct($db)
    {
        parent::__construct('#__sessions', 'session_id', $db);

        $this->username = '';
    }

    function insert($sessionId, $application_id)
    {
        $this->session_id = $sessionId;

        $this->application_id = $application_id;

        $this->session_time = time();
        $ret = $this->_db->insertObject($this->_tbl, $this, 'session_id');

        if (!$ret) {
            $this->setError(TextHelper::sprintf('MOLAJO_DB_ERROR_STORE_FAILED', strtolower(get_class($this)), $this->_db->stderr()));
            return false;
        } else {
            return true;
        }
    }

    function update($updateNulls = false)
    {
        $this->session_time = time();
        $ret = $this->_db->updateObject($this->_tbl, $this, 'session_id', $updateNulls);

        if (!$ret) {
            $this->setError(TextHelper::sprintf('MOLAJO_DB_ERROR_STORE_FAILED', strtolower(get_class($this)), $this->_db->stderr()));
            return false;
        } else {
            return true;
        }
    }

    /**
     * Destroys the pesisting session
     */
    function destroy($userId, $application_ids = array())
    {
        $application_ids = implode(',', $application_ids);

        $query = 'DELETE FROM #__sessions'
                 . ' WHERE user_id = ' . $this->_db->Quote($userId)
                 . ' AND application_id IN (' . $application_ids . ')';

        $this->_db->setQuery($query->__toString());

        if (!$this->_db->query()) {
            $this->setError($this->_db->stderr());
            return false;
        }

        return true;
    }

    /**
     * Purge old sessions
     *
     * @param   integer  Session age in seconds
     *
     * @return  mixed    Resource on success, null on fail
     */
    function purge($maxLifetime = 1440)
    {
        $past = time() - $maxLifetime;
        $query = 'DELETE FROM ' . $this->_tbl . ' WHERE (session_time < \'' . (int)$past . '\')'; // Index on 'VARCHAR'
        $this->_db->setQuery($query->__toString());

        return $this->_db->query();
    }

    /**
     * Find out if a user has a one or more active sessions
     *
     * @param   integer  $user_id The identifier of the user
     *
     * @return  boolean  True if a session for this user exists
     *
     * @since   1.0
     */
    function exists($user_id)
    {
        $query = 'SELECT COUNT(user_id) FROM #__sessions'
                 . ' WHERE user_id = ' . $this->_db->Quote($user_id);

        $this->_db->setQuery($query->__toString());

        if (!$result = $this->_db->loadResult()) {
            $this->setError($this->_db->stderr());
            return false;
        }

        return (boolean)$result;
    }

    /**
     * Overloaded delete method
     *
     * We must override it because of the non-integer primary key
     *
     * @return true if successful otherwise returns and error message
     */
    function delete($oid = null)
    {
        $k = $this->_tbl_key;
        if ($oid) {
            $this->$k = $oid;
        }

        $query = 'DELETE FROM ' . $this->_db->quoteName($this->_tbl) .
                 ' WHERE ' . $this->_tbl_key . ' = ' . $this->_db->Quote($this->$k);

        $this->_db->setQuery($query->__toString());

        if ($this->_db->query()) {
            return true;
        }
        else
        {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }
    }
}
