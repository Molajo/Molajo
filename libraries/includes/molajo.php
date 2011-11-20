<?php
/**
 * @package     Molajo
 * @subpackage  Load Molajo Framework
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
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
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/access/authentication.php', 'MolajoAuthentication');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/access/molajo.php', 'MolajoACL');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/access/core.php', 'MolajoACLCore');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/access/user.php', 'MolajoUser');

/**
 *  Application
 */
$files = JFolder::files(MOLAJO_LIBRARY.'/application', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(MOLAJO_LIBRARY.'/application/'.$file, 'Molajo'.ucfirst(substr($file, 0, strpos($file, '.'))));
}

/**
 *  Data
 */

/** Data: Fields */

/** Data: Fields: Attributes */
$filehelper->requireClassFile(MOLAJO_LIBRARY_DATA.'/fields/attribute.php', 'MolajoAttribute');
$files = JFolder::files(MOLAJO_LIBRARY_DATA.'/fields/attributes', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(MOLAJO_LIBRARY_DATA.'/fields/attributes/'.$file, 'MolajoAttribute'.ucfirst(substr($file, 0, strpos($file, '.'))));
}

/** Data: Fields: Form */
$filehelper->requireClassFile(MOLAJO_LIBRARY_DATA.'/fields/form/formfield.php', 'MolajoFormField');
$filehelper->requireClassFile(MOLAJO_LIBRARY_DATA.'/fields/form/formrule.php', 'MolajoFormRule');
$filehelper->requireClassFile(MOLAJO_LIBRARY_DATA.'/fields/form/helper.php', 'MolajoFormHelper');
$filehelper->requireClassFile(MOLAJO_LIBRARY_DATA.'/fields/form/form.php', 'MolajoForm');

/** Data: Fields: FieldTypes */
$filehelper->requireClassFile(MOLAJO_LIBRARY_DATA.'/fields/fieldtypes/list.php', 'MolajoFormFieldList');
$filehelper->requireClassFile(MOLAJO_LIBRARY_DATA.'/fields/fieldtypes/filelist.php', 'MolajoFormFieldFileList');
$filehelper->requireClassFile(MOLAJO_LIBRARY_DATA.'/fields/fieldtypes/groupedlist.php', 'MolajoFormFieldGroupedList');
$filehelper->requireClassFile(MOLAJO_LIBRARY_DATA.'/fields/fieldtypes/text.php', 'MolajoFormFieldText');

$files = JFolder::files(MOLAJO_LIBRARY_DATA.'/fields/fieldtypes', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'list.php' || $file == 'filelist.php' || $file == 'groupedlist.php' || $file == 'text.php') {
    } else {
        $filehelper->requireClassFile(MOLAJO_LIBRARY_DATA.'/fields/fieldtypes/'.$file, 'MolajoFormField'.ucfirst(substr($file, 0, strpos($file, '.'))));
    }
}

/** Data: Fields: Fields */
$filehelper->requireClassFile(MOLAJO_LIBRARY_DATA.'/fields/field.php', 'MolajoField');
$files = JFolder::files(MOLAJO_LIBRARY_DATA.'/fields/fields', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(MOLAJO_LIBRARY_DATA.'/fields/fields/'.$file, 'MolajoField'.ucfirst(substr($file, 0, strpos($file, '.'))));
}

/** Data: HTML */
$filehelper->requireClassFile(MOLAJO_LIBRARY_DATA.'/html/editor.php', 'MolajoEditor');
$filehelper->requireClassFile(MOLAJO_LIBRARY_DATA.'/html/grid.php', 'MolajoGrid');
$filehelper->requireClassFile(MOLAJO_LIBRARY_DATA.'/html/html.php', 'MolajoHtml');
$filehelper->requireClassFile(MOLAJO_LIBRARY_DATA.'/html/pagination.php', 'MolajoPagination');
$filehelper->requireClassFile(MOLAJO_LIBRARY_DATA.'/html/pane.php', 'MolajoPane');

$files = JFolder::files(MOLAJO_LIBRARY_DATA.'/html/html', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(MOLAJO_LIBRARY_DATA.'/html/html/'.$file, 'MolajoHtml'.ucfirst(substr($file, 0, strpos($file, '.'))));
}

/** Tables */
$filehelper->requireClassFile(MOLAJO_LIBRARY_DATA.'/tables/table.php', 'MolajoTable');
$filehelper->requireClassFile(MOLAJO_LIBRARY_DATA.'/tables/tablenested.php', 'MolajoTableNested');
$files = JFolder::files(MOLAJO_LIBRARY_DATA.'/tables', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'table.php' || $file == 'tablenested.php') {
    } else {
        $filehelper->requireClassFile(MOLAJO_LIBRARY_DATA.'/tables/'.$file, 'MolajoTable'.ucfirst(substr($file, 0, strpos($file, '.'))));
    }
}

