<?php
/**
 * Bootstrap Application
 *
 * @package    Molajo
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
if (substr($_SERVER['DOCUMENT_ROOT'], - 1) == '/') {
    define('ROOT_FOLDER', $_SERVER['DOCUMENT_ROOT']);
} else {
    define('ROOT_FOLDER', $_SERVER['DOCUMENT_ROOT'] . '/');
}

define('BASE_FOLDER', __DIR__);
$base_folder = BASE_FOLDER;

/**
 *  PHP Settings
 */
ini_set('display_errors', 'On');
ini_set('error_reporting', E_ALL);
ini_set('short_open_tag', 'On');
if (function_exists('date_default_timezone_set') && function_exists('date_default_timezone_get')) {
    date_default_timezone_set(@date_default_timezone_get());
}

/**
 *  For User
 */
define('GROUP_ADMINISTRATOR', 1);
define('GROUP_PUBLIC', 2);
define('GROUP_GUEST', 3);
define('GROUP_REGISTERED', 4);

/**
 *  Autoload
 */
require_once __DIR__ . '/Autoload.php';

/**
 *  Inversion of Control
 *
 *  1. Container
 *  2. Service Provider Namespaces and Aliases
 *  3. Service Provider Controller
 */

// 1. Container
require_once BASE_FOLDER . '/vendor/molajo/ioc/Source/Container.php';;
$container_namespace  = 'Molajo\\IoC\\Container';
$container = new $container_namespace();

// 2. Service Provider Namespaces and Aliases
require_once BASE_FOLDER . '/vendor/molajo/ioc/Source/MapServiceProviderNamespaces.php';

$service_provider_folders = readJsonFile(
    $base_folder . '/vendor/molajo/resource/Source/Files/Input/ServiceFolders.json'
);
$folder_namespace = 'Molajo\\Service';
$map = new $ControllerNamespace ($container, $classDependencies, $service_provider_folders, $folder_namespace);
$map->createMap();

die;

$container_namespace  = 'Molajo\\IoC\\Container';
$classDependencies = BASE_FOLDER . '/vendor/molajo/resource/Source/Files/Output/ClassDependencies.json';
$ControllerNamespace = 'Molajo\\IoC\\ServiceProviderController';
$ControllerFile      = BASE_FOLDER . '/vendor/molajo/ioc/Source/ServiceProviderController.php';

// 3. Service Provider Controller
require_once $ControllerFile;
$IoC = new $ControllerNamespace ($container, $classDependencies, $service_provider_folders, $folder_namespace);

require_once $ControllerFile;
$IoC = new $ControllerNamespace ($container, $classDependencies, $service_provider_folders, $folder_namespace);

/**
 *  FrontController
 */
$FCNamespace = 'Molajo\\Controller\\FrontController';
$FCFile      = __DIR__ . '/Controller/FrontController.php';

require_once $FCFile;
$frontController = new $FCNamespace ($IoC);
$frontController->driver();
