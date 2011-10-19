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

    /**
     * Initialization
     */
        $filehelper = new MolajoFileHelper();

		$credentials = array(
			'username' => JRequest::getVar('username', '', 'method', 'username'),
			'password' => JRequest::getVar('password', '', 'post', 'string', JREQUEST_ALLOWRAW)
		);

        $options = array('action' => 'login');

		$authenticate = MolajoAuthentication::getInstance();
		$response	= $authenticate->authenticate($credentials, $options);
echo '<pre>';var_dump($response);'</pre>';
        die;
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
     * Authenticate 
     */
		$response = new MolajoAuthentication();
		$plugins = MolajoPluginHelper::getPlugin('authentication');

		foreach ($plugins as $plugin) {

            $path = MOLAJO_PATH_PLUGINS.'/'.$plugin->type.'/'.$plugin->name.'/'.$plugin->name.'.php';
            $className = 'plg'.ucfirst($plugin->type).ucfirst($plugin->name);
            $filehelper->requireClassFile($path, $className);

			if (class_exists($className)) {
				$authenticate = new $className($response, (array) $plugin);

			} else {
                echo 'NOT exists'.$className;
                die;
				JError::raiseWarning(50, MolajoText::sprintf('MOLAJO_USER_ERROR_AUTHENTICATION_FAILED_LOAD_PLUGIN', $className));
				continue;
			}
echo 'going in';
			$plugin->onUserAuthenticate($credentials, $options, $response);
echo 'dfasdfasfsaf';
//echo '<pre>';var_dump($response);'</pre>';
die;

			if ($response->status == MOLAJO_AUTHENTICATE_STATUS_SUCCESS) {
				if (empty($response->type)) {
					$response->type = isset($plugin->_name) ? $plugin->_name : $plugin->name;
				}
				break;
			}
		}

		if (empty($response->username)) { $response->username = $credentials['username']; }
		if (empty($response->fullname)) { $response->fullname = $credentials['username']; }
		if (empty($response->password)) { $response->password = $credentials['password']; }

        /**
         *  If Login succeeded so far, fire onUserLogin Plugins
         */
        if ($response->status === MOLAJO_AUTHENTICATE_STATUS_SUCCESS) {

            MolajoPluginHelper::importPlugin('user');
            $results = $this->dispatcher->trigger('onUserLogin', array((array)$response, $options));

            if (in_array(false, $results, true)) {
                $response->status = MOLAJO_AUTHENTICATE_STATUS_FAILURE;

            } else {

                if (isset($options['remember']) && $options['remember']) {

                    $key = MolajoUtility::getHash(@$_SERVER['HTTP_USER_AGENT']);

                    $crypt = new JSimpleCrypt($key);
                    $rcookie = $crypt->encrypt(serialize($credentials));
                    $lifetime = time() + 365*24*60*60;

                    $cookie_domain = $this->getCfg('cookie_domain', '');
                    $cookie_path = $this->getCfg('cookie_path', '/');
                    setcookie( MolajoUtility::getHash('JLOGIN_REMEMBER'), $rcookie, $lifetime, $cookie_path, $cookie_domain );
                }
            }

            /** success message **/
            $this->redirectClass->setRedirectMessage(MolajoText::_('MOLAJO_LOGIN_SUCCESSFUL'));
            $this->redirectClass->setSuccessIndicator(true);


        } else {

        /**
         *  Login Failed
         */
            $results = $this->dispatcher->trigger('onUserLoginFailure', array((array)$response));

            // If silent is set, just return false.
            if (isset($options['silent']) && $options['silent']) {
                $this->redirectClass->setRedirectMessage('');
            } else {
                $this->redirectClass->setRedirectMessage(MolajoText::_('MOLAJO_LOGIN_FAILED'));
            }
            $this->redirectClass->setSuccessIndicator(false);
            return false;
        }
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
     * @param   array    $options  Array('applicationid' => array of client id's)
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
        $user = MolajoFactory::getUser($userid);

        // Build the credentials array.
        $parameters['username']	= $user->get('username');
        $parameters['id']		= $user->get('id');

        // Set clientid in the options array if it hasn't been set already.
        if (!isset($options['applicationid'])) {
            $options['applicationid']= $this->getApplicationId();
        }

        // Import the user plugin group.
        MolajoPluginHelper::importPlugin('user');

        // OK, the credentials are built. Lets fire the onLogout event.
        $results = $this->triggerEvent('onUserLogout', array($parameters, $options));

        // Check if any of the plugins failed. If none did, success.

        if (in_array(false, $results, true)) {
        } else {
            // Use domain and path set in config for cookie if it exists.
            $cookie_domain = $this->getCfg('cookie_domain', '');
            $cookie_path = $this->getCfg('cookie_path', '/');
            setcookie(MolajoUtility::getHash('JLOGIN_REMEMBER'), false, time() - 86400, $cookie_path, $cookie_domain);

            return true;
        }

        // Trigger onUserLoginFailure Event.
        $this->triggerEvent('onUserLogoutFailure', array($parameters));

        return false;
    }
}