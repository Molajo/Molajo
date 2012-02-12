<?php
/**
 * @package     Molajo
 * @subpackage  Service
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Security
 *
 * @package     Molajo
 * @subpackage  Service
 * @since       1.0
 */
class MolajoSecurityService
{
    /**
     * Instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

    /**
     * Session
     *
     * @var    object
     * @since  1.0
     */
    protected $session;

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
            self::$instance = new MolajoSecurityService ();
        }
        return self::$instance;
    }

    /**
     * Class constructor.
     *
     * @return  null
     * @since   1.0
     */
    public function __construct()
    {
        $this->session = Services::Session();
    }

    /**
     * getToken
     *
     * Tokens are used to secure forms from spamming attacks.
     *
     * @param   boolean  If true, force a new token to be created
     * @return  string   Session token
     */
    public function getToken($forceNew = false)
    {
        $token = $this->session->get('session.token');

        if ($token === null
            || $forceNew
        ) {
            $token = $this->session->_createToken(12);
            $this->session->set('session.token', $token);
        }

        return $token;
    }

    /**
     * hasToken
     *
     * Method to determine if a token exists in the session. If not the
     * session will be set to expired
     *
     * @param   string   Hashed token to be verified
     * @param   boolean  If true, expires the session
     *
     * @return  boolean
     * @since   1.0
     */
    public function hasToken($tCheck, $forceExpire = true)
    {
        $tStored = $this->session->get('session.token');

        if (($tStored !== $tCheck)) {
            if ($forceExpire) {
                $this->session->_state = 'expired';
            }
            return false;
        }

        return true;
    }

    /**
     * getFormToken
     *
     * Method to determine a hash for anti-spoofing variable names
     *
     * @return  string  Hashed variable name
     * @since   1.0
     */
    public function getFormToken($forceNew = false)
    {
        return $this->getHash(Services::User()->get('id', 0));
        //                . $this->getToken($forceNew)
    }

    /**
     * getHash
     *
     * Provides a secure hash based on a seed
     *
     * @param   string   $seed  Seed string.
     *
     * @return  string   A secure hash
     * @since  1.0
     */
    public function getHash($seed)
    {
        return md5(Services::Configuration()->get('secret') . $seed);
    }

    /**
     * _createToken
     *
     * Create a token-string
     *
     * @param   integer  length of string
     *
     * @return  string  generated token
     * @since  1.0
     */
    protected function _createToken($length = 32)
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
}
