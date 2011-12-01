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
 *  Exceptions - already loaded
 */

/**
 *  Language - language and text already loaded
 */
$filehelper->requireClassFile(MOLAJO_PLATFORM.'/language/languagetransliterate.php', 'MolajoLanguagetransliterate');

/**
 *  Session
 */
$filehelper->requireClassFile(MOLAJO_PLATFORM.'/session/session.php', 'MolajoSession');
$filehelper->requireClassFile(MOLAJO_PLATFORM.'/session/storage.php', 'MolajoSessionStorage');
$files = JFolder::files(MOLAJO_PLATFORM.'/session/storage', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(MOLAJO_PLATFORM.'/session/storage/'.$file, 'MolajoSessionStorage'.ucfirst(substr($file, 0, strpos($file, '.'))));
}

/**
 *  Utilities
 */
$files = JFolder::files(MOLAJO_PLATFORM.'/utilities', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(MOLAJO_PLATFORM.'/utilities/'.$file, 'Molajo'.ucfirst(substr($file, 0, strpos($file, '.'))));
}

