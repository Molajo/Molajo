<?php
/**
 * @version        $Id: logout.php 21097 2011-04-07 15:38:03Z dextercowley $
 * @copyright    Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.error.error');
jimport('joomla.utilities.utility');

/**
 * Plugin class for logout redirect handling.
 *
 * @package        Joomla.Plugin
 * @subpackage    System.logout
 */
class plgSystemLogout extends MolajoPluginHelper
{
    /**
     * Object Constructor.
     *
     * @access    public
     * @param    object    The object to observe -- event dispatcher.
     * @param    object    The configuration object for the plugin.
     * @return    void
     * @since    1.5
     */
    function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);

        $hash = MolajoUtility::getHash('plgSystemLogout');
        $conf = MolajoController::getApplication()->get();
        $cookie_domain = $conf->get('config.cookie_domain', '');
        $cookie_path = $conf->get('config.cookie_path', '/');
        setcookie($hash, false, time() - 86400, $cookie_path, $cookie_domain);

        // Set the error handler for E_ALL to be the class handleError method.
        JError::setErrorHandling(E_ALL, 'callback', array('plgSystemLogout', 'handleError'));
    }

    /**
     * This method should handle any logout logic and report back to the subject
     *
     * @param    array    $user        Holds the user data.
     * @param    array    $options    Array holding options (client, ...).
     *
     * @return    object    True on success
     * @since    1.5
     */
    public function onUserLogout($user, $options = array())
    {
        // Create the cookie
        $hash = MolajoUtility::getHash('plgSystemLogout');
        $conf = MolajoController::getApplication()->get();
        $cookie_domain = $conf->get('config.cookie_domain', '');
        $cookie_path = $conf->get('config.cookie_path', '/');
        setcookie($hash, true, time() + 86400, $cookie_path, $cookie_domain);
        return true;
    }

    static function handleError(&$error)
    {
        // Get the application object.


        // Make sure the error is a 403 and we are in the frontend.
        if ($error->getCode() == 403) {
            // Redirect to the home page
            //			$this->loadLanguage();
            MolajoController::getApplication()->redirect('index.php', MolajoTextHelper::_('PLG_SYSTEM_LOGOUT_REDIRECT'), null, true, false);
        }
        else {
            // Render the error page.
            JError::customErrorPage($error);
        }
    }
}
