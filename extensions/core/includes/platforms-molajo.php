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
 *  Exceptions - already loaded
 */

/**
 *  Language - language and text already loaded
 */
$fileHelper->requireClassFile(PLATFORM_MOLAJO . '/language/languagetransliterate.php', 'MolajoTransliterateHelper');

/**
 *  MVC
 */

/** Controller */
$fileHelper->requireClassFile(PLATFORM_MOLAJO_MVC . '/controllers/controller.php', 'MolajoController');
$files = JFolder::files(PLATFORM_MOLAJO_MVC . '/controllers', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'controller.php') {
    } else {
        $fileHelper->requireClassFile(PLATFORM_MOLAJO_MVC . '/controllers/' . $file, 'MolajoController' . ucfirst(substr($file, 0, strpos($file, '.'))));
    }
}

/** Models */
$fileHelper->requireClassFile(PLATFORM_MOLAJO_MVC . '/models/model.php', 'MolajoModel');
$files = JFolder::files(PLATFORM_MOLAJO_MVC . '/models', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'view.php') {
    } else {
        $fileHelper->requireClassFile(PLATFORM_MOLAJO_MVC . '/models/' . $file, 'MolajoModel' . ucfirst(substr($file, 0, strpos($file, '.'))));
    }
}

/** Views */
$fileHelper->requireClassFile(PLATFORM_MOLAJO_MVC . '/views/view.php', 'MolajoView');
$files = JFolder::files(PLATFORM_MOLAJO_MVC . '/views', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'view.php' || $file == 'view.php') {
    } else {
        $fileHelper->requireClassFile(PLATFORM_MOLAJO_MVC . '/views/' . $file, 'MolajoView' . ucfirst(substr($file, 0, strpos($file, '.'))));
    }
}

/**
 *  Session
 */
$fileHelper->requireClassFile(PLATFORM_MOLAJO . '/session/session.php', 'MolajoSession');
$fileHelper->requireClassFile(PLATFORM_MOLAJO . '/session/storage.php', 'MolajoSessionStorage');
$files = JFolder::files(PLATFORM_MOLAJO . '/session/storage', '\.php$', false, false);
foreach ($files as $file) {
    $fileHelper->requireClassFile(PLATFORM_MOLAJO . '/session/storage/' . $file, 'MolajoSessionStorage' . ucfirst(substr($file, 0, strpos($file, '.'))));
}

/**
 *  Utilities
 */
$files = JFolder::files(PLATFORM_MOLAJO . '/utilities', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'LoremIpsum.class.php') {
        $fileHelper->requireClassFile(PLATFORM_MOLAJO . '/utilities/' . $file, 'LoremIpsumGenerator');
    } else {
        $fileHelper->requireClassFile(PLATFORM_MOLAJO . '/utilities/' . $file, 'Molajo' . ucfirst(substr($file, 0, strpos($file, '.'))));
    }
}

