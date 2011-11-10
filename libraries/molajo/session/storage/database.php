<?php
/**
 * @package     Molajo
 * @subpackage  Session
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Individual Molajo Contributors. All rights reserved.
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
     * Read the data for a particular session identifier from the SessionHandler backend.
     *
     * @param   string  $id  The session identifier.
     *
     * @return  string  The session data.
     *
     * @since   11.1
     */
    public function read($id)
    {
        $db = MolajoFactory::getDBO();
        if ($db->connected()) {
        } else {
            return false;
        }

        $query = $db->getQuery(true);
        $query->select($db->quoteName('data'));
        $query->from($db->quoteName('#__sessions'));
        $query->where($db->quoteName('session_id') . ' = ' . $db->quote($id));

        $db->setQuery($query->__toString());

        return (string)$db->loadResult();
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
        $db = MolajoFactory::getDBO();
        if ($db->connected()) {
        } else {
            return false;
        }

        $query = $db->getQuery(true);

        $query->update($db->quoteName('#__sessions'));
        $query->set($db->quoteName('data') . ' = ' . $db->quote($data));
        $query->set($db->quoteName('session_time') . ' = ' . (int)time());
        $query->where($db->quoteName('session_id') . ' = ' . $db->quote($id));

        $db->setQuery($query->__toString());
        $db->query();

        if ($db->getAffectedRows()) {
            return true;

        } else {
            $db->setQuery(
                'INSERT INTO ' . $db->quoteName('#__sessions') .
                ' (' . $db->quoteName('session_id') . ', ' . $db->quoteName('data') . ', ' . $db->quoteName('session_time') . ')' .
                ' VALUES (' . $db->quote($id) . ', ' . $db->quote($data) . ', ' . (int)time() . ')'
            );
            return (boolean)$db->query();
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
        $db = MolajoFactory::getDBO();
        if ($db->connected()) {
        } else {
            return false;
        }

        // Remove a session from the database.
        $db->setQuery(
            'DELETE FROM ' . $db->quoteName('#__sessions') .
            ' WHERE ' . $db->quoteName('session_id') . ' = ' . $db->quote($id)
        );

        return (boolean)$db->query();
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
    function gc($lifetime = 1440)
    {
        $db = MolajoFactory::getDBO();
        if ($db->connected()) {
        } else {
            return false;
        }

        // Determine the timestamp threshold with which to purge old sessions.
        $past = time() - $lifetime;

        // Remove expired sessions from the database.
        $db->setQuery(
            'DELETE FROM ' . $db->quoteName('#__sessions') .
            ' WHERE ' . $db->quoteName('session_time') . ' < ' . (int)$past
        );

        return (boolean)$db->query();
    }
}
