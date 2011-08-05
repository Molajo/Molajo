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
 * Handles the standard single-item save, delete, and cancel tasks
 *
 * Cancel: cancel and close
 * Save: apply, create, save, save2copy, save2new, restore
 * Delete: delete
 *
 * Called from the Multiple Controller for batch (copy, move) and delete
 *
 * @package	Molajo
 * @subpackage	Controller
 * @since	1.0
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
        JRequest::checkToken() or die;

        /** initialisation */
        parent::initialise('login');

        /** @var $app */
		$app = MolajoFactory::getApplication();

		$this->model = $this->getModel('login');
		$credentials = $this->model->getState('credentials');
		$return = $this->model->getState('return');

		$result = $app->login($credentials, array('action' => 'core.login.admin'));
        /** success message **/
        $this->redirectClass->setRedirectMessage(JText::_('MOLAJO_LOGIN_SUCCESSFUL'));
        $this->redirectClass->setSuccessIndicator(true);

		if (!JError::isError($result)) {
			$app->redirect($return);
		}

		parent::display();
	}

    /**
     * Login authentication function.
     *
     * Username and encoded password are passed the the onUserLogin event which
     * is responsible for the user validation. A successful validation updates
     * the current session record with the user's details.
     *
     * Username and encoded password are sent as credentials (along with other
     * possibilities) to each observer (authentication plugin) for user
     * validation.  Successful validation will update the current session with
     * the user details.
     *
     * @param   array  $credentials  Array('username' => string, 'password' => string)
     * @param   array  $options      Array('remember' => boolean)
     *
     * @return  boolean  True on success.
     *
     * @since  1.0
     */
    public function adminlogin($credentials, $options = array())
    {
        $authenticate = JAuthentication::getInstance();
        $response	= $authenticate->authenticate($credentials, $options);

        if ($response->status === JAUTHENTICATE_STATUS_SUCCESS) {
            // Import the user plugin group.
            MolajoPluginHelper::importPlugin('user');

            // OK, the credentials are authenticated.  Lets fire the onLogin event.
            $results = $this->triggerEvent('onUserLogin', array((array)$response, $options));

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
        $this->triggerEvent('onUserLoginFailure', array((array)$response));

        // If silent is set, just return false.
        if (isset($options['silent']) && $options['silent']) {
            return false;
        }

        // If status is success, any error will ahve been raised by the user plugin
        if ($response->status !== JAUTHENTICATE_STATUS_SUCCESS) {
            JError::raiseWarning('SOME_ERROR_CODE', JText::_('MOLAJO_LOGIN_AUTHENTICATE'));
        }

        return false;
    }

	/**
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
     * @param   array    $options  Array('applicationid' => array of application id's)
     *
     * @return  boolean  True on success
     *
     * @since  1.0
     */
    public function adminlogout($userid = null, $options = array())
    {
        // Initialise variables.
        $retval = false;

        // Get a user object from the MolajoApplication.
        $user = MolajoFactory::getUser($userid);

        // Build the credentials array.
        $parameters['username']	= $user->get('username');
        $parameters['id']		= $user->get('id');

        // Set applicationid in the options array if it hasn't been set already.
        if (!isset($options['applicationid'])) {
            $options['applicationid']= $this->getApplicationId();
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