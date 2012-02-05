<?php
/**
 * @package     Molajo
 * @subpackage  Load Molajo Framework
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 *  File Helper
 */
$fileHelper = new FileHelper();

/**
 *  Access
 */
$fileHelper->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/access/access.php', 'MolajoAccess');
$fileHelper->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/access/user.php', 'MolajoUser');
$fileHelper->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/access/group.php', 'MolajoGroup');

/**
 *  MVC
 */
/** Controller */
$fileHelper->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/helpers/application.php', 'MolajoApplicationHelper');
$fileHelper->requireClassFile(MOLAJO_APPLICATIONS_MVC . '/controllers/extension.php', 'MolajoControllerExtension');
$fileHelper->requireClassFile(MOLAJO_APPLICATIONS_MVC . '/controllers/update.php', 'MolajoControllerUpdate');
$files = JFolder::files(MOLAJO_APPLICATIONS_MVC . '/controllers', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'controller.php' || $file == 'extension.php' || $file == 'update.php') {
    } else {
        $fileHelper->requireClassFile(MOLAJO_APPLICATIONS_MVC . '/controllers/' . $file, 'MolajoController' . ucfirst(substr($file, 0, strpos($file, '.'))));
    }
}

/** Models */
$fileHelper->requireClassFile(MOLAJO_APPLICATIONS_MVC . '/models/model.php', 'MolajoModel');
$fileHelper->requireClassFile(MOLAJO_APPLICATIONS_MVC . '/models/nested.php', 'MolajoModelNested');
$files = JFolder::files(MOLAJO_APPLICATIONS_MVC . '/models', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'model.php' || $file == 'nested.php') {
    } else {
        $fileHelper->requireClassFile(MOLAJO_APPLICATIONS_MVC . '/models/' . $file, 'MolajoModel' . ucfirst(substr($file, 0, strpos($file, '.'))));
    }
}

/**
 *  Helpers
 */
$files = JFolder::files(MOLAJO_APPLICATIONS_CORE . '/helpers', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'configuration.php') {
    } else {
        $fileHelper->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/helpers/' . $file, 'Molajo' . ucfirst(substr($file, 0, strpos($file, '.'))) . 'Helper');
    }
}

/**
 *  Installer
 */
$fileHelper->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/installer/adapter.php', 'MolajoAdapter');
$fileHelper->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/installer/adapterinstance.php', 'MolajoAdapterInstance');
$fileHelper->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/installer/installer/installer.php', 'MolajoInstaller');
$files = JFolder::files(MOLAJO_APPLICATIONS_CORE . '/installer/installer', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'installer.php') {
    } else {
        $fileHelper->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/installer/installer/' . $file, 'MolajoInstaller' . ucfirst(substr($file, 0, strpos($file, '.'))));
    }
}
$files = JFolder::files(MOLAJO_APPLICATIONS_CORE . '/installer/installer/adapters', '\.php$', false, false);
foreach ($files as $file) {
    $fileHelper->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/installer/installer/adapters/' . $file, 'MolajoInstallerAdapter' . ucfirst(substr($file, 0, strpos($file, '.'))));
}
/** updater  */
$files = JFolder::files(MOLAJO_APPLICATIONS_CORE . '/installer/updater', '\.php$', false, false);
foreach ($files as $file) {
    $fileHelper->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/installer/updater/' . $file, 'Molajo' . ucfirst(substr($file, 0, strpos($file, '.'))));
}
$files = JFolder::files(MOLAJO_APPLICATIONS_CORE . '/installer/updater/adapters', '\.php$', false, false);
foreach ($files as $file) {
    $fileHelper->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/installer/updater/adapters/' . $file, 'MolajoUpdater' . ucfirst(substr($file, 0, strpos($file, '.'))));
}
