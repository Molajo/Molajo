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
$filehelper = new MolajoFileHelper();

/**
 *  Access
 */
$filehelper->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/access/authentication.php', 'MolajoAuthentication');
$filehelper->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/access/molajo.php', 'MolajoACL');
$filehelper->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/access/core.php', 'MolajoACLCore');
$filehelper->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/access/user.php', 'MolajoUser');
$filehelper->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/access/userhelper.php', 'MolajoUserhelper');
$filehelper->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/access/group.php', 'MolajoGroup');

/**
 *  Application
 */
$filehelper->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/application/application.php', 'MolajoApplication');
$files = JFolder::files(MOLAJO_APPLICATIONS_CORE . '/application', '\.php$', false, false);

echo 'yes';
        die;


foreach ($files as $file) {
    if ($file == 'application') {
    } else {
        $filehelper->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/application/' . $file, 'MolajoApplication' . ucfirst(substr($file, 0, strpos($file, '.'))));
    }
}
echo 'yes';
        die;
/**
 *  Data
 */

/** Data: Entity */
$files = JFolder::files(MOLAJO_APPLICATIONS_DATA . '/Entity', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(MOLAJO_APPLICATIONS_DATA . '/Entity/' . $file, ucfirst(substr($file, 0, strpos($file, '.'))));
}

/** Data: Fields */

/** Data: Fields: Attributes */
$filehelper->requireClassFile(MOLAJO_APPLICATIONS_DATA . '/fields/attribute.php', 'MolajoAttribute');
$files = JFolder::files(MOLAJO_APPLICATIONS_DATA . '/fields/attributes', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(MOLAJO_APPLICATIONS_DATA . '/fields/attributes/' . $file, 'MolajoAttribute' . ucfirst(substr($file, 0, strpos($file, '.'))));
}

/** Data: Fields: Fields */
$filehelper->requireClassFile(MOLAJO_APPLICATIONS_DATA . '/fields/field.php', 'MolajoField');
$files = JFolder::files(MOLAJO_APPLICATIONS_DATA . '/fields/fields', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(MOLAJO_APPLICATIONS_DATA . '/fields/fields/' . $file, 'MolajoField' . ucfirst(substr($file, 0, strpos($file, '.'))));
}

/** Data: Fields: Form */
$filehelper->requireClassFile(MOLAJO_APPLICATIONS_DATA . '/fields/form/formfield.php', 'MolajoFormField');
$filehelper->requireClassFile(MOLAJO_APPLICATIONS_DATA . '/fields/form/formrule.php', 'MolajoFormRule');
$filehelper->requireClassFile(MOLAJO_APPLICATIONS_DATA . '/fields/form/helper.php', 'MolajoFormHelper');
$filehelper->requireClassFile(MOLAJO_APPLICATIONS_DATA . '/fields/form/form.php', 'MolajoForm');

/** Data: Fields: FieldTypes - must follow form */
$filehelper->requireClassFile(MOLAJO_APPLICATIONS_DATA . '/fields/fieldtypes/list.php', 'MolajoFormFieldList');
$filehelper->requireClassFile(MOLAJO_APPLICATIONS_DATA . '/fields/fieldtypes/filelist.php', 'MolajoFormFieldFileList');
$filehelper->requireClassFile(MOLAJO_APPLICATIONS_DATA . '/fields/fieldtypes/groupedlist.php', 'MolajoFormFieldGroupedList');
$filehelper->requireClassFile(MOLAJO_APPLICATIONS_DATA . '/fields/fieldtypes/text.php', 'MolajoFormFieldText');

$files = JFolder::files(MOLAJO_APPLICATIONS_DATA . '/fields/fieldtypes', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'list.php' || $file == 'filelist.php' || $file == 'groupedlist.php' || $file == 'text.php') {
    } else {
        $filehelper->requireClassFile(MOLAJO_APPLICATIONS_DATA . '/fields/fieldtypes/' . $file, 'MolajoFormField' . ucfirst(substr($file, 0, strpos($file, '.'))));
    }
}

