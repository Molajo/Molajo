<?php
/**
 * @package     Joomla.Platform
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

// Set the platform root path as a constant if necessary.
if (!defined('JPATH_PLATFORM')) {
    define('JPATH_PLATFORM', dirname(__FILE__));
}

// Set the directory separator define if necessary.
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

// Detect the native operating system type.
$os = strtoupper(substr(PHP_OS, 0, 3));
if (!defined('IS_WIN')) {
    define('IS_WIN', ($os === 'WIN') ? true : false);
}
if (!defined('IS_MAC')) {
    define('IS_MAC', ($os === 'MAC') ? true : false);
}
if (!defined('IS_UNIX')) {
    define('IS_UNIX', (($os !== 'MAC') && ($os !== 'WIN')) ? true : false);
}

// Import the platform version library if necessary.
if (!class_exists('JPlatform')) {
    require_once JPATH_PLATFORM . '/platform.php';
}

// Import the library loader if necessary.
if (!class_exists('JLoader')) {
    require_once JPATH_PLATFORM . '/loader.php';
}

class_exists('JLoader') or die;
