<?php
/**
 * Bootstrap Application
 *
 * @package    Molajo
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */

/**
 *  I. Set Root and Base Folders
 */
if (substr($_SERVER['DOCUMENT_ROOT'], - 1) == '/') {
    define('ROOT_FOLDER', $_SERVER['DOCUMENT_ROOT']);
} else {
    define('ROOT_FOLDER', $_SERVER['DOCUMENT_ROOT'] . '/');
}

define('BASE_FOLDER', __DIR__);
$base_folder = BASE_FOLDER;

/**
 *  II. Set PHP Settings
 */
ini_set('display_errors', 'On');
ini_set('error_reporting', E_ALL);
ini_set('short_open_tag', 'On');
if (function_exists('date_default_timezone_set') && function_exists('date_default_timezone_get')) {
    date_default_timezone_set(@date_default_timezone_get());
}

/**
 *  III. Set PHP Constants For User Package
 */
define('GROUP_ADMINISTRATOR', 1);
define('GROUP_PUBLIC', 2);
define('GROUP_GUEST', 3);
define('GROUP_REGISTERED', 4);

/**
 *  IV. Process Autoload
 */
require_once __DIR__ . '/Autoload.php';

/**
 *  V. Inversion of Control Processing
 *
 *  1. Service Provider Namespaces and Aliases
 *  2. IoC Container
 */

// 1. Service Provider Namespaces and Aliases
require_once BASE_FOLDER . '/vendor/molajo/ioc/Source/MapServiceProviders.php';
$service_provider_folders = array();
$temp                     = readJsonFile(
    $base_folder . '/vendor/molajo/ioc/Source/Files/Input/ServiceFolders.json'
);
foreach ($temp as $folder) {
    $service_provider_folders[] = $base_folder . '/' . $folder;
}

$folder_namespace                = 'Molajo\\Service';
$service_provider_alias_filename = $base_folder . '/vendor/molajo/ioc/Source/Files/Output/ServiceProviderAliases.json';

$map_class = 'Molajo\\IoC\\MapServiceProviders';
$map       = new $map_class (
    $service_provider_folders,
    $folder_namespace,
    $service_provider_alias_filename
);

$map->createMap();

// 2. IoC Container
require_once BASE_FOLDER . '/vendor/molajo/ioc/Source/Container.php';
$classDependencies    = BASE_FOLDER . '/vendor/molajo/resource/Source/Files/Output/ClassDependencies.json';
$ioc_container_class = 'Molajo\\IoC\\Container';
$ioc_container       = new $ioc_container_class (
    readJsonFile($service_provider_alias_filename),
    $classDependencies
);

/**
 *  VI. Fire off FrontController
 */
require_once BASE_FOLDER . '/vendor/molajo/framework/Source/Controller/FrontController.php';
$front_controller_class = 'Molajo\\Controller\\FrontController';
$front_controller       = new $front_controller_class ($ioc_container, $base_folder);

$front_controller->driver();
