<?php
/**
 * @package     Molajo
 * @subpackage  Bootstrap
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

function hook()
{
    echo 'is this all a hook is? interesting.';
}


/**
 *  Molajo Base Class
 */
Class Molajo extends Base
{
    public static function Site()
    {
        return Base::getSite();
    }

    public static function Application()
    {
        return Base::getApplication();
    }

    public static function Request($request = null, $override_request_url = null, $override_asset_id = null)
    {
        return Base::getRequest($request, $override_request_url, $override_asset_id);
    }

    public static function Parse()
    {
        return Base::getParse();
    }

    public static function Service()
    {
        return Base::getService();
    }
}

abstract class JFactory extends Base
{
}

abstract class JError
{
    static $legacy = false;
}

/**
 *  Molajo Services
 */
class Services extends Service
{
    public static function Access()
    {
        return Molajo::Service()->get('Access');
    }

    public static function Authentication()
    {
        return Molajo::Service()->get('Authentication');
    }

    public static function Configuration()
    {
        return Molajo::Service()->get('Configuration');
    }

    public static function Cookie()
    {
        return Molajo::Service()->get('Cookie');
    }

    public static function Date()
    {
        return Molajo::Service()->get('Date');
    }

    public static function DB()
    {
        return Molajo::Service()->get('jdb');
    }

    public static function Dispatcher()
    {
        return Molajo::Service()->get('Dispatcher');
    }

    public static function Document()
    {
        return Molajo::Service()->get('Document');
    }

    public static function File()
    {
        return Molajo::Service()->get('File');
    }

    public static function Folder()
    {
        return Molajo::Service()->get('Folder');
    }

    public static function Image()
    {
        return Molajo::Service()->get('Image');
    }

    public static function Language()
    {
        return Molajo::Service()->get('Language');
    }

    public static function Mail()
    {
        return Molajo::Service()->get('Mail');
    }

    public static function Message()
    {
        return Molajo::Service()->get('Message');
    }

    public static function Parameter()
    {
        return Molajo::Service()->get('Parameter');
    }

    public static function Request()
    {
        return Molajo::Service()->get('Request');
    }

    public static function Response()
    {
        return Molajo::Service()->get('Response');
    }

    public static function Security()
    {
        return Molajo::Service()->get('Security');
    }

    public static function Session()
    {
        return Molajo::Service()->get('Session');
    }

    public static function Text()
    {
        return Molajo::Service()->get('Text');
    }

    public static function Url()
    {
        return Molajo::Service()->get('URL');
    }

    public static function User()
    {
        return Molajo::Service()->get('User');
    }
}

/**
 *  Molajo Class for alias creation, ex Molajo::User
 */
class Registry extends JRegistry
{
}
