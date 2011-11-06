<?php
/**
 * @package     Molajo
 * @subpackage  Table
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Session Table Class
 *
 * @package     Molajo
 * @subpackage  Table
 * @since       1.0
 * @link
 */
class MolajoTableSession extends MolajoTable
{
    /**
     * Constructor
     * @param database A database connector object
     */
    function __construct(&$db)
    {
        parent::__construct('#__session', 'session_id', $db);

        $this->guest = 1;
        $this->username = '';
    }

    function insert($sessionId, $application_id)
    {
        $this->session_id = $sessionId;
        $this->application_id = $application_id;

        $this->time = time();
        $ret = $this->_db->insertObject($this->_tbl, $this, 'session_id');

        if (!$ret) {
            $this->setError(MolajoText::sprintf('MOLAJO_DATABASE_ERROR_STORE_FAILED', strtolower(get_class($this)), $this->_db->stderr()));
            return false;
        } else {
            return true;
        }
    }

    function update($updateNulls = false)
    {
        $this->time = time();
        $ret = $this->_db->updateObject($this->_tbl, $this, 'session_id', $updateNulls);

        if (!$ret) {
            $this->setError(MolajoText::sprintf('MOLAJO_DATABASE_ERROR_STORE_FAILED', strtolower(get_class($this)), $this->_db->stderr()));
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

        $query = 'DELETE FROM #__session'
                 . ' WHERE userid = ' . $this->_db->Quote($userId)
                 . ' AND application_id IN (' . $application_ids . ')';
        $this->_db->setQuery($query);

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
        $query = 'DELETE FROM ' . $this->_tbl . ' WHERE (time < \'' . (int)$past . '\')'; // Index on 'VARCHAR'
        $this->_db->setQuery($query);

        return $this->_db->query();
    }

    /**
     * Find out if a user has a one or more active sessions
     *
     * @param   integer  $userid The identifier of the user
     *
     * @return  boolean  True if a session for this user exists
     *
     * @since   1.0
     */
    function exists($userid)
    {
        $query = 'SELECT COUNT(userid) FROM #__session'
                 . ' WHERE userid = ' . $this->_db->Quote($userid);
        $this->_db->setQuery($query);

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
        //if (!$this->canDelete($msg))
        //{
        //	return $msg;
        //}

        $k = $this->_tbl_key;
        if ($oid) {
            $this->$k = $oid;
        }

        $query = 'DELETE FROM ' . $this->_db->quoteName($this->_tbl) .
                 ' WHERE ' . $this->_tbl_key . ' = ' . $this->_db->Quote($this->$k);
        $this->_db->setQuery($query);

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
