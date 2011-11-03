<?php
/**
 * @package     Molajo
 * @subpackage  Bootstrap
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
if (defined('MOLAJO_APPLICATION')) {
} else {
    define('MOLAJO_APPLICATION', 'site');
    define('DS', DIRECTORY_SEPARATOR);
}
define('MOLAJO', 'Long Live Molajo!');

/** Base Folder and URL */
define('MOLAJO_BASE_FOLDER', strtolower(dirname(__FILE__)));

$baseURL = 'http';
if (isset($_SERVER['HTTPS'])) {
	$baseURL .= 's';
}
$baseURL .= '://';
if (isset($_SERVER['SERVER_NAME'])) {
	$baseURL .= $_SERVER['SERVER_NAME'];
}
if (isset($_SERVER['SERVER_PORT'])) {
    if ($_SERVER['SERVER_PORT'] == '80') {
    } else {
 	    $baseURL .= ":".$_SERVER['SERVER_PORT'];
    }
}
if (isset($_SERVER["REQUEST_URI"])) {
	$baseURL .= $_SERVER['REQUEST_URI'];
}
define('MOLAJO_BASE_URL', strtolower($baseURL));

/** Override Locations */
if (file_exists(MOLAJO_BASE_FOLDER.'/defines.php')) {
	include_once MOLAJO_BASE_FOLDER.'/defines.php';
}

/** Load libraries */
define('LIBRARIES', MOLAJO_BASE_FOLDER.'/libraries/');
include_once LIBRARIES.'/index.php';