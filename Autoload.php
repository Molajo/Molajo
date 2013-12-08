<?php
/**
 * Autoload
 *
 * @package    Molajo
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    http:/www.opensource.org/licenses/mit-license.html MIT License
 */

function readJsonFile($file_name)
{
    $temp_array = array();

    if (file_exists($file_name)) {
    } else {
        return array();
    }

    $input = file_get_contents($file_name);

    $temp = json_decode($input);

    if (count($temp) > 0) {
        $temp_array = array();
        foreach ($temp as $key => $value) {
            $temp_array[$key] = $value;
        }
    }

    return $temp_array;
}
require_once BASE_FOLDER . '/Resources/Api/ConfigurationDataInterface.php';
require_once BASE_FOLDER . '/Resources/Api/ConfigurationInterface.php';
require_once BASE_FOLDER . '/Resources/Api/RegistryInterface.php';
require_once BASE_FOLDER . '/Vendor/commonapi/resource/ClassHandlerInterface.php';
require_once BASE_FOLDER . '/Vendor/commonapi/resource/ClassMapInterface.php';
require_once BASE_FOLDER . '/Vendor/commonapi/resource/ExtensionsInterface.php';
require_once BASE_FOLDER . '/Vendor/commonapi/resource/HandlerInterface.php';
require_once BASE_FOLDER . '/Vendor/commonapi/resource/LocatorInterface.php';
require_once BASE_FOLDER . '/Vendor/commonapi/resource/MapInterface.php';
require_once BASE_FOLDER . '/Vendor/commonapi/resource/NamespaceInterface.php';
require_once BASE_FOLDER . '/Vendor/commonapi/resource/SchemeInterface.php';
// AdapterInterface must follow other interfaces
require_once BASE_FOLDER . '/Vendor/commonapi/resource/AdapterInterface.php';
// carry on ...
require_once BASE_FOLDER . '/Vendor/Molajo/Resource/Handler/AbstractHandler.php';
require_once BASE_FOLDER . '/Vendor/Molajo/Resource/Handler/ClassHandler.php';
require_once BASE_FOLDER . '/Vendor/Molajo/Resource/Adapter.php';
require_once BASE_FOLDER . '/Vendor/Molajo/Resource/Scheme.php';

$resource_map_filename = BASE_FOLDER . '/Vendor/Molajo/Resource/Files/Output/ResourceMap.json';
if (file_exists($resource_map_filename)) {
    $resource_map = readJsonFile($resource_map_filename);
} else {
    $resource_map = array();
}
/** Composer */
include BASE_FOLDER . '/Vendor/autoload.php';

/** Class Loader */
$class                            = 'Molajo\\Resources\\Handler\\ClassHandler';
$handler                          = new $class(BASE_FOLDER, $resource_map, array(), array('.php'));
$handler_instance                 = array();
$handler_instance['ClassHandler'] = $handler;
$file                             = BASE_FOLDER . '/Vendor/Molajo/Resource/Files/Input/SchemeClass.json';
$class                            = 'Molajo\\Resources\\Scheme';
$scheme                           = new $class($file);
$class                            = 'Molajo\\Resources\\Adapter';
$class_loader                     = new $class($scheme, $handler_instance);
include __DIR__ . '/SetNamespace.php';
$hold_class_loader = $class_loader;

/** Resource Map */
if (is_file($resource_map_filename . 'vvvvvvvvvvvvvvvvvvv')) {
} else {
    $class        = 'Molajo\\Resources\\ResourceMap';
    $class_loader = new $class (
        BASE_FOLDER,
        $resource_map_filename = BASE_FOLDER . '/Vendor/Molajo/Resource/Files/Output/ResourceMap.json',
        $interface_map_filename = BASE_FOLDER . '/Vendor/Molajo/Resource/Files/Output/ClassMap.json',
        $interface_map_filename = BASE_FOLDER . '/Vendor/Molajo/Resource/Files/Input/ExcludeFolders.json'
    );

    include __DIR__ . '/SetNamespace.php';

    $class_loader->createMap();

    $class = 'Molajo\\Resources\\ClassMap';

    try {
        $map_instance = new $class (
            BASE_FOLDER . '/Vendor/Molajo/Resource/Files/Output/ClassMap.json',
            BASE_FOLDER . '/Vendor/Molajo/Resource/Files/Output/Interfaces.json',
            BASE_FOLDER . '/Vendor/Molajo/Resource/Files/Output/ClassDependencies.json',
            BASE_FOLDER . '/Vendor/Molajo/Resource/Files/Output/Events.json'
        );
    } catch (Exception $e) {
        throw new Exception ('Interface Map ' . $class . ' Exception during Instantiation: ' . $e->getMessage());
    }

    $map_instance->createMap();
}
