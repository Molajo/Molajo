<?php
/**
 * @package     Molajo
 * @subpackage  Login Controller
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Login Controller
 *
 * Handles Login and Logout Methods
 *
 * @package	    Molajo
 * @subpackage	Controller
 * @since	    1.0
 */
class MolajoControllerLogin extends MolajoController
{

	/**
	 * login
     * 
     * Method to log in a user.
	 *
	 * @return	void
	 */
	public function login()
	{
        /** security token **/
//        JRequest::checkToken() or die;

        $options = array();
        $options = array('action' => 'login');

		$credentials = array(
			'username' => JRequest::getVar('username', '', 'method', 'username'),
			'password' => JRequest::getVar('passwd', '', 'post', 'string', JREQUEST_ALLOWRAW)
		);

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

		$authenticate = JAuthentication::getInstance();
		$response	= $authenticate->authenticate($credentials, $options);
 
		if ($response->status === JAUTHENTICATE_STATUS_SUCCESS) {
			// Import the user plugin group.
			MolajoPluginHelper::importPlugin('user');

			// OK, the credentials are authenticated.  Lets fire the onLogin event.
			$results = $this->dispatcher->trigger('onUserLogin', array((array)$response, $options));

			/*
			 * If any of the user plugins did not successfully complete the login routine
			 * then the whole method fails.
			 *
			 * Any errors raised should be done in the plugin as this provides the ability
			 * to provide much more information about why the routine may have failed.
			 */

			if (!in_array(false, $results, true)) {
				// Set the remember me cookie if enabled.
				if (isset($options['remember']) && $options['remember']) {
					// Create the encryption key, apply extra hardening using the user agent string.
					$key = JUtility::getHash(@$_SERVER['HTTP_USER_AGENT']);

					$crypt = new JSimpleCrypt($key);
					$rcookie = $crypt->encrypt(serialize($credentials));
					$lifetime = time() + 365*24*60*60;

					// Use domain and path set in config for cookie if it exists.
					$cookie_domain = $this->getCfg('cookie_domain', '');
					$cookie_path = $this->getCfg('cookie_path', '/');
					setcookie( JUtility::getHash('JLOGIN_REMEMBER'), $rcookie, $lifetime, $cookie_path, $cookie_domain );
				}

				return true;
			}
		}

		// Trigger onUserLoginFailure Event.
        $results = $this->dispatcher->trigger('onUserLoginFailure', array((array)$response));

		// If silent is set, just return false.
		if (isset($options['silent']) && $options['silent']) {
			return false;
		}

		// If status is success, any error will ahve been raised by the user plugin
		if ($response->status !== JAUTHENTICATE_STATUS_SUCCESS) {
			JError::raiseWarning('SOME_ERROR_CODE', MolajoText::_('JLIB_LOGIN_AUTHENTICATE'));
		}

		return false;

        /** success message **/
        $this->redirectClass->setRedirectMessage(MolajoText::_('MOLAJO_LOGIN_SUCCESSFUL'));
        $this->redirectClass->setSuccessIndicator(true);

		if (!JError::isError($result)) {
			$app->redirect($return);
		}

        /** what to display? */
		parent::display();

///
//The minimum group
		$options['group'] = 'Public Backend';

		//Make sure users are not autoregistered
		$options['autoregister'] = false;

		//Set the application login entry point
		if (!array_key_exists('entry_url', $options)) {
			$options['entry_url'] = JURI::base().'index.php?option=com_users&task=login';
		}

		// Set the access control action to check.
		$options['action'] = 'core.login.admin';

		$result = parent::login($credentials, $options);

		if (!JError::isError($result))
		{
			$lang = JRequest::getCmd('lang');
			$lang = preg_replace('/[^A-Z-]/i', '', $lang);
			$this->setUserState('application.lang', $lang );

			JAdministrator::purgeMessages();
		}

		return $result;
	}

