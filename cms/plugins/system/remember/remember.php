<?php
/**
 * @version        $Id: remember.php 21097 2011-04-07 15:38:03Z dextercowley $
 * @copyright    Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('MOLAJO') or die;


/**
 * Molajo System Remember Me Plugin
 *
 * @package        Joomla.Plugin
 * @subpackage    System.remember
 */
class plgSystemRemember extends MolajoPlugin
{
    function onAfterInitialise()
    {
        $app = MolajoFactory::getApplication();

        $user = MolajoFactory::getUser();
        if ($user->get('guest')) {
            jimport('joomla.utilities.utility');
            $hash = MolajoUtility::getHash('JLOGIN_REMEMBER');

            if ($str = JRequest::getString($hash, '', 'cookie', JREQUEST_ALLOWRAW | JREQUEST_NOTRIM)) {
                jimport('joomla.utilities.simplecrypt');

                //Create the encryption key, apply extra hardening using the user agent string
                $key = MolajoUtility::getHash(@$_SERVER['HTTP_USER_AGENT']);

                $crypt = new MolajoSimpleCrypt($key);
                $str = $crypt->decrypt($str);

                $options = array();
                $options['silent'] = true;
                if (!$app->login(@unserialize($str), $options)) {
                    $config = MolajoFactory::getConfig();
                    $cookie_domain = $config->get('cookie_domain', '');
                    $cookie_path = $config->get('cookie_path', '/');
                    // Clear the remember me cookie
                    setcookie(MolajoUtility::getHash('JLOGIN_REMEMBER'), false, time() - 86400, $cookie_path, $cookie_domain);
                }
            }
        }
    }
}
