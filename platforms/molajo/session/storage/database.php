<?php
/**
 * @package     Molajo
 * @subpackage  Helper
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Database session storage handler for PHP
 *
 * @package     Joomla.Platform
 * @subpackage  Session
 * @see         http://www.php.net/manual/en/function.session-set-save-handler.php
 * @since       11.1
 */
class MolajoSessionStorageDatabase extends MolajoSessionStorage
{

    /**
     * Open the SessionHandler backend.
     *
     * @param   string  $save_path     The path to the session object.
     * @param   string  $session_name  The name of the session.
     *
     * @return  boolean  True on success, false otherwise.
     *
     * @since   11.1
     */
    public function open($save_path, $session_name)
    {
        return true;
    }

    /**
     * Close the SessionHandler backend.
     *
     * @return  boolean  True on success, false otherwise.
     *
     * @since   11.1
     */
    public function close()
    {
        return true;
    }

    /**
     * read
     *
     * Retrieve data for session
     *
     * @param   string  $id
     *
     * @return  string  session data
     * @since   1.0
     */
    public function read($id)
    {
        $m = new MolajoSessionsModel ();

        $m->query->select($m->db->qn('data'));
        $m->query->where($m->db->qn('session_id')
            . ' = ' . $m->db->quote($id));

        return (string)$m->loadResult();
    }

    /**
     * Write session data to the SessionHandler backend.
     *
     * @param   string  $id    The session identifier.
     * @param   string  $data  The session data.
     *
     * @return  boolean  True on success, false otherwise.
     *
     * @since   11.1
     */
    public function write($id, $data)
    {
        /** Does the session exist? */
        $m = new MolajoSessionsModel();
        $m->query->where($m->db->qn('session_id')
            . ' = ' . $m->db->q($id));

        $results = $m->loadResult();

        if (empty($results)) {
            $action = 'insert';
        } else {
            $action = 'update';
        }

        $m->session_id = $id;
        $m->data = $data;

        $results = $m->$action();

        if ($results === false) {
            echo 'false false false';
            die;
        }
        echo 'true true true';
        die;
        if ($results == false) {
            echo 'false';
        } else {
            echo 'true';
        }

    }

    /**
     * Destroy the data for a particular session identifier in the SessionHandler backend.
     *
     * @param   string  $id  The session identifier.
     *
     * @return  boolean  True on success, false otherwise.
     *
     * @since   11.1
     */
    public function destroy($id)
    {

    }

    /**
     * Garbage collect stale sessions from the SessionHandler backend.
     *
     * @param   integer  $lifetime  The maximum age of a session.
     *
     * @return  boolean  True on success, false otherwise.
     *
     * @since   11.1
     */
    public function gc($lifetime = 1440)
    {

    }
}
