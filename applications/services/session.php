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
     * Session
     *
     * @var    object Session
     * @since  1.0
     */
    protected $session = null;

    /**
     * Hash
     *
     * @var    array
     * @since  1.0
     */
    protected $hash;

    /**
     * Token
     *
     * @var    array
     * @since  1.0
     */
    protected $token;

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
     * create
     *
     * Initiated by Application startup to either a) identify an existing session
     * for a user (in which case the last_visit_datetime for the user is updated)
     * or b) create a new session (in which case, a session id is generated and
     * a new session record created).
     *
     * In addition, this method does general housekeeping on old sessions
     *
     * @param   string  $name
     *
     * @return  session object
     * @since  1.0
     */
    public function create($name)
    {
        debug('MolajoSessionService create');

        $handler =
                Services::Configuration()
                    ->get('session_handler', 'none');

        $options = array();

        $options['expire'] = Services::Configuration()->get('lifetime', 15) * 60;
        $options['force_ssl'] = Services::Configuration()->get('force_ssl', 0);
        debug('Going into MolajoSession::getInstance');
        $this->session = MolajoSession::getInstance($handler, $options);
echo 'out of get instance'.'<br />';
var_dump($this->session);
die;
        if ($this->session->getState() == 'expired') {
            $this->session->restart();
        }



       		$time = time();
       		if ($time % 2)
       		{
       			$query = $db->getQuery(true);
       			$query->delete($query->qn('#__session'))
       				->where($query->qn('time') . ' < '
                       . $query->q((int) ($time - $this->session->getExpire())));

       			$db->setQuery($query);
       			$db->query();
       		}

       		// Check to see the the session already exists.
       		if (($this->getCfg('session_handler') != 'database'
                   && ($time % 2 || $this->session->isNew()))
       			|| ($this->getCfg('session_handler') == 'database'
                       && $this->session->isNew()))
       		{
       			$this->checkSession();
       		}

       		return $this->session;
       	}


    /**
     * _removeExpiredSessions
     *
     * @return void
     */
    protected function _removeExpiredSessions()
    {
        $m = new MolajoSessionsModel();


        $db->setQuery(
            'DELETE FROM `#__sessions`' .
            ' WHERE `session_time` < ' . (int)(time() - $this->session->getExpire())
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
        $db = Services::DB();
        $this->session = Services::Session();
        $user = Services::User();

        $db->setQuery(
            'SELECT `session_id`' .
            ' FROM `#__sessions`' .
            ' WHERE `session_id` = ' .
                $db->q($this->session->getId()), 0, 1
        );
        $exists = $db->loadResult();
        if ($exists) {
            return;
        }

        if ($this->session->isNew()) {
            $db->setQuery(
                'INSERT INTO `#__sessions` '.
                    '(`session_id`, `application_id`, `session_time`)' .
                ' VALUES (' . $db->q($this->session->getId()) .
                    ', ' . (int)MOLAJO_APPLICATION_ID .
                    ', ' . (int)time() . ')'
            );

        } else {
            $db->setQuery(
                'INSERT INTO `#__sessions`
                (`session_id`, `application_id`, `session_time`, `user_id`)' .
                ' VALUES (' .
                $db->q($this->session->getId()) . ', ' .
                (int)MOLAJO_APPLICATION_ID . ', ' .
                (int)$this->session->get('session.timer.start') . ', ' .
                (int)$user->get('id') . ')'
            );
        }

        // If the insert failed, exit the application.
        if ($db->query()) {
        } else {
            jexit($db->getErrorMSG());
        }

        // Session doesn't exist yet, so create session variables
        if ($this->session->isNew()) {
            $this->session->set('registry', new Registry('session'));
            $this->session->set('user', new MolajoUser());
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
        return $this->session;
    }

    /**
     * getHash
     *
     * Provides a secure hash based on a seed
     *
     * @param   string   $seed
     *
     * @return  string
     * @since  1.0
     */
    public function getHash($seed)
    {
        return md5(Services::Configuration()->get('secret') . $seed);
    }

    /**
     * Get a session token, if a token isn't set yet one will be generated.
     *
     * Tokens are used to secure forms from spamming attacks. Once a token
     * has been generated the system will check the post request to see if
     * it is present, if not it will invalidate the session.
     *
     * @param   boolean  $forceNew  If true, force a new token to be created
     *
     * @return  string  The session token
     *
     * @since   11.1
     */
    public function getToken($forceNew = false)
    {
        $token = $this->get('session.token');

        // Create a token
        if ($token === null || $forceNew) {
            $token = $this->_createToken(12);
            $this->set('session.token', $token);
        }

        return $token;
    }

    /**
     * createToken
     *
     * Create a token-string
     *
     * @param   integer  length of string
     *
     * @return  string  generated token
     * @since  1.0
     */
    protected function createToken($length = 32)
    {
        static $chars = '0123456789abcdef';
        $max = strlen($chars) - 1;
        $token = '';
        $name = session_name();
        for ($i = 0; $i < $length; ++$i) {
            $token .= $chars[(rand(0, $max))];
        }
        return md5($token . $name);
    }

    /**
     * Method to determine if a token exists in the session. If not the
     * session will be set to expired
     *
     * @param   string   $tCheck       Hashed token to be verified
     * @param   boolean  $forceExpire  If true, expires the session
     *
     * @return  boolean
     *
     * @since   11.1
     */
    public function hasToken($tCheck, $forceExpire = true)
    {
        // Check if a token exists in the session
        $tStored = $this->get('session.token');

        // Check token
        if (($tStored !== $tCheck)) {
            if ($forceExpire) {
                $this->_state = 'expired';
            }
            return false;
        }

        return true;
    }

    /**
     * Method to determine a hash for anti-spoofing variable names
     *
     * @param   boolean  $forceNew  If true, force a new token to be created
     *
     * @return  string  Hashed var name
     *
     * @since   11.1
     */
    public function getFormToken($forceNew = false)
    {
        $session = JFactory::getSession();
        $hash = JApplication::getHash(
            Services::User()->get('id', 0)
                . $session->getToken($forceNew));

        return $hash;
    }

    /**
     * Checks for a form token in the request.
     *
     * Use in conjunction with JHtml::_('form.token') or MolajoSession::getFormToken.
     *
     * @param   string  $method  The request method in which to look for the token key.
     *
     * @return  boolean  True if found and valid, false otherwise.
     *
     * @since       12.1
     */
    public function checkToken($method = 'post')
    {
        $token = self::getFormToken();
        $app = JFactory::getApplication();

        if (!$app->input->$method->get($token, '', 'alnum')) {
            $session = JFactory::getSession();
            if ($session->isNew()) {
                // Redirect to login screen.
                $app->redirect(JRoute::_('index.php'), Services::Languages()->translate('JLIB_ENVIRONMENT_SESSION_EXPIRED'));
                $app->close();
            }
            else
            {
                return false;
            }
        }
        else
        {
            return true;
        }
    }
}
