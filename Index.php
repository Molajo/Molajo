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

/** PHP Settings */
ini_set('display_errors', 'On');
ini_set('error_reporting', E_ALL);
ini_set('short_open_tag', 'On');
if (function_exists('date_default_timezone_set') && function_exists('date_default_timezone_get')) {
    date_default_timezone_set(@date_default_timezone_get());
}

if (file_exists(BASE_FOLDER . '/defines.php')) {
    include_once BASE_FOLDER . '/defines.php';
}

define('GROUP_ADMINISTRATOR', 1);
define('GROUP_PUBLIC', 2);
define('GROUP_GUEST', 3);
define('GROUP_REGISTERED', 4);
$base_folder = BASE_FOLDER;
/**
 *  Autoload and OverrideAutoload (if needed to override previous)
 */
if (file_exists(BASE_FOLDER . '/OverrideAutoload.php')) {
    require_once BASE_FOLDER . '/OverrideAutoload.php';
} else {
    require_once __DIR__ . '/Autoload.php';
}

/**
 *  Defined before OverrideAutoload.php where it could be changed
 *
 *  1. IoC Container, Controller and Service Folders
 *  2. Resource Definitions
 *  3. FrontController
 */
$ContainerNamespace  = 'Molajo\\IoC\\Container';
$ContainerFile       = BASE_FOLDER . '/vendor/Molajo/IoC/Container.php';
$ControllerNamespace = 'Molajo\\IoC\\Controller';
$ControllerFile      = BASE_FOLDER . '/vendor/Molajo/IoC/Controller.php';

$service_provider_folders                                              = array();
$service_provider_folders[BASE_FOLDER . '/Application/Service']        = 'Molajo\\Service';
$service_provider_folders[BASE_FOLDER . '/vendor/Molajo/User/Service'] = 'Molajo\\Service';

$classDependencies = BASE_FOLDER . '/vendor/molajo/resource/Files/Output/ClassDependencies.json';

$FCNamespace = 'Molajo\\Controller\\FrontController';
$FCFile      = __DIR__ . '/Controller/FrontController.php';

/**
 *  Instantiate IoC Container, Service Provider Controller and FrontController
 */
require_once $ContainerFile;
$container = new $ContainerNamespace();

require_once $ControllerFile;
$IoC = new $ControllerNamespace ($container, $service_provider_folders, $classDependencies);

require_once $FCFile;
$frontController = new $FCNamespace ($IoC);
$frontController->driver();