/**
 *  Document
 */
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/document/document.php', 'MolajoDocument');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/document/renderer.php', 'MolajoDocumentRenderer');

$format = JRequest::getCmd('format', 'html');
if ($format == 'error' || $format == 'feed' || $format == 'raw') {
    $includeFormat = $format;
} else {
    $includeFormat = 'html';
}
$formatClass = 'MolajoDocument'.ucfirst($includeFormat);
if (class_exists($formatClass)) {
} else {
    $path = MOLAJO_LIBRARY.'/document/'.$includeFormat.'/'.$includeFormat.'.php';
    $formatClass = 'MolajoDocument'.ucfirst($includeFormat);
    $filehelper->requireClassFile(MOLAJO_LIBRARY.'/document/'.$includeFormat.'/'.$includeFormat.'.php', $formatClass);
}

/**
 *  Helpers
 */
$files = JFolder::files(MOLAJO_LIBRARY.'/helpers', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(MOLAJO_LIBRARY.'/helpers/'.$file, 'Molajo'.ucfirst(substr($file, 0, strpos($file, '.'))).'Helper');
}

/**
 *  Installer
 */
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/installer/adapter.php', 'MolajoAdapter');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/installer/adapterinstance.php', 'MolajoAdapterInstance');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/installer/installer/helper.php', 'MolajoInstallerHelper');
$files = JFolder::files(MOLAJO_LIBRARY.'/installer/installer', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'helper.php') {
    } else {
        $filehelper->requireClassFile(MOLAJO_LIBRARY.'/installer/installer/'.$file, 'Molajo'.ucfirst(substr($file, 0, strpos($file, '.'))));
    }
}
$files = JFolder::files(MOLAJO_LIBRARY.'/installer/installer/adapters', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(MOLAJO_LIBRARY.'/installer/installer/adapters/'.$file, 'MolajoInstaller'.ucfirst(substr($file, 0, strpos($file, '.'))));
}
/** updater */
$files = JFolder::files(MOLAJO_LIBRARY.'/installer/updater', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(MOLAJO_LIBRARY.'/installer/updater/'.$file, 'Molajo'.ucfirst(substr($file, 0, strpos($file, '.'))));
}
$files = JFolder::files(MOLAJO_LIBRARY.'/installer/updater/adapters', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(MOLAJO_LIBRARY.'/installer/updater/adapters/'.$file, 'MolajoUpdater'.ucfirst(substr($file, 0, strpos($file, '.'))));
}

/**
 *  MVC
 */

/** Controller */
$filehelper->requireClassFile(MOLAJO_LIBRARY_MVC.'/controllers/controller.php', 'MolajoController');
$files = JFolder::files(MOLAJO_LIBRARY_MVC.'/controllers', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'controller.php') {
    } else {
        $filehelper->requireClassFile(MOLAJO_LIBRARY_MVC.'/controllers/'.$file, 'MolajoController'.ucfirst(substr($file, 0, strpos($file, '.'))));
    }
}

/** Models */
$files = JFolder::files(MOLAJO_LIBRARY_MVC.'/models', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(MOLAJO_LIBRARY_MVC.'/models/'.$file, 'MolajoModel'.ucfirst(substr($file, 0, strpos($file, '.'))));
}

/** Views */
$filehelper->requireClassFile(MOLAJO_LIBRARY_MVC.'/views/view.php', 'MolajoView');
$files = JFolder::files(MOLAJO_LIBRARY_MVC.'/views', '\.php$', false, false);
$includeFormat = JRequest::getCmd('format', 'html');
foreach ($files as $file) {
    if ($file == 'layout.php' || $file == 'view.php') {
    } else {
        if (strpos($file, $includeFormat)) {
            $filehelper->requireClassFile(MOLAJO_LIBRARY_MVC.'/views/'.$file, 'MolajoView'.ucfirst(substr($file, 0, strpos($file, '.'))));
        }
    }
}
/** Router */
$files = JFolder::files(MOLAJO_LIBRARY_MVC.'/router/', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(MOLAJO_LIBRARY_MVC.'/router/'.$file, 'MolajoRouter'.ucfirst(substr($file, 0, strpos($file, '.'))));
}

/**
 *  Session
 */
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/session/session.php', 'MolajoSession');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/session/storage.php', 'MolajoSessionStorage');
$files = JFolder::files(MOLAJO_LIBRARY.'/session/storage', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(MOLAJO_LIBRARY.'/session/storage/'.$file, 'MolajoSessionStorage'.ucfirst(substr($file, 0, strpos($file, '.'))));
}

/**
 *  Site
 */
$files = JFolder::files(MOLAJO_LIBRARY.'/site', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(MOLAJO_LIBRARY.'/site/'.$file, 'Molajo'.ucfirst(substr($file, 0, strpos($file, '.'))));
}

