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
$load = new MolajoLoadHelper();

/**
 *  Base
 */
$files = JFolder::files(MOLAJO_APPLICATIONS_CORE. '/base', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'base.php') {
    } else {
        $load->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/base/' . $file, 'Molajo' . ucfirst(substr($file, 0, strpos($file, '.'))));
    }
}

/**
 *  Helpers
 */
$files = JFolder::files(MOLAJO_APPLICATIONS_CORE . '/helpers', '\.php$', false, false);
foreach ($files as $file) {
    $load->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/helpers/' . $file, 'Molajo' . ucfirst(substr($file, 0, strpos($file, '.'))) . 'Helper');
}

/**
 *  Renderers
 */
$files = JFolder::files(MOLAJO_APPLICATIONS_CORE . '/base/renderers', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'renderer.php') {
    } else {
        $load->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/base/renderers/' . $file, 'Molajo' . ucfirst(substr($file, 0, strpos($file, '.'))) . 'Renderer');
    }
}

/**
 *  Services
 */
$files = JFolder::files(MOLAJO_APPLICATIONS_CORE . '/services', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'file.php' || $file == 'text.php') {
    } else {
        $load->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/services/' . $file, 'Molajo' . ucfirst(substr($file, 0, strpos($file, '.'))) . 'Service');
    }
}

/**
 *  Installer

$load->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/installer/adapter.php', 'MolajoAdapter');
$load->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/installer/adapterinstance.php', 'MolajoAdapterInstance');
$load->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/installer/installer.php', 'MolajoInstaller');
$load->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/installer/extension.php', 'MolajoInstallerExtension');
$load->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/installer/librarymanifest.php', 'MolajoInstallerLibraryManifest');
$load->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/installer/packagemanifest.php', 'MolajoInstallerPackageManifest');

$files = JFolder::files(MOLAJO_APPLICATIONS_CORE . '/installer/adapters', '\.php$', false, false);
foreach ($files as $file) {
    $load->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/installer/adapters/' . $file, 'MolajoAdapter' . ucfirst(substr($file, 0, strpos($file, '.'))));
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
$load->requireClassFile(MOLAJO_APPLICATIONS_MVC . '/controllers/redirect.php', 'MolajoRedirectController');
$load->requireClassFile(MOLAJO_APPLICATIONS_MVC . '/controllers/update.php', 'MolajoUpdateController');
$load->requireClassFile(MOLAJO_APPLICATIONS_MVC . '/controllers/updatelist.php', 'MolajoUpdatelistController');

/** Models */
$load->requireClassFile(MOLAJO_APPLICATIONS_MVC . '/models/model.php', 'MolajoModel');
$load->requireClassFile(MOLAJO_APPLICATIONS_MVC . '/models/display.php', 'MolajoDisplayModel');
$load->requireClassFile(MOLAJO_APPLICATIONS_MVC . '/models/nested.php', 'MolajoNestedModel');
$files = JFolder::files(MOLAJO_APPLICATIONS_MVC . '/models', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'model.php' || $file == 'display.php'|| $file == 'nested.php') {
    } else {
        $load->requireClassFile(MOLAJO_APPLICATIONS_MVC . '/models/' . $file, 'Molajo' . ucfirst(substr($file, 0, strpos($file, '.'))).'Model');
    }
}

/** Model Helpers */
$load->requireClassFile(MOLAJO_APPLICATIONS_MVC . '/models/helpers/helper.php', 'MolajoModelHelper');
$files = JFolder::files(MOLAJO_APPLICATIONS_MVC . '/models/helpers', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'helper.php') {
    } else {
        $load->requireClassFile(MOLAJO_APPLICATIONS_MVC . '/models/helpers/' . $file, 'Molajo' . ucfirst(substr($file, 0, strpos($file, '.'))) . 'ModelHelper');
    }
}
