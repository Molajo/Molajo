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
 *  ACL
 */
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/acl/molajo.php', 'MolajoACL');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/acl/core.php', 'MolajoACLCore');

/**
 *  Application
 */
$files = JFolder::files(MOLAJO_LIBRARY.'/application', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'factory.php') {
    } else {
        $filehelper->requireClassFile(MOLAJO_LIBRARY.'/application/'.$file, 'Molajo'.ucfirst(substr($file, 0, strpos($file, '.'))));
    }
}

/**
 *  Component
 */

/** Controller */
$filehelper->requireClassFile(MOLAJO_LIBRARY_CONTROLLERS.'/controller.php', 'MolajoController');
$files = JFolder::files(MOLAJO_LIBRARY_CONTROLLERS, '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'controller.php') {
    } else {
        $filehelper->requireClassFile(MOLAJO_LIBRARY_CONTROLLERS.'/'.$file, 'MolajoController'.ucfirst(substr($file, 0, strpos($file, '.'))));
    }
}

/** Fields */

/** Fields: Attributes */
$filehelper->requireClassFile(MOLAJO_LIBRARY_FIELDS.'/attribute.php', 'MolajoAttribute');
$files = JFolder::files(MOLAJO_LIBRARY_FIELDS.'/attributes', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(MOLAJO_LIBRARY_FIELDS.'/attributes/'.$file, 'MolajoAttribute'.ucfirst(substr($file, 0, strpos($file, '.'))));
}

/** Fields: Form */
$filehelper->requireClassFile(MOLAJO_LIBRARY_FIELDS.'/form/formfield.php', 'MolajoFormField');
$filehelper->requireClassFile(MOLAJO_LIBRARY_FIELDS.'/form/formrule.php', 'MolajoFormRule');
$filehelper->requireClassFile(MOLAJO_LIBRARY_FIELDS.'/form/helper.php', 'MolajoFormHelper');
$filehelper->requireClassFile(MOLAJO_LIBRARY_FIELDS.'/form/form.php', 'MolajoForm');

/** Fields: FieldTypes */
$filehelper->requireClassFile(MOLAJO_LIBRARY_FIELDS.'/fieldtypes/list.php', 'MolajoFormFieldList');
$filehelper->requireClassFile(MOLAJO_LIBRARY_FIELDS.'/fieldtypes/filelist.php', 'MolajoFormFieldFileList');
$filehelper->requireClassFile(MOLAJO_LIBRARY_FIELDS.'/fieldtypes/groupedlist.php', 'MolajoFormFieldGroupedList');
$filehelper->requireClassFile(MOLAJO_LIBRARY_FIELDS.'/fieldtypes/text.php', 'MolajoFormFieldText');

$files = JFolder::files(MOLAJO_LIBRARY_FIELDS.'/fieldtypes', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'list.php' || $file == 'filelist.php' || $file == 'groupedlist.php' || $file == 'text.php') {
    } else {
        $filehelper->requireClassFile(MOLAJO_LIBRARY_FIELDS.'/fieldtypes/'.$file, 'MolajoFormField'.ucfirst(substr($file, 0, strpos($file, '.'))));
    }
}

/** Fields: Fields */
$filehelper->requireClassFile(MOLAJO_LIBRARY_FIELDS.'/field.php', 'MolajoField');
$files = JFolder::files(MOLAJO_LIBRARY_FIELDS.'/fields', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(MOLAJO_LIBRARY_FIELDS.'/fields/'.$file, 'MolajoField'.ucfirst(substr($file, 0, strpos($file, '.'))));
}

/** Models */
$files = JFolder::files(MOLAJO_LIBRARY_MODELS, '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(MOLAJO_LIBRARY_MODELS.'/'.$file, 'MolajoModel'.ucfirst(substr($file, 0, strpos($file, '.'))));
}

/** Model-Elements */
$files = JFolder::files(MOLAJO_LIBRARY_MODELS.'/elements', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(MOLAJO_LIBRARY_MODELS.'/elements/'.$file, 'MolajoElement'.ucfirst(substr($file, 0, strpos($file, '.'))));
}

/** Router */
$files = JFolder::files(MOLAJO_LIBRARY_ROUTER, '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(MOLAJO_LIBRARY_ROUTER.'/'.$file, 'MolajoRouter'.ucfirst(substr($file, 0, strpos($file, '.'))));
}

/** Tables */
$filehelper->requireClassFile(MOLAJO_LIBRARY_TABLES.'/table.php', 'MolajoTable');
$filehelper->requireClassFile(MOLAJO_LIBRARY_TABLES.'/tablenested.php', 'MolajoTableNested');
$files = JFolder::files(MOLAJO_LIBRARY_TABLES, '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'table.php' || $file == 'tablenested.php') {
    } else {
        $filehelper->requireClassFile(MOLAJO_LIBRARY_TABLES.'/'.$file, 'MolajoTable'.ucfirst(substr($file, 0, strpos($file, '.'))));
    }
}

/** Views */
$filehelper->requireClassFile(MOLAJO_LIBRARY_VIEWS.'/view.php', 'MolajoView');
$files = JFolder::files(MOLAJO_LIBRARY_VIEWS, '\.php$', false, false);
$includeFormat = JRequest::getCmd('format', 'html');
foreach ($files as $file) {
    if ($file == 'layout.php' || $file == 'view.php') {
    } else {
        if (strpos($file, $includeFormat)) {
            $filehelper->requireClassFile(MOLAJO_LIBRARY_VIEWS.'/'.$file, 'MolajoView'.ucfirst(substr($file, 0, strpos($file, '.'))));
        }
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
 *  HTML
 */
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/html/editor.php', 'MolajoEditor');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/html/grid.php', 'MolajoGrid');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/html/html.php', 'MolajoHtml');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/html/pagination.php', 'MolajoPagination');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/html/pane.php', 'MolajoPane');
$files = JFolder::files(MOLAJO_LIBRARY.'/html/html', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(MOLAJO_LIBRARY.'/html/html/'.$file, 'MolajoHtml'.ucfirst(substr($file, 0, strpos($file, '.'))));
}

/**
 *  Installer
 */
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/installer/adapter.php', 'MolajoAdapter');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/installer/adapterinstance.php', 'MolajoAdapterInstance');

/** installer */
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
 *  Language (JHelp and JLanguageHelper not used)
 */
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/language/language.php', 'MolajoLanguage');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/language/latin_transliterate.php', 'MolajoLanguageTransliterate');

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
 *  Exceptions
 */
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/application/applicationexception.php', 'ApplicationException');
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/database/databaseexception.php', 'JDatabaseException');
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/log/logexception.php', 'Logexception');
