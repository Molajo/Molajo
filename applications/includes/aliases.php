<?php
/**
 * @package     Molajo
 * @subpackage  Load
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

function hook() {
    echo 'is this all a hook is? interesting.';
}

Class AssetHelper extends MolajoAssetHelper {}
Class ComponentHelper extends MolajoComponentHelper {}
Class ContentHelper extends MolajoContentHelper {}
Class ExtensionHelper extends MolajoExtensionHelper {}
Class InstallerHelper extends MolajoInstallerHelper {}
Class LanguageHelper extends MolajoLanguageHelper {}
Class LoadHelper extends MolajoLoadHelper {}
Class MenuHelper extends MolajoMenuHelper {}
Class ModuleHelper extends MolajoModuleHelper {}
Class PluginHelper extends MolajoPluginHelper {}
Class SiteHelper extends MolajoSiteHelper {}
Class ThemeHelper extends MolajoThemeHelper {}
Class UserHelper extends MolajoUserHelper {}
Class ViewHelper extends MolajoViewHelper {}

/**
 *  Molajo Base Class
 */
class Molajo extends MolajoBase
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
    public static function Responder()
    {
        return MolajoBase::getResponder();
    }
    public static function Services()
    {
        return MolajoBase::getServices();
    }
}
abstract class JFactory extends MolajoBase
{
}

/**
 *  Molajo Services
 */
class Services extends MolajoServices
{
    public static function Access ()
    {
        return Molajo::Services()->get('Access');
    }
    public static function Authentication ()
    {
        return Molajo::Services()->get('Authentication');
    }
    public static function Configuration ()
       {
           return Molajo::Services()->get('Configuration');
       }
    public static function Date ()
    {
        return Molajo::Services()->get('Date');
    }
    public static function Dispatcher ()
    {
        return Molajo::Services()->get('Dispatcher');
    }
    public static function Image ()
    {
        return Molajo::Services()->get('Image');
    }
    public static function DB ()
    {
        return Molajo::Services()->get('jdb');
    }
    public static function Language ()
    {
        return Molajo::Services()->get('Language');
    }
    public static function Mail ()
    {
        return Molajo::Services()->get('Mail');
    }
    public static function Message ()
    {
        return Molajo::Services()->get('Message');
    }
    public static function Security ()
    {
        return Molajo::Services()->get('Security');
    }
    public static function Session ()
    {
        return Molajo::Services()->get('Session');
    }
    public static function Text ()
    {
        return Molajo::Services()->get('Text');
    }
    public static function Url ()
    {
        return Molajo::Services()->get('Url');
    }
    public static function User ()
    {
        return Molajo::Services()->get('User');
    }
}

/**
 *  Molajo Class for alias creation, ex Molajo::User
 */
class Registry extends JRegistry {}
class Input extends JInput {}
class FilterInput extends JFilterInput {}
class FilterOutput extends JFilterOutput {}
