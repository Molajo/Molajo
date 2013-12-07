<?php
/**
 * Bootstrap System
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

define('BASE_FOLDER', substr(__DIR__, 0, strlen(__DIR__) - 12));

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
$ContainerFile       = BASE_FOLDER . '/Vendor/Molajo/IoC/Container.php';
$ControllerNamespace = 'Molajo\\IoC\\Controller';
$ControllerFile      = BASE_FOLDER . '/Vendor/Molajo/IoC/Controller.php';

$handler_folders                                              = array();
$handler_folders[BASE_FOLDER . '/Application/Service']        = 'Molajo\\Service';
$handler_folders[BASE_FOLDER . '/Vendor/Molajo/User/Service'] = 'Molajo\\Service';

$classDependencies = BASE_FOLDER . '/Vendor/Molajo/Resource/Files/Output/ClassDependencies.json';

$FCNamespace = 'Molajo\\Controller\\FrontController';
$FCFile      = __DIR__ . '/Controller/FrontController.php';

/**
 *  Instantiate IoC Container, IoC Controller and FrontController
 */
require_once $ContainerFile;
$container = new $ContainerNamespace();

require_once $ControllerFile;
$IoC = new $ControllerNamespace ($container, $handler_folders, $classDependencies);

require_once $FCFile;
$frontController = new $FCNamespace ($IoC);
$frontController->driver();