	/**
	 * Finds out if a set of login credentials are valid by asking all obvserving
	 * objects to run their respective authentication routines.
	 *
	 * @param   array  Array holding the user credentials
	 * @return  mixed  Integer userid for valid user if credentials are valid or
	 *					boolean false if they are not
	 * @since   11.1
	 */
	public function authenticate($credentials, $options)
	{
		// Initialise variables.
		$auth = false;

		// Get plugins
		$plugins = MolajoPluginHelper::getPlugin('authentication');

		// Create authencication response
		$response = new MolajoAuthenticationResponse();

		/*
		 * Loop through the plugins and check of the creditials can be used to authenticate
		 * the user
		 *
		 * Any errors raised in the plugin should be returned via the JAuthenticationResponse
		 * and handled appropriately.
		 */
		foreach ($plugins as $plugin)
		{
			$className = 'plg'.$plugin->type.$plugin->name;
            echo $className;
            die;
			if (class_exists($className)) {
				$plugin = new $className($this, (array)$plugin);
			}
			else {
				// Bail here if the plugin can't be created
				JError::raiseWarning(50, MolajoText::sprintf('MOLAJO_USER_ERROR_AUTHENTICATION_FAILED_LOAD_PLUGIN', $className));
				continue;
			}

			// Try to authenticate
			$plugin->onUserAuthenticate($credentials, $options, $response);

			// If authentication is successful break out of the loop
			if ($response->status === JAUTHENTICATE_STATUS_SUCCESS)
			{
				if (empty($response->type)) {
					$response->type = isset($plugin->_name) ? $plugin->_name : $plugin->name;
				}
				break;
			}
		}

		if (empty($response->username)) {
			$response->username = $credentials['username'];
		}

		if (empty($response->fullname)) {
			$response->fullname = $credentials['username'];
		}

		if (empty($response->password)) {
			$response->password = $credentials['password'];
		}

		return $response;
	}


    /**
     * logout
     *
     * Method to log out a user.
     *
     * @return	void
     */
    public function logout()
    {
        JRequest::checkToken('default') or die;

        $app = MolajoFactory::getApplication();

        $userid = JRequest::getInt('uid', null);

        $options = array(
            'applicationid' => ($userid) ? 0 : 1
        );

        $result = $app->logout($userid, $options);

        if (!JError::isError($result)) {
            $this->model 	= $this->getModel('login');
            $return = $this->model->getState('return');
            $app->redirect($return);
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
     * @param   integer  $userid   The user to load - Can be an integer or string - If string, it is converted to ID automatically
     * @param   array    $options  Array('clientid' => array of client id's)
     *
     * @return  boolean  True on success
     *
     * @since   11.1
     */
    public function logout2($userid = null, $options = array())
    {
        // Initialise variables.
        $retval = false;

        // Get a user object from the JApplication.
        $user = JFactory::getUser($userid);

        // Build the credentials array.
        $parameters['username']	= $user->get('username');
        $parameters['id']		= $user->get('id');

        // Set clientid in the options array if it hasn't been set already.
        if (!isset($options['clientid'])) {
            $options['clientid']= $this->getClientId();
        }

        // Import the user plugin group.
        MolajoPluginHelper::importPlugin('user');

        // OK, the credentials are built. Lets fire the onLogout event.
        $results = $this->triggerEvent('onUserLogout', array($parameters, $options));

        // Check if any of the plugins failed. If none did, success.

        if (!in_array(false, $results, true)) {
            // Use domain and path set in config for cookie if it exists.
            $cookie_domain = $this->getCfg('cookie_domain', '');
            $cookie_path = $this->getCfg('cookie_path', '/');
            setcookie(JUtility::getHash('JLOGIN_REMEMBER'), false, time() - 86400, $cookie_path, $cookie_domain);

            return true;
        }

        // Trigger onUserLoginFailure Event.
        $this->triggerEvent('onUserLogoutFailure', array($parameters));

        return false;
    }
}