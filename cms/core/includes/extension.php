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
 *  Document
 */
$fileHelper->requireClassFile(MOLAJO_CMS_CORE . '/document/document.php', 'MolajoDocument');
$fileHelper->requireClassFile(MOLAJO_CMS_CORE . '/document/renderer.php', 'MolajoDocumentRenderer');

$format = JRequest::getCmd('format', 'html');
if ($format == 'error' || $format == 'feed' || $format == 'raw') {
    $includeFormat = $format;
} else {
    $includeFormat = 'html';
}
$formatClass = 'MolajoDocument' . ucfirst($includeFormat);
if (class_exists($formatClass)) {
} else {
    $path = MOLAJO_CMS_CORE . '/document/' . $includeFormat . '/' . $includeFormat . '.php';
    $formatClass = 'MolajoDocument' . ucfirst($includeFormat);
    $fileHelper->requireClassFile(MOLAJO_CMS_CORE . '/document/' . $includeFormat . '/' . $includeFormat . '.php', $formatClass);
}

/**
 *  Extensions
 */
$files = JFolder::files(MOLAJO_CMS_CORE . '/extensions', '\.php$', false, false);
foreach ($files as $file) {
        $fileHelper->requireClassFile(MOLAJO_CMS_CORE . '/extensions/' . $file, 'Molajo' . ucfirst(substr($file, 0, strpos($file, '.'))));
}

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
