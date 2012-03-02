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

Class AssetHelper extends AssetHelper
{
}

Class ComponentHelper extends MolajoComponentHelper
{
}

Class ContentHelper extends ContentHelper
{
}

Class ExtensionHelper extends ExtensionHelper
{
}

Class InstallerHelper extends InstallerHelper
{
}

Class LoadHelper extends LoadHelper
{
}

Class MenuItemHelper extends MolajoMenuItemHelper
{
}

Class ModuleHelper extends MolajoModuleHelper
{
}

Class PluginHelper extends MolajoPluginHelper
{
}

Class SiteHelper extends SiteHelper
{
}

Class ThemeHelper extends MolajoThemeHelper
{
}

Class ViewHelper extends MolajoViewHelper
{
}

/**
 *  Molajo Base Class
 */
Class extends MolajoBase
{
    public static function Site()
    {
        return MolajoBase::getSite();
    }

    public static function Application()
    {
        return MolajoBase::getApplication();
    }

    public static function Request($request = null, $override_request_url = null, $override_asset_id = null)
    {
        return MolajoBase::getRequest($request, $override_request_url, $override_asset_id);
    }

    public static function Parser()
    {
        return MolajoBase::getParser();
    }

    public static function Services()
    {
        return MolajoBase::getServices();
    }
}

abstract class JFactory extends MolajoBase
{
}

abstract class JError
{
    static $legacy = false;
}

/**
 *  Molajo Services
 */
class Services extends MolajoServices
{
    public static function Access()
    {
        return Molajo::Services()->get('Access');
    }

    public static function Authentication()
    {
        return Molajo::Services()->get('Authentication');
    }

    public static function Configuration()
    {
        return Molajo::Services()->get('Configuration');
    }

    public static function Cookie()
    {
        return Molajo::Services()->get('Cookie');
    }

    public static function Date()
    {
        return Molajo::Services()->get('Date');
    }

    public static function DB()
    {
        return Molajo::Services()->get('jdb');
    }

    public static function Dispatcher()
    {
        return Molajo::Services()->get('Dispatcher');
    }

    public static function Document()
    {
        return Molajo::Services()->get('Document');
    }

    public static function File()
    {
        return Molajo::Services()->get('File');
    }

    public static function Folder()
    {
        return Molajo::Services()->get('Folder');
    }

    public static function Image()
    {
        return Molajo::Services()->get('Image');
    }

    public static function Language()
    {
        return Molajo::Services()->get('Language');
    }

    public static function Mail()
    {
        return Molajo::Services()->get('Mail');
    }

    public static function Message()
    {
        return Molajo::Services()->get('Message');
    }

    public static function Parameter()
    {
        return Molajo::Services()->get('Parameter');
    }

    public static function Request()
    {
        return Molajo::Services()->get('Request');
    }

    public static function Response()
    {
        return Molajo::Services()->get('Response');
    }

    public static function Security()
    {
        return Molajo::Services()->get('Security');
    }

    public static function Session()
    {
        return Molajo::Services()->get('Session');
    }

    public static function Text()
    {
        return Molajo::Services()->get('Text');
    }

    public static function Url()
    {
        return Molajo::Services()->get('URL');
    }

    public static function User()
    {
        return Molajo::Services()->get('User');
    }
}

/**
 *  Molajo Class for alias creation, ex Molajo::User
 */
class Registry extends JRegistry
{
}
