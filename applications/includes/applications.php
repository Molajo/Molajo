<?php
/**
 * @package     Molajo
 * @subpackage  Bootstrap
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 *  File Helper
 */
$load = new MolajoLoadHelper();

/**
 *  Base
 */
$files = JFolder::files(MOLAJO_APPLICATIONS . '/base', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'base.php') {
    } else {
        $load->requireClassFile(MOLAJO_APPLICATIONS . '/base/' . $file, 'Molajo' . ucfirst(substr($file, 0, strpos($file, '.'))));
    }
}

/**
 *  Filters
 */
$files = JFolder::files(MOLAJO_APPLICATIONS . '/filters', '\.php$', false, false);
foreach ($files as $file) {
    $load->requireClassFile(MOLAJO_APPLICATIONS . '/filters/' . $file, 'Molajo' . ucfirst(substr($file, 0, strpos($file, '.'))) . 'Filter');
}

/**
 *  Helpers
 */
$files = JFolder::files(MOLAJO_APPLICATIONS . '/helpers', '\.php$', false, false);
foreach ($files as $file) {
    $load->requireClassFile(MOLAJO_APPLICATIONS . '/helpers/' . $file, 'Molajo' . ucfirst(substr($file, 0, strpos($file, '.'))) . 'Helper');
}

/**
 *  Renderers
 */
$files = JFolder::files(MOLAJO_APPLICATIONS . '/base/renderers', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'renderer.php') {
    } else {
        $load->requireClassFile(MOLAJO_APPLICATIONS . '/base/renderers/' . $file, 'Molajo' . ucfirst(substr($file, 0, strpos($file, '.'))) . 'Renderer');
    }
}

/**
 *  Services
 */
$files = JFolder::files(MOLAJO_APPLICATIONS . '/services', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'file.php' || $file == 'text.php') {
    } else {
        $load->requireClassFile(MOLAJO_APPLICATIONS . '/services/' . $file, 'Molajo' . ucfirst(substr($file, 0, strpos($file, '.'))) . 'Service');
    }
}

/**
 *  Installer

$load->requireClassFile(MOLAJO_APPLICATIONS . '/installer/adapter.php', 'MolajoAdapter');
$load->requireClassFile(MOLAJO_APPLICATIONS . '/installer/adapterinstance.php', 'MolajoAdapterInstance');
$load->requireClassFile(MOLAJO_APPLICATIONS . '/installer/installer.php', 'MolajoInstaller');
$load->requireClassFile(MOLAJO_APPLICATIONS . '/installer/extension.php', 'MolajoInstallerExtension');
$load->requireClassFile(MOLAJO_APPLICATIONS . '/installer/librarymanifest.php', 'MolajoInstallerLibraryManifest');
$load->requireClassFile(MOLAJO_APPLICATIONS . '/installer/packagemanifest.php', 'MolajoInstallerPackageManifest');

$files = JFolder::files(MOLAJO_APPLICATIONS . '/installer/adapters', '\.php$', false, false);
foreach ($files as $file) {
$load->requireClassFile(MOLAJO_APPLICATIONS . '/installer/adapters/' . $file, 'MolajoAdapter' . ucfirst(substr($file, 0, strpos($file, '.'))));
}
 */
/**
 *  MVC
 */
/** Controller */
$load->requireClassFile(MOLAJO_APPLICATIONS_MVC . '/controllers/controller.php', 'MolajoController');
$load->requireClassFile(MOLAJO_APPLICATIONS_MVC . '/controllers/display.php', 'MolajoDisplayController');
$load->requireClassFile(MOLAJO_APPLICATIONS_MVC . '/controllers/edit.php', 'MolajoEditController');
$load->requireClassFile(MOLAJO_APPLICATIONS_MVC . '/controllers/login.php', 'MolajoLoginController');
$load->requireClassFile(MOLAJO_APPLICATIONS_MVC . '/controllers/update.php', 'MolajoUpdateController');
$load->requireClassFile(MOLAJO_APPLICATIONS_MVC . '/controllers/updatelist.php', 'MolajoUpdatelistController');

/** Models */
$load->requireClassFile(MOLAJO_APPLICATIONS_MVC . '/models/model.php', 'MolajoModel');
$load->requireClassFile(MOLAJO_APPLICATIONS_MVC . '/models/item.php', 'MolajoItemModel');
$load->requireClassFile(MOLAJO_APPLICATIONS_MVC . '/models/load.php', 'MolajoLoadModel');
$load->requireClassFile(MOLAJO_APPLICATIONS_MVC . '/models/display.php', 'MolajoDisplayModel');
$files = JFolder::files(MOLAJO_APPLICATIONS_MVC . '/models', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'model.php' || $file == 'item.php' || $file == 'load.php' || $file == 'display.php') {
    } else {
        $load->requireClassFile(MOLAJO_APPLICATIONS_MVC . '/models/' . $file, 'Molajo' . ucfirst(substr($file, 0, strpos($file, '.'))) . 'Model');
    }
}

