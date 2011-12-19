<?php
/**
 * @package     Molajo
 * @subpackage  Component
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * File session handler for PHP
 *
 * @package     Joomla.Platform
 * @subpackage  Session
 * @since       11.1
 * @see http://www.php.net/manual/en/function.session-set-save-handler.php
 */
class MolajoSessionStorageNone extends MolajoSessionStorage
{
    /**
     * Register the functions of this class with PHP's session handler
     *
     * @param   array    $options optional parameters
     */
    public function register($options = array())
    {
        //let php handle the session storage
    }

    /**
     * Open the SessionHandler backend.
     *
     * @param    string    The path to the session object.
     * @param    string    The name of the session.
     * @return    boolean    True on success, false otherwise.
     * @since    1.0
     */
    public function open($save_path, $session_name)
    {
        return true;
    }

    /**
     * Close the SessionHandler backend.
     *
     * @return    boolean    True on success, false otherwise.
     * @since    1.0
     */
    public function close()
    {
        return true;
    }

    /**
     * Read the data for a particular session identifier from the
     * SessionHandler backend.
     *
     * @param    string    The session identifier.
     * @return    string    The session data.
     * @since    1.0
     */
    public function read($id)
    {
        return true;
    }

    /**
     * Write session data to the SessionHandler backend.
     *
     * @param    string    The session identifier.
     * @param    string    The session data.
     *
     * @return    boolean    True on success, false otherwise.
     * @since    1.0
     */
    public function write($id, $data)
    {
        return true;
    }

    /**
     * Destroy the data for a particular session identifier in the
     * SessionHandler backend.
     *
     * @param    string    The session identifier.
     *
     * @return    boolean    True on success, false otherwise.
     * @since    1.0
     */
    public function destroy($id)
    {
        return true;
    }

    /**
     * Garbage collect stale sessions from the SessionHandler backend.
     *
     * @param    integer    The maximum age of a session.
     * @return    boolean    True on success, false otherwise.
     * @since    1.0
     */
    function gc($lifetime = 1440)
    {
        return true;
    }

    /**
     * Test to see if the SessionHandler is available.
     *
     * @return    boolean    True on if available, false otherwise.
     * @since    1.0
     */
    public static function test()
    {
        return true;
    }
}
