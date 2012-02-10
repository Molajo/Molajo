<?php
/**
 * @package     Molajo
 * @subpackage  Service
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Session
 *
 * @package     Molajo
 * @subpackage  Service
 * @since       1.0
 */
class MolajoSessionService
{
    /**
     * Static instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

    /**
     * $_session
     *
     * @var    object Session
     * @since  1.0
     */
    protected $_session = null;

    /**
     * getInstance
     *
     * @static
     * @return bool|object
     * @since  1.0
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new MolajoSessionService();
        }
        return self::$instance;
    }

    /**
     * __construct
     *
     * Class constructor.
     *
     * @return boolean
     * @since  1.0
     */
    public function __construct()
    {
    }

    /**
     * createSession
     *
     * Create the user session.
     *
     * Old sessions are flushed based on the configuration value for the cookie
     * lifetime. If an existing session, then the last access time is updated.
     * If a new session, a session id is generated and a record is created in
     * the #__sessions table.
     *
     * @param   string  $name  The sessions name.
     *
     * @return  MolajoSession  May call exit() on database error.
     * @since  1.0
     */
    public function get($name=null)
    {
        $options = array();
        $options['name'] = $name;

        if (Molajo::Application()->get('force_ssl') == 2) {
            $options['force_ssl'] = true;
        }

        /** retrieve session */
        $this->_session = Molajo::Application()->getSession($options);

        /** unlock */

        /** The modulus introduces a little entropy so that queries only fires less than half the time. */
        $time = time() % 2;
        if ($time) {
        } else {
            return $this->_session;
        }

        $this->_removeExpiredSessions();

        $this->_checkSession();

        return $this->_session;
    }

    /**
     * _removeExpiredSessions
     *
     * @return void
     */
    protected function _removeExpiredSessions()
    {
        $db = Molajo::Services()->connect('jdb');

        $db->setQuery(
            'DELETE FROM `#__sessions`' .
            ' WHERE `session_time` < ' . (int)(time() - $this->_session->getExpire())
        );
        $db->query();
    }

    /**
     * _checkSession
     *
     * Checks the user session.
     *
     * If the session record doesn't exist, initialise it.
     * If session is new, create session variables
     *
     * @return  void
     *
     * @since  1.0
     */
    protected function _checkSession()
    {
        $db = Molajo::Services()->connect('jdb');
        $session = Molajo::Services()->connect('Session');
        $user = Molajo::Services()->connect('User');

        $db->setQuery(
            'SELECT `session_id`' .
            ' FROM `#__sessions`' .
            ' WHERE `session_id` = ' .
                $db->quote($session->getId()), 0, 1
        );
        $exists = $db->loadResult();
        if ($exists) {
            return;
        }

        if ($session->isNew()) {
            $db->setQuery(
                'INSERT INTO `#__sessions` '.
                    '(`session_id`, `application_id`, `session_time`)' .
                ' VALUES (' . $db->quote($session->getId()) .
                    ', ' . (int)MOLAJO_APPLICATION_ID .
                    ', ' . (int)time() . ')'
            );

        } else {
            $db->setQuery(
                'INSERT INTO `#__sessions`
                (`session_id`, `application_id`, `session_time`, `user_id`)' .
                ' VALUES (' .
                $db->quote($session->getId()) . ', ' .
                (int)MOLAJO_APPLICATION_ID . ', ' .
                (int)$session->get('session.timer.start') . ', ' .
                (int)$user->get('id') . ')'
            );
        }

        // If the insert failed, exit the application.
        if ($db->query()) {
        } else {
            jexit($db->getErrorMSG());
        }

        // Session doesn't exist yet, so create session variables
        if ($session->isNew()) {
            $session->set('registry', new Registry('session'));
            $session->set('user', new MolajoUser());
        }
    }

    /**
     * getSession
     *
     * Method to get the application _session object.
     *
     * @return  Session  The _session object
     *
     * @since   1.0
     */
    public function getSession()
    {
        return $this->_session;
    }
}
