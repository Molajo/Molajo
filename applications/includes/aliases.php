<?php
/**
 * @package     Molajo
 * @subpackage  Load
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

Class ApplicationHelper extends MolajoApplicationHelper {}
Class AssetHelper extends MolajoAssetHelper {}
Class ComponentHelper extends MolajoComponentHelper {}
Class ConfigurationHelper extends MolajoConfigurationHelper {}
Class ContentHelper extends MolajoContentHelper {}
Class DateHelper extends MolajoDateHelper {}
Class ExtensionHelper extends MolajoExtensionHelper {}
Class FileHelper extends MolajoFileHelper {}
Class FilesystemHelper extends MolajoFileSystemHelper {}
Class FilterHelper extends MolajoFilterHelper {}
Class ImageHelper extends MolajoImageHelper {}
Class InstallerHelper extends MolajoInstallerHelper {}
Class LanguageHelper extends MolajoLanguageHelper {}
Class MailHelper extends MolajoMailHelper {}
Class MenuHelper extends MolajoMenuHelper {}
Class ModuleHelper extends MolajoModuleHelper {}
Class SessionHelper extends MolajoSessionHelper {}
Class SiteHelper extends MolajoSiteHelper {}
Class TextHelper extends MolajoTextHelper {}
Class ThemeHelper extends MolajoThemeHelper {}
Class TransliterateHelper extends MolajoTransliterateHelper {}
Class UrlHelper extends MolajoUrlHelper {}
Class UserHelper extends MolajoUserHelper {}
Class ViewHelper extends MolajoViewHelper {}


/**
 *  Molajo Class for alias creation, ex Molajo::User
 */
class Molajo extends MolajoBase
{
    public static function Site($id = null, $config = null)
    {
        return MolajoBase::getSite($id, $config);
    }

    public static function Application($id = null, $config = null, $input = null)
    {
        return MolajoBase::getApplication($id, $config, $input);
    }

    public static function Request($request = null, $override_request_url = null, $override_asset_id = null)
    {
        return MolajoBase::getRequest($request, $override_request_url, $override_asset_id);
    }

    public static function Parser($config = null)
    {
        return MolajoBase::getParser($config);
    }

    public static function Responder($config = null)
    {
        return MolajoBase::getResponder($config);
    }

    public static function User($id = null)
    {
        return MolajoBase::getUser($id);
    }

    public static function DB()
    {
        return MolajoBase::getDbo();
    }

    public static function Date($time = 'now', $tzOffset = null)
    {
        return MolajoBase::getDate($time, $tzOffset);
    }
}
abstract class JFactory extends MolajoBase
{
}
class Registry extends JRegistry {}
class Input extends JInput {}
class FilterInput extends JFilterInput {}
class FilterOutput extends JFilterOutput {}
