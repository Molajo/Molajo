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
    // MolajoRenderer is not statically linked
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
        return Molajo::Services()->connect('Access');
    }
    public static function Authentication ()
    {
        return Molajo::Services()->connect('Authentication');
    }
    public static function Date ()
    {
        return Molajo::Services()->connect('Date');
    }
    public static function Dispatcher ()
    {
        return Molajo::Services()->connect('Dispatcher');
    }
    public static function Image ()
    {
        return Molajo::Services()->connect('Image');
    }
    public static function Jdb ()
    {
        return Molajo::Services()->connect('jdb');
    }
    public static function Language ()
    {
        return Molajo::Services()->connect('Language');
    }
    public static function Mail ()
    {
        return Molajo::Services()->connect('Mail');
    }
    public static function Message ()
    {
        return Molajo::Services()->connect('Message');
    }
    public static function Security ()
    {
        return Molajo::Services()->connect('Security');
    }
    public static function Session ()
    {
        return Molajo::Services()->connect('Session');
    }
    public static function Text ()
    {
        return Molajo::Services()->connect('Text');
    }
    public static function Url ()
    {
        return Molajo::Services()->connect('Url');
    }
    public static function User ()
    {
        return Molajo::Services()->connect('User');
    }
}

/**
 *  Molajo Class for alias creation, ex Molajo::User
 */
class Registry extends JRegistry {}
class Input extends JInput {}
class FilterInput extends JFilterInput {}
class FilterOutput extends JFilterOutput {}
