<?php
/**
 * Bootstrap Application
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */

$base_path = substr(__DIR__, 0, strlen(__DIR__) - strlen('/Bootstrap'));

ini_set('display_errors', 'On');
ini_set('error_reporting', E_ALL);
if (function_exists('date_default_timezone_set') && function_exists('date_default_timezone_get')) {
    date_default_timezone_set(@date_default_timezone_get());
}

require_once $base_path . '/vendor/autoload.php';
require_once __DIR__ . '/ReadJsonFile.php';
require_once __DIR__ . '/ResourceMaps.php';
require_once __DIR__ . '/Autoload.php';
require_once __DIR__ . '/SetNamespace.php';
require_once __DIR__ . '/IoCC.php';
require_once __DIR__ . '/Files/Input/Requests.php';
require_once __DIR__ . '/Frontcontroller.php';
