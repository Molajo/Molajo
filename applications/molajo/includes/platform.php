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
 *  Exceptions - already loaded
 */

/**
 *  Language - language and text already loaded
 */
$filehelper->requireClassFile(MOLAJO_PLATFORM . '/language/languagetransliterate.php', 'MolajoTransliterateHelper');

/**
 *  MVC
 */

/** Controller */
$filehelper->requireClassFile(MOLAJO_MVC . '/controllers/controller.php', 'MolajoController');
$files = JFolder::files(MOLAJO_MVC . '/controllers', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'controller.php') {
    } else {
        $filehelper->requireClassFile(MOLAJO_MVC . '/controllers/' . $file, 'MolajoController' . ucfirst(substr($file, 0, strpos($file, '.'))));
    }
}

/** Models */
$files = JFolder::files(MOLAJO_MVC . '/models', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(MOLAJO_MVC . '/models/' . $file, 'MolajoModel' . ucfirst(substr($file, 0, strpos($file, '.'))));
}

/** Router */
$files = JFolder::files(MOLAJO_MVC . '/router/', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(MOLAJO_MVC . '/router/' . $file, 'MolajoRouter' . ucfirst(substr($file, 0, strpos($file, '.'))));
}

/** Views */
$filehelper->requireClassFile(MOLAJO_MVC . '/views/view.php', 'MolajoView');
$files = JFolder::files(MOLAJO_MVC . '/views', '\.php$', false, false);
//$includeFormat = JRequest::getCmd('format', 'html');
foreach ($files as $file) {
    if ($file == 'layout.php' || $file == 'view.php') {
    } else {
//        if (strpos($file, $includeFormat)) {
            $filehelper->requireClassFile(MOLAJO_MVC . '/views/' . $file, 'MolajoView' . ucfirst(substr($file, 0, strpos($file, '.'))));
//        }
    }
}

/**
 *  Session
 */
$filehelper->requireClassFile(MOLAJO_PLATFORM . '/session/session.php', 'MolajoSession');
$filehelper->requireClassFile(MOLAJO_PLATFORM . '/session/storage.php', 'MolajoSessionStorage');
$files = JFolder::files(MOLAJO_PLATFORM . '/session/storage', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(MOLAJO_PLATFORM . '/session/storage/' . $file, 'MolajoSessionStorage' . ucfirst(substr($file, 0, strpos($file, '.'))));
}

/**
 *  Utilities
 */
$files = JFolder::files(MOLAJO_PLATFORM . '/utilities', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(MOLAJO_PLATFORM . '/utilities/' . $file, 'Molajo' . ucfirst(substr($file, 0, strpos($file, '.'))));
}