/** Data: HTML */
$filehelper->requireClassFile(MOLAJO_APPLICATIONS_DATA . '/html/editor.php', 'MolajoEditor');
$filehelper->requireClassFile(MOLAJO_APPLICATIONS_DATA . '/html/grid.php', 'MolajoGrid');
$filehelper->requireClassFile(MOLAJO_APPLICATIONS_DATA . '/html/html.php', 'MolajoHtml');
$filehelper->requireClassFile(MOLAJO_APPLICATIONS_DATA . '/html/pagination.php', 'MolajoPagination');
$filehelper->requireClassFile(MOLAJO_APPLICATIONS_DATA . '/html/pane.php', 'MolajoPane');

$files = JFolder::files(MOLAJO_APPLICATIONS_DATA . '/html/html', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(MOLAJO_APPLICATIONS_DATA . '/html/html/' . $file, 'MolajoHtml' . ucfirst(substr($file, 0, strpos($file, '.'))));
}

/** Data: Tables */
$filehelper->requireClassFile(MOLAJO_APPLICATIONS_DATA . '/tables/table.php', 'MolajoTable');
$filehelper->requireClassFile(MOLAJO_APPLICATIONS_DATA . '/tables/tablenested.php', 'MolajoTableNested');
$files = JFolder::files(MOLAJO_APPLICATIONS_DATA . '/tables', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'table.php' || $file == 'tablenested.php') {
    } else {
        $filehelper->requireClassFile(MOLAJO_APPLICATIONS_DATA . '/tables/' . $file, 'MolajoTable' . ucfirst(substr($file, 0, strpos($file, '.'))));
    }
}

/**
 *  Document
 */
$filehelper->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/document/document.php', 'MolajoDocument');
$filehelper->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/document/renderer.php', 'MolajoDocumentRenderer');

$format = JRequest::getCmd('format', 'html');
if ($format == 'error' || $format == 'feed' || $format == 'raw') {
    $includeFormat = $format;
} else {
    $includeFormat = 'html';
}
$formatClass = 'MolajoDocument' . ucfirst($includeFormat);
if (class_exists($formatClass)) {
} else {
    $path = MOLAJO_APPLICATIONS_CORE . '/document/' . $includeFormat . '/' . $includeFormat . '.php';
    $formatClass = 'MolajoDocument' . ucfirst($includeFormat);
    $filehelper->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/document/' . $includeFormat . '/' . $includeFormat . '.php', $formatClass);
}

/**
 *  Helpers
 */
$files = JFolder::files(MOLAJO_APPLICATIONS_CORE . '/helpers', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/helpers/' . $file, 'Molajo' . ucfirst(substr($file, 0, strpos($file, '.'))) . 'Helper');
}

/**
 *  Installer
 */
$filehelper->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/installer/adapter.php', 'MolajoAdapter');
$filehelper->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/installer/adapterinstance.php', 'MolajoAdapterInstance');
$filehelper->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/installer/installer/installer.php', 'MolajoInstaller');
$files = JFolder::files(MOLAJO_APPLICATIONS_CORE . '/installer/installer', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'installer.php') {
    } else {
        $filehelper->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/installer/installer/' . $file, 'MolajoInstaller' . ucfirst(substr($file, 0, strpos($file, '.'))));
    }
}
$files = JFolder::files(MOLAJO_APPLICATIONS_CORE . '/installer/installer/adapters', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/installer/installer/adapters/' . $file, 'MolajoInstallerAdapter' . ucfirst(substr($file, 0, strpos($file, '.'))));
}
/** updater */
$files = JFolder::files(MOLAJO_APPLICATIONS_CORE . '/installer/updater', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/installer/updater/' . $file, 'Molajo' . ucfirst(substr($file, 0, strpos($file, '.'))));
}
$files = JFolder::files(MOLAJO_APPLICATIONS_CORE . '/installer/updater/adapters', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(MOLAJO_APPLICATIONS_CORE . '/installer/updater/adapters/' . $file, 'MolajoUpdater' . ucfirst(substr($file, 0, strpos($file, '.'))));
}

echo 'yes';
        die;
