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
$fileHelper = new MolajoFileHelper();

/**
 *  Access
 */
$fileHelper->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/access/authentication.php', 'MolajoAuthentication');
$fileHelper->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/access/molajo.php', 'MolajoACL');
$fileHelper->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/access/core.php', 'MolajoACLCore');
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
$files = JFolder::files(MOLAJO_APPLICATIONS_MVC . '/models', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'model.php') {
    } else {
        $fileHelper->requireClassFile(MOLAJO_APPLICATIONS_MVC . '/models/' . $file, 'MolajoModel' . ucfirst(substr($file, 0, strpos($file, '.'))));
    }
}
/**
 *  Data
 */

/** Data: Entity */
//$files = JFolder::files(MOLAJO_APPLICATIONS_CORE_DATA . '/entities', '\.php$', false, false);
//foreach ($files as $file) {
//    echo $file.'<br />';
//    $fileHelper->requireClassFile(MOLAJO_APPLICATIONS_CORE_DATA . '/entities/' . $file, ucfirst(substr($file, 0, strpos($file, '.'))));
//}

/** Data: Fields */

/** Data: Fields: Attributes */
$fileHelper->requireClassFile(MOLAJO_APPLICATIONS_CORE_DATA . '/fields/attribute.php', 'MolajoAttribute');
$files = JFolder::files(MOLAJO_APPLICATIONS_CORE_DATA . '/fields/attributes', '\.php$', false, false);
foreach ($files as $file) {
    $fileHelper->requireClassFile(MOLAJO_APPLICATIONS_CORE_DATA . '/fields/attributes/' . $file, 'MolajoAttribute' . ucfirst(substr($file, 0, strpos($file, '.'))));
}

/** Data: Fields: Fields */
$fileHelper->requireClassFile(MOLAJO_APPLICATIONS_CORE_DATA . '/fields/field.php', 'MolajoField');
$files = JFolder::files(MOLAJO_APPLICATIONS_CORE_DATA . '/fields/fields', '\.php$', false, false);
foreach ($files as $file) {
    $fileHelper->requireClassFile(MOLAJO_APPLICATIONS_CORE_DATA . '/fields/fields/' . $file, 'MolajoField' . ucfirst(substr($file, 0, strpos($file, '.'))));
}

/** Data: Fields: Form */
$fileHelper->requireClassFile(MOLAJO_APPLICATIONS_CORE_DATA . '/fields/form/formfield.php', 'MolajoFormField');
$fileHelper->requireClassFile(MOLAJO_APPLICATIONS_CORE_DATA . '/fields/form/formrule.php', 'MolajoFormRule');
$fileHelper->requireClassFile(MOLAJO_APPLICATIONS_CORE_DATA . '/fields/form/helper.php', 'MolajoFormHelper');
$fileHelper->requireClassFile(MOLAJO_APPLICATIONS_CORE_DATA . '/fields/form/form.php', 'MolajoForm');

/** Data: Fields: FieldTypes - must follow form */
$fileHelper->requireClassFile(MOLAJO_APPLICATIONS_CORE_DATA . '/fields/fieldtypes/list.php', 'MolajoFormFieldList');
$fileHelper->requireClassFile(MOLAJO_APPLICATIONS_CORE_DATA . '/fields/fieldtypes/filelist.php', 'MolajoFormFieldFileList');
$fileHelper->requireClassFile(MOLAJO_APPLICATIONS_CORE_DATA . '/fields/fieldtypes/groupedlist.php', 'MolajoFormFieldGroupedList');
$fileHelper->requireClassFile(MOLAJO_APPLICATIONS_CORE_DATA . '/fields/fieldtypes/text.php', 'MolajoFormFieldText');

$files = JFolder::files(MOLAJO_APPLICATIONS_CORE_DATA . '/fields/fieldtypes', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'list.php' || $file == 'filelist.php' || $file == 'groupedlist.php' || $file == 'text.php') {
    } else {
        $fileHelper->requireClassFile(MOLAJO_APPLICATIONS_CORE_DATA . '/fields/fieldtypes/' . $file, 'MolajoFormField' . ucfirst(substr($file, 0, strpos($file, '.'))));
    }
}

/** Data: HTML */
$fileHelper->requireClassFile(MOLAJO_APPLICATIONS_CORE_DATA . '/html/editor.php', 'MolajoEditor');
$fileHelper->requireClassFile(MOLAJO_APPLICATIONS_CORE_DATA . '/html/grid.php', 'MolajoGrid');
$fileHelper->requireClassFile(MOLAJO_APPLICATIONS_CORE_DATA . '/html/html.php', 'MolajoHtml');
$fileHelper->requireClassFile(MOLAJO_APPLICATIONS_CORE_DATA . '/html/pagination.php', 'MolajoPagination');
$fileHelper->requireClassFile(MOLAJO_APPLICATIONS_CORE_DATA . '/html/pane.php', 'MolajoPane');

$files = JFolder::files(MOLAJO_APPLICATIONS_CORE_DATA . '/html/html', '\.php$', false, false);
foreach ($files as $file) {
    $fileHelper->requireClassFile(MOLAJO_APPLICATIONS_CORE_DATA . '/html/html/' . $file, 'MolajoHtml' . ucfirst(substr($file, 0, strpos($file, '.'))));
}

/** Data: Tables */
$fileHelper->requireClassFile(MOLAJO_APPLICATIONS_CORE_DATA . '/tables/table.php', 'MolajoTable');
$fileHelper->requireClassFile(MOLAJO_APPLICATIONS_CORE_DATA . '/tables/tablenested.php', 'MolajoTableNested');
$files = JFolder::files(MOLAJO_APPLICATIONS_CORE_DATA . '/tables', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'table.php' || $file == 'tablenested.php') {
    } else {
        $fileHelper->requireClassFile(MOLAJO_APPLICATIONS_CORE_DATA . '/tables/' . $file, 'MolajoTable' . ucfirst(substr($file, 0, strpos($file, '.'))));
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
