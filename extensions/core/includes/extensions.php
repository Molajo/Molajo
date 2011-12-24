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
 *  Formats
 */
$files = JFolder::files(MOLAJO_EXTENSIONS_CORE . '/core/formats', '\.php$', false, false);
foreach ($files as $file) {
        $fileHelper->requireClassFile(MOLAJO_EXTENSIONS_CORE . '/core/formats/' . $file, 'Molajo' . ucfirst(substr($file, 0, strpos($file, '.'))).'Format');
}
/**
 *  Extensions
 */
$files = JFolder::files(MOLAJO_EXTENSIONS_CORE . '/core/extensions', '\.php$', false, false);
foreach ($files as $file) {
        $fileHelper->requireClassFile(MOLAJO_EXTENSIONS_CORE . '/core/extensions/' . $file, 'Molajo' . ucfirst(substr($file, 0, strpos($file, '.'))));
}

/**
 *  Helpers
 */
$files = JFolder::files(MOLAJO_EXTENSIONS_CORE . '/core/helpers', '\.php$', false, false);
foreach ($files as $file) {
    $fileHelper->requireClassFile(MOLAJO_EXTENSIONS_CORE . '/core/helpers/' . $file, 'Molajo' . ucfirst(substr($file, 0, strpos($file, '.'))) . 'Helper');
}

/**
 *  Installer
 */
$files = JFolder::files(MOLAJO_EXTENSIONS_CORE . '/core/installer/adapters', '\.php$', false, false);
foreach ($files as $file) {
    $fileHelper->requireClassFile(MOLAJO_EXTENSIONS_CORE . '/core/installer/adapters/' . $file, 'MolajoInstallerAdapter' . ucfirst(substr($file, 0, strpos($file, '.'))));
}
