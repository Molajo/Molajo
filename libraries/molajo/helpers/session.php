<?php
/**
 * @package     Molajo
 * @subpackage  Session
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Class to manage the session
 *
 * The user's session for the application.
 *
 * @package     Molajo
 * @subpackage  Application
 * @since       1.0
 */
class MolajoSessionHelper extends JObject
{
	/**
     * $_session
     *
	 * @var    object Session
	 * @since  1.0
	 */
	protected $_session = null;

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
     * @return  MolajoSession  MolajoSession on success. May call exit() on database error.
     *
     * @since  1.0
     */
    public function createSession($name)
    {
        $options = array();
        $options['name'] = $name;

        if ($this->_getConfiguration('force_ssl') == 2) {
            $options['force_ssl'] = true;
        }

        /** retrieve session */
        $this->_session = MolajoFactory::getSession($options);

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
        $db = MolajoFactory::getDBO();
        $db->setQuery(
            'DELETE FROM `#__session`' .
            ' WHERE `time` < '.(int) (time() - $this->_session->getExpire())
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
        $db 		= MolajoFactory::getDBO();
        $session 	= MolajoFactory::getSession();
        $user		= MolajoFactory::getUser();

        $db->setQuery(
            'SELECT `session_id`' .
            ' FROM `#__session`' .
            ' WHERE `session_id` = '.$db->quote($session->getId()), 0, 1
        );
        $exists = $db->loadResult();
        if ($exists) {
            return;
        }

        if ($session->isNew()) {
            $db->setQuery(
                'INSERT INTO `#__session` (`session_id`, `application_id`, `time`)' .
                ' VALUES ('.$db->quote($session->getId()).', '.(int) MOLAJO_APPLICATION_ID.', '.(int) time().')'
            );

        } else {
            $db->setQuery(
                'INSERT INTO `#__session` (`session_id`, `application_id`, `guest`, `time`, `userid`, `username`)' .
                ' VALUES ('.
                $db->quote($session->getId()).', '.
                (int) MOLAJO_APPLICATION_ID.', '.
                (int) $user->get('guest').', '.
                (int) $session->get('session.timer.start').', '.
                (int) $user->get('id').', '.
                $db->quote($user->get('username')).')'
            );
        }

        // If the insert failed, exit the application.
        if ($db->query()) {
        } else {
            jexit($db->getErrorMSG());
        }

        // Session doesn't exist yet, so create session variables
        if ($session->isNew()) {
            $session->set('registry',	new JRegistry('session'));
            $session->set('user',		new MolajoUser());
        }
    }

    /**
     * _getConfiguration
     *
     * Gets a configuration value.
     *
     * @param   string   The name of the value to get.
     * @param   string   Default value to return
     *
     * @return  mixed    The user state.
     *
     * @since  1.0
     */
    protected function _getConfiguration($varname, $default=null)
    {
        return MolajoFactory::getConfig()->get(''.$varname, $default);
    }
}
