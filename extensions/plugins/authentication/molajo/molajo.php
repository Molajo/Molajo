<?php
/**
 * @package     Molajo
 * @subpackage  Authentication
 * @copyright   Copyright (C) 2012 Molajo. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Authentication
 *
 * @package        Molajo
 * @subpackage    Authentication
 * @since       1.0
 */
class plgAuthenticationMolajo extends MolajoPlugin
{
    /**
     * onUserAuthenticate
     *
     * Authenticates the credentials of the user
     *
     * @access    public
     * @param    array    Array holding the user credentials
     * @param    array    Array of extra options
     * @param    object    Authentication response object
     * @return    boolean
     * @since   1.0
     */
    function onUserAuthenticate($credentials, $options, $response)
    {
        /** disallow empty password */
        if (empty($credentials['password'])) {
            $response->status = JAUTHENTICATE_STATUS_FAILURE;
            $response->error_message = MolajoTextHelper::_('JGLOBAL_AUTH_EMPTY_PASS_NOT_ALLOWED');
            return false;
        }

        /** retrieve user from database */
        $conditions = '';
        $db = MolajoFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('id, password');
        $query->from('#__users');
        $query->where('username=' . $db->Quote($credentials['username']));

        $db->setQuery($query);
        $result = $db->loadObject();

        /** does the user exist? */
        if ($result) {
        } else {
            $response->status = JAUTHENTICATE_STATUS_FAILURE;
            $response->error_message = MolajoTextHelper::_('JGLOBAL_AUTH_NO_USER');
            return false;
        }

        /** is the password correct? */
        $parts = explode(':', $result->password);
        $crypt = $parts[0];
        $salt = @$parts[1];

        $testcrypt = MolajoUserHelper::getCryptedPassword($credentials['password'], $salt);

        if ($crypt == $testcrypt) {
        } else {
            $response->status = JAUTHENTICATE_STATUS_FAILURE;
            $response->error_message = MolajoTextHelper::_('JGLOBAL_AUTH_INVALID_PASS');
        }

        /** retrieve user information */
        $user = MolajoUser::getInstance($result->id);
        $response->email = $user->email;
        $response->fullname = $user->name;
        $response->language = $user->getParam('language');

        /** success */
        $response->status = JAUTHENTICATE_STATUS_SUCCESS;
        $response->error_message = '';
    }

    /**
     * onUserAuthorisation
     *
     * Determines whether or not a User can logon
     *
     * @param $response
     * @param $options
     * @return void
     */
    function onUserAuthorisation($response, $options)
    {
        /** check the ACL to see if this user can logon to this application */

    }
}
