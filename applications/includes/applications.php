<?php
/**
 * @package     Molajo
 * @subpackage  Load
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 *  File Helper
 */
$fileHelper = new MolajoFileService();

/**
 *  Base
 */
$files = JFolder::files(MOLAJO_APPLICATIONS_CORE. '/base', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'base.php' || $file == 'language.php') {
    } else {
        $fileHelper->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/base/' . $file, 'Molajo' . ucfirst(substr($file, 0, strpos($file, '.'))));
    }
}

/**
 *  Helpers
 */
$files = JFolder::files(MOLAJO_APPLICATIONS_CORE . '/helpers', '\.php$', false, false);
foreach ($files as $file) {
    $fileHelper->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/helpers/' . $file, 'Molajo' . ucfirst(substr($file, 0, strpos($file, '.'))) . 'Helper');
}

/**
 *  Renderers
 */
$files = JFolder::files(MOLAJO_APPLICATIONS_CORE . '/base/renderers', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'renderer.php') {
    } else {
        $fileHelper->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/base/renderers/' . $file, 'Molajo' . ucfirst(substr($file, 0, strpos($file, '.'))) . 'Renderer');
    }
}

/**
 *  Services
 */
$fileHelper->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/services/services.php', 'MolajoServices');

$files = JFolder::files(MOLAJO_APPLICATIONS_CORE . '/services', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'services.php' || $file == 'text.php') {
    } else {
        $fileHelper->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/services/' . $file, 'Molajo' . ucfirst(substr($file, 0, strpos($file, '.'))) . 'Service');
    }
}

/**
 *  Installer
 */
$fileHelper->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/installer/adapter.php', 'MolajoAdapter');
$fileHelper->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/installer/adapterinstance.php', 'MolajoAdapterInstance');
$fileHelper->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/installer/installer.php', 'MolajoInstaller');
$fileHelper->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/installer/extension.php', 'MolajoInstallerExtension');
$fileHelper->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/installer/librarymanifest.php', 'MolajoInstallerLibraryManifest');
$fileHelper->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/installer/packagemanifest.php', 'MolajoInstallerPackageManifest');

$files = JFolder::files(MOLAJO_APPLICATIONS_CORE . '/installer/adapters', '\.php$', false, false);
foreach ($files as $file) {
    $fileHelper->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/installer/adapters/' . $file, 'MolajoInstallerAdapter' . ucfirst(substr($file, 0, strpos($file, '.'))));
}

/**
 *  MVC
 */
/** Controller */
$fileHelper->requireClassFile(MOLAJO_APPLICATIONS_MVC . '/controllers/controller.php', 'MolajoController');
$fileHelper->requireClassFile(MOLAJO_APPLICATIONS_MVC . '/controllers/display.php', 'MolajoDisplayController');
//$fileHelper->requireClassFile(MOLAJO_APPLICATIONS_MVC . '/controllers/edit.php', 'MolajoEditController');
$fileHelper->requireClassFile(MOLAJO_APPLICATIONS_MVC . '/controllers/login.php', 'MolajoLoginController');
$fileHelper->requireClassFile(MOLAJO_APPLICATIONS_MVC . '/controllers/redirect.php', 'MolajoRedirectController');
$fileHelper->requireClassFile(MOLAJO_APPLICATIONS_MVC . '/controllers/update.php', 'MolajoUpdateController');
$fileHelper->requireClassFile(MOLAJO_APPLICATIONS_MVC . '/controllers/updatelist.php', 'MolajoUpdatelistController');

/** Models */
$fileHelper->requireClassFile(MOLAJO_APPLICATIONS_MVC . '/models/model.php', 'MolajoModel');
$fileHelper->requireClassFile(MOLAJO_APPLICATIONS_MVC . '/models/nested.php', 'MolajoNestedModel');
$files = JFolder::files(MOLAJO_APPLICATIONS_MVC . '/models', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'model.php' || $file == 'nested.php') {
    } else {
        $fileHelper->requireClassFile(MOLAJO_APPLICATIONS_MVC . '/models/' . $file, 'Molajo' . ucfirst(substr($file, 0, strpos($file, '.'))).'Model');
    }
}
