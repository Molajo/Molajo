<?php
/**
 * @package     Molajo
 * @subpackage  Extension
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 *  File Helper
 */
$fileHelper = new MolajoFileHelper();

/**
 *  Extensions
 */
$fileHelper->requireClassFile(MOLAJO_CMS_CORE . '/extensions/configuration.php', 'MolajoExtensionConfiguration');
$files = JFolder::files(MOLAJO_CMS_CORE . '/extensions', '\.php$', false, false);
foreach ($files as $file) {
        $fileHelper->requireClassFile(MOLAJO_CMS_CORE . '/extensions/' . $file, 'Molajo' . ucfirst(substr($file, 0, strpos($file, '.'))));
}

/**
 *  Document
 */

/**
 *  Helpers
 */
$files = JFolder::files(MOLAJO_CMS_CORE . '/helpers', '\.php$', false, false);
foreach ($files as $file) {
    $fileHelper->requireClassFile(MOLAJO_CMS_CORE . '/helpers/' . $file, 'Molajo' . ucfirst(substr($file, 0, strpos($file, '.'))) . 'Helper');
}

/**
 *  Installer
 */
$files = JFolder::files(MOLAJO_CMS_CORE . '/installer/adapters', '\.php$', false, false);
foreach ($files as $file) {
    $fileHelper->requireClassFile(MOLAJO_CMS_CORE . '/installer/adapters/' . $file, 'MolajoInstallerAdapter' . ucfirst(substr($file, 0, strpos($file, '.'))));
}
