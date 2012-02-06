<?php
/**
 * @package     Molajo
 * @subpackage  Controller
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Login Controller
 *
 * Handles Login and Logout Methods
 *
 * @package      Molajo
 * @subpackage   Controller
 * @since        1.0
 */
class MolajoLoginController
{
    /**
     * login
     *
     * Method to log in a user.
     *
     * @return    void
     */
    public function login()
    {
        /**
         *  Retrieve Form Fields
         */
        //JRequest::checkToken() or die();

        $credentials = array(
            'username' => JRequest::getVar('username', '', 'method', 'username'),
            'password' => JRequest::getVar('password', '', 'post', 'string', JREQUEST_ALLOWRAW)
        );

        $options = array('action' => 'login');

        /** security check: internal URL only */
        if ($return = JRequest::getVar('return', '', 'method', 'base64')) {
            $return = base64_decode($return);
            if (JURI::isInternal($return)) {
            } else {
                $return = '';
            }
        }
        if (empty($return)) {
            $return = 'index.php';
        }

        /**
         *  Authenticate, Authorize and Execute After Login Plugins
         */
        $molajoAuth = MolajoAuthentication::getInstance();

        $userObject = $molajoAuth->authenticate($credentials, $options);
        if ($userObject->status === MolajoAuthentication::STATUS_SUCCESS) {
        } else {
            $this->_loginFailed('authenticate', $userObject, $options);
            return;
        }

        $molajoAuth->authorise($userObject, (array)$options);
        if ($userObject->status === MolajoAuthentication::STATUS_SUCCESS) {
        } else {
            $this->_loginFailed('authorise', $userObject, $options);
            return;
        }

        $molajoAuth->onUserLogin($userObject, (array)$options);
        if (isset($options['remember']) && $options['remember']) {

            // Create the encryption key, apply extra hardening using the user agent string.
            $agent = @$_SERVER['HTTP_USER_AGENT'];

            // Ignore empty and crackish user agents
            if ($agent != '' && $agent != 'JLOGIN_REMEMBER') {
                $key = MolajoUtility::getHash($agent);
                $crypt = new MolajoSimpleCrypt($key);
                $rcookie = $crypt->encrypt(serialize($credentials));
                $lifetime = time() + 365 * 24 * 60 * 60;

                // Use domain and path set in config for cookie if it exists.
                $cookie_domain = $this->getConfig('cookie_domain', '');
                $cookie_path = $this->getConfig('cookie_path', '/');
                setcookie(
                    MolajoUtility::getHash('JLOGIN_REMEMBER'), $rcookie, $lifetime,
                    $cookie_path, $cookie_domain
                );
            }
        }

        /** success message */
        $this->redirectClass->setRedirectMessage(TextHelper::_('MOLAJO_SUCCESSFUL_LOGON'));
        $this->redirectClass->setSuccessIndicator(true);
    }

    /**
     * _loginFailed
     *
     * Handles failed login attempts
     *
     * @param $response
     * @param array $options
     * @return
     */
    protected function _loginFailed($type, $response, $options = Array())
    {
//        MolajoPluginHelper::getPlugin('user');
//        if ($type == 'authenticate') {
//            JDispatcher::getInstance()->trigger('onUserLoginFailure', array($response, $options));
//        } else {
//            JDispatcher::getInstance()->trigger('onUserAuthorisationFailure', array($response, $options));
//        }

        if (isset($options['silent']) && $options['silent']) {
        } else {
            $this->redirectClass->setRedirectMessage(TextHelper::_('JLIB_LOGIN_AUTHORIZED'));
            $this->redirectClass->setRedirectMessageType(TextHelper::_('warning'));
        }
        return $this->redirectClass->setSuccessIndicator(false);
    }

    /**
     * logout
     *
     * Method to log out a user.
     *
     * @return    void
     */
    public function logout()
    {
        JRequest::checkToken('default') or die;


        $user_id = JRequest::getInt('uid', null);
        $options = array(
            'application_id' => ($user_id) ? 0 : 1
        );

        $result = Molajo::Application()->logout($user_id, $options);
        if (!MolajoError::isError($result)) {
            $this->model = $this->getModel('login');
            $return = $this->model->getState('return');
            Molajo::Application()->redirect($return);
        }

        parent::display();
    }

    /**
     * Logout authentication function.
     *
     * Passed the current user information to the onUserLogout event and reverts the current
     * session record back to 'anonymous' parameters.
     * If any of the authentication plugins did not successfully complete
     * the logout routine then the whole method fails.  Any errors raised
     * should be done in the plugin as this provides the ability to give
     * much more information about why the routine may have failed.
     *
     * @param   integer  $user_id   The user to load - Can be an integer or string - If string, it is converted to ID automatically
     * @param   array    $options  Array('application_id' => array of client id's)
     *
     * @return  boolean  True on success
     *
     * @since   1.0
     */
    public function logout2($user_id = null, $options = array())
    {
        // Initialise variables.
        $retval = false;

        // Get a user object from the MolajoApplication.
        $user = Molajo::User($user_id);

        // Build the credentials array.
        $parameters['username'] = $user->get('username');
        $parameters['id'] = $user->get('id');

        // Set clientid in the options array if it hasn't been set already.
        if (!isset($options['application_id'])) {
            $options['application_id'] = MOLAJO_APPLICATION_ID;
        }

        // Import the user plugin group.
//        MolajoPluginHelper::importPlugin('user');

        // OK, the credentials are built. Lets fire the onLogout event.
//        $results = Molajo::Application()->triggerEvent('onUserLogout', array($parameters, $options));

        // Check if any of the plugins failed. If none did, success.

//        if (in_array(false, $results, true)) {
//        } else {
            // Use domain and path set in config for cookie if it exists.
            $cookie_domain = $this->getConfig('cookie_domain', '');
            $cookie_path = $this->getConfig('cookie_path', '/');
            setcookie(MolajoUtility::getHash('JLOGIN_REMEMBER'), false, time() - 86400, $cookie_path, $cookie_domain);

            return true;
//        }

        // Trigger onUserLoginFailure Event.
//        Molajo::Application()->triggerEvent('onUserLogoutFailure', array($parameters));

        return false;
    }
}
