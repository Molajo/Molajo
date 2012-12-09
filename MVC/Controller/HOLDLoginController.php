<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\MVC\Controller;

use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Signin
 *
 * @package      Molajo
 * @subpackage   Controller
 * @since        1.0
 */
class HOLDSigninController extends Controller
{
    /**
     * signin
     *
     * Method to log in a user.
     *
     * @return void
     */
    public function signin()
    {
        /**
         *  Retrieve Form Fields
         */
        //JRequest::checkToken() or die();

        $credentials = array(
            'username' => JRequest::getVar('username', '', 'method', 'username'),
            'password' => JRequest::getVar('password', '', 'post', 'string', JREQUEST_ALLOWRAW)
        );

        $options = array('action' => 'signin');

        /** security check: internal URL only */
        $return = JRequest::getVar('return', '', 'method', 'base64');
        if ($return = true) {
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
         *  Authenticate, Authorize and Execute After Signin Plugins
         */
        $userObject = Services::Authentication()->authenticate($credentials, $options);

        if ($userObject->status === Services::Authentication()->STATUS_SUCCESS) {
        } else {
            $this->_signinFailed('authenticate', $userObject, $options);

            return;
        }

        Services::Authentication()->authorise($userObject, (array) $options);
        if ($userObject->status === Services::Authentication()->STATUS_SUCCESS) {
        } else {
            $this->_signinFailed('authorise', $userObject, $options);

            return;
        }

        Services::Authentication()->onUserSignin($userObject, (array) $options);
        if (isset($options['remember']) && $options['remember']) {

            // Create the encryption key, apply extra hardening using the user agent string.
            $agent = $_SERVER['HTTP_USER_AGENT'];

            // Ignore empty and crackish user agents
            if ($agent != '' && $agent != 'JSIGNIN_REMEMBER') {
                $key = MolajoUtility::getHash($agent);
                $crypt = new MolajoSimpleCrypt($key);
                $rcookie = $crypt->encrypt(serialize($credentials));
                $lifetime = time() + 365 * 24 * 60 * 60;

                // Use domain and path set in config for cookie if it exists.
                $cookie_domain = $this->getConfig('cookie_domain', '');
                $cookie_path = $this->getConfig('cookie_path', '/');
                setcookie(
                    MolajoUtility::getHash('JSIGNIN_REMEMBER'), $rcookie, $lifetime,
                    $cookie_path, $cookie_domain
                );
            }
        }

        /** success message */
        // success redirect
    }

    /**
     * _signinFailed
     *
     * Handles failed signin attempts
     *
     * @param $response
     * @param array $options
     * @return
     */
    protected function _signinFailed($type, $response, $options = Array())
    {
//        MolajoPluginHelper::getPlugin(USER_LITERAL);
//        if ($type == 'authenticate') {
//            Services::Event()->scheduleEvent('onUserSigninFailure', array($response, $options));
//        } else {
//            Services::Event()->scheduleEvent('onUserPermissionsFailure', array($response, $options));
//        }

        //redirect false;
    }

    /**
     * signout
     *
     * Method to log out a user.
     *
     * @return void
     */
    public function signout()
    {
        JRequest::checkToken('default') or die;

        $user_id = JRequest::getInt('uid', null);
        $options = array(
            'application_id' => ($user_id) ? 0 : 1
        );

        $result = Application::signout($user_id, $options);
        if (!MolajoError::isError($result)) {
            $this->model = $this->getModel('signin');
            $return = $this->model->getState('return');
            Services::Response()->redirect($return);
        }

        parent::display();
    }

    /**
     * Signout authentication function.
     *
     * Passed the current user information to the onUserSignout event and reverts the current
     * session record back to 'anonymous' parameters.
     * If any of the authentication plugins did not successfully complete
     * the signout routine then the whole method fails.  Any errors raised
     * should be done in the plugin as this provides the ability to give
     * much more information about why the routine may have failed.
     *
     * @param integer $user_id The user to load - Can be an integer or string - If string, it is converted to ID automatically
     * @param array   $options Array('application_id' => array of client id's)
     *
     * @return boolean true on success
     *
     * @since   1.0
     */
    public function signout2($user_id = null, $options = array())
    {
        // Initialise variables.
        $retval = false;

        // Get a user object from the Application.
        $user = Molajo::User($user_id);

        // Build the credentials array.
        $parameters['username'] = $user->get('username');
        $parameters['id'] = $user->get('id');

        // Set clientid in the options array if it hasn't been set already.
        if (!isset($options['application_id'])) {
            $options['application_id'] = APPLICATION_ID;
        }

        // Import the user plugin group.
//        MolajoPluginHelper::importPlugin(USER_LITERAL);

        // OK, the credentials are built. Lets fire the onSignout event.
//        $results = Services::Event()->scheduleEvent('onUserSignout', array($parameters, $options));

        // Check if any of the plugins failed. If none did, success.

//        if (in_array(false, $results, true)) {
//        } else {
        // Use domain and path set in config for cookie if it exists.
        $cookie_domain = $this->getConfig('cookie_domain', '');
        $cookie_path = $this->getConfig('cookie_path', '/');
        setcookie(MolajoUtility::getHash('JSIGNIN_REMEMBER'), false, time() - 86400, $cookie_path, $cookie_domain);

        return true;
//        }

        // Plugin onUserSigninFailure Event.
//        Services::Event()->scheduleEvent('onUserSignoutFailure', array($parameters));
        return false;
    }
}
