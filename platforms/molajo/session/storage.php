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
 * Custom session storage handler for PHP
 *
 * @package     Joomla.Platform
 * @subpackage  Session
 * @since       11.1
 * @see         http://www.php.net/manual/en/function.session-set-save-handler.php
 */
abstract class MolajoSessionStorage extends JObject
{
    /**
     * Constructor
     *
     * @param   array  $options    Optional parameters.
     */
    public function __construct($options = array())
    {
        $this->register($options);
    }

    /**
     * Returns a session storage handler object, only creating it
     * if it doesn't already exist.
     *
     * @param   name        $name The session store to instantiate
     *
     * @return  database    A MolajoSessionStorage object
     * @since   1.0
     */
    public static function getInstance($name = 'none', $options = array())
    {
        static $instances;

        if (!isset ($instances)) {
            $instances = array();
        }

        $name = strtolower(JFilterInput::getInstance()->clean($name, 'word'));

        if (empty ($instances[$name])) {
            $class = 'MolajoSessionStorage' . ucfirst($name);

            if (class_exists($class)) {
            } else {
                $path = dirname(__FILE__) . DS . 'storage' . DS . $name . '.php';

                if (file_exists($path)) {
                    require_once $path;
                } else {
                    // No call to JError::raiseError here, as it tries to close the non-existing session
                    jexit('Unable to load session storage class: ' . $name);
                }
            }

            $instances[$name] = new $class($options);
        }

        return $instances[$name];
    }

    /**
     * Register the functions of this class with PHP's session handler
     *
     * @param   array  $options optional parameters
     */
    public function register($options = array())
    {
        // use this object as the session handler
        session_set_save_handler(
            array($this, 'open'),
            array($this, 'close'),
            array($this, 'read'),
            array($this, 'write'),
            array($this, 'destroy'),
            array($this, 'gc')
        );
    }

    /**
     * Open the SessionHandler backend.
     *
     * @param   string  $save_path        The path to the session object.
     * @param   string  $session_name    The name of the session.
     *
     * @return boolean  True on success, false otherwise.
     */
    public function open($save_path, $session_name)
    {
        return true;
    }

    /**
     * Close the SessionHandler backend.
     *
     * @return boolean  True on success, false otherwise.
     */
    public function close()
    {
        return true;
    }

    /**
     * Read the data for a particular session identifier from the
     * SessionHandler backend.
     *
     * @param   string  $id  The session identifier.
     * @return  string  The session data.
     */
    public function read($id)
    {
        return;
    }

    /**
     * Write session data to the SessionHandler backend.
     *
     * @param   string  $id                The session identifier.
     * @param   string  $session_data    The session data.
     *
     * @return  boolean  True on success, false otherwise.
     */
    public function write($id, $session_data)
    {
        return true;
    }

    /**
     * Destroy the data for a particular session identifier in the
     * SessionHandler backend.
     *
     * @param   string  $id  The session identifier.
     *
     * @return  boolean  True on success, false otherwise.
     */
    public function destroy($id)
    {
        return true;
    }

    /**
     * Garbage collect stale sessions from the SessionHandler backend.
     *
     * @param   integer  $maxlifetime    The maximum age of a session.
     * @return  boolean  True on success, false otherwise.
     */
    public function gc($maxlifetime = null)
    {
        return true;
    }

    /**
     * Test to see if the SessionHandler is available.
     *
     * @return boolean  True on success, false otherwise.
     */
    public static function test()
    {
        return true;
    }
}