<?php
/**
 * @version        $Id: helper.php 20196 2011-01-09 02:40:25Z ian $
 * @package        Joomla.Site
 * @subpackage    login
 * @copyright    Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('MOLAJO') or die;

class modLoginHelper
{
    static function getReturnURL($parameters, $type)
    {

        $router = MolajoController::getApplication()->getRouter();
        $url = null;

        return base64_encode($url);
    }

    static function getType()
    {
        $user = MolajoController::getUser();
        return (!$user->get('guest')) ? 'logout' : 'login';
    }
}
