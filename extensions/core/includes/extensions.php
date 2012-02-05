<?php
/**
 * @package     Molajo
 * @subpackage  Extension
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 *  File Helper
 */
$fileHelper = new FileHelper();

/**
 *  Primary Extensions Classes
 */
$fileHelper->requireClassFile(MOLAJO_EXTENSIONS_CORE . '/core/request.php', 'MolajoControllerRequest');
$fileHelper->requireClassFile(MOLAJO_EXTENSIONS_CORE . '/core/document.php', 'MolajoDocument');
$fileHelper->requireClassFile(MOLAJO_EXTENSIONS_CORE . '/core/renderers/renderer.php', 'MolajoRenderer');

/**
 *  Helpers
 */
$files = JFolder::files(MOLAJO_EXTENSIONS_CORE . '/core/helpers', '\.php$', false, false);
foreach ($files as $file) {
    $fileHelper->requireClassFile(MOLAJO_EXTENSIONS_CORE . '/core/helpers/' . $file, 'Molajo' . ucfirst(substr($file, 0, strpos($file, '.'))) . 'Helper');
}

/**
 *  Installer
 */
$files = JFolder::files(MOLAJO_EXTENSIONS_CORE . '/core/installer/adapters', '\.php$', false, false);
foreach ($files as $file) {
    $fileHelper->requireClassFile(MOLAJO_EXTENSIONS_CORE . '/core/installer/adapters/' . $file, 'MolajoInstallerAdapter' . ucfirst(substr($file, 0, strpos($file, '.'))));
}

/**
 *  Renderers
 */
$files = JFolder::files(MOLAJO_EXTENSIONS_CORE . '/core/renderers', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'renderer.php') {
    } else {
        $fileHelper->requireClassFile(MOLAJO_EXTENSIONS_CORE . '/core/renderers/' . $file, 'Molajo' . ucfirst(substr($file, 0, strpos($file, '.'))) . 'Renderer');
    }
}

/**
 *  Aliases
 */
Class AppHelper extends MolajoApplicationHelper {}
Class AssetHelper extends MolajoAssetHelper {}
Class ComponentHelper extends MolajoComponentHelper {}
Class ConfigHelper extends MolajoConfigurationHelper {}
Class ContentHelper extends MolajoContentHelper {}
Class DateHelper extends MolajoDateHelper {}
Class ExtensionHelper extends MolajoExtensionHelper {}
Class FilesystemHelper extends MolajoFileSystemHelper {}
Class FilterHelper extends MolajoFilterHelper {}
Class ImageHelper extends MolajoImageHelper {}
Class InstallHelper extends MolajoInstallHelper {}
Class LanguageHelper extends MolajoLanguageHelper {}
Class MailHelper extends MolajoMailHelper {}
Class MenuHelper extends MolajoMenuHelper {}
Class ModuleHelper extends MolajoModuleHelper {}
Class SessionHelper extends MolajoSessionHelper {}
Class SiteHelper extends MolajoSiteHelper {}
Class ThemeHelper extends MolajoThemeHelper {}
Class TransliterateHelper extends MolajoTransliterateHelper {}
Class UrlHelper extends MolajoUrlHelper {}
Class UserHelper extends MolajoUserHelper {}
Class ViewHelper extends MolajoViewHelper {}

