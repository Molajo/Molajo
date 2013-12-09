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

require_once $base_folder . '/vendor/molajo/framework/Resource/Api/ConfigurationDataInterface.php';
require_once $base_folder . '/vendor/molajo/framework/Resource/Api/ConfigurationInterface.php';
require_once $base_folder . '/vendor/molajo/framework/Resource/Api/RegistryInterface.php';
require_once $base_folder . '/vendor/commonapi/resource/ClassHandlerInterface.php';
require_once $base_folder . '/vendor/commonapi/resource/ClassMapInterface.php';
require_once $base_folder . '/vendor/commonapi/resource/ExtensionsInterface.php';
require_once $base_folder . '/vendor/commonapi/resource/HandlerInterface.php';
require_once $base_folder . '/vendor/commonapi/resource/LocatorInterface.php';
require_once $base_folder . '/vendor/commonapi/resource/MapInterface.php';
require_once $base_folder . '/vendor/commonapi/resource/NamespaceInterface.php';
require_once $base_folder . '/vendor/commonapi/resource/SchemeInterface.php';
// AdapterInterface must follow other interfaces
require_once $base_folder . '/vendor/commonapi/resource/AdapterInterface.php';
// carry on ...
require_once $base_folder . '/vendor/molajo/resource/Handler/AbstractHandler.php';
require_once $base_folder . '/vendor/molajo/resource/Handler/ClassHandler.php';
require_once $base_folder . '/vendor/molajo/resource/Adapter.php';
require_once $base_folder . '/vendor/molajo/resource/Scheme.php';

$resource_map_filename = $base_folder . '/vendor/molajo/resource/Files/Output/ResourceMap.json';
if (file_exists($resource_map_filename)) {
    $resource_map = readJsonFile($resource_map_filename);
} else {
    $resource_map = array();
}
/** Composer */
include $base_folder . '/vendor/autoload.php';

/** Class Loader */
$class                            = 'Molajo\\Resource\\Handler\\ClassHandler';
$handler                          = new $class($base_folder, $resource_map, array(), array('.php'));
$handler_instance                 = array();
$handler_instance['ClassHandler'] = $handler;
$file                             = $base_folder . '/vendor/molajo/resource/Files/Input/SchemeClass.json';
$class                            = 'Molajo\\Resource\\Scheme';
$scheme                           = new $class($file);
$class                            = 'Molajo\\Resource\\Adapter';
$class_loader                     = new $class($scheme, $handler_instance);

include __DIR__ . '/SetNamespace.php';

$hold_class_loader = $class_loader;

/** Resource Map */
if (is_file($resource_map_filename . 'vvvvvvvvvvvvvvvvvvv')) {
} else {
    $class        = 'Molajo\\Resource\\ResourceMap';
    $class_loader = new $class (
        $base_folder,
        $resource_map_filename = $base_folder . '/vendor/molajo/resource/Files/Output/ResourceMap.json',
        $interface_map_filename = $base_folder . '/vendor/molajo/resource/Files/Output/ClassMap.json',
        $interface_map_filename = $base_folder . '/vendor/molajo/resource/Files/Input/ExcludeFolders.json'
    );

    include __DIR__ . '/SetNamespace.php';

    $class_loader->createMap();

    $class = 'Molajo\\Resource\\ClassMap';

    try {
        $map_instance = new $class (
            $base_folder . '/vendor/molajo/resource/Files/Output/ClassMap.json',
            $base_folder . '/vendor/molajo/resource/Files/Output/Interfaces.json',
            $base_folder . '/vendor/molajo/resource/Files/Output/ClassDependencies.json',
            $base_folder . '/vendor/molajo/resource/Files/Output/Events.json'
        );
    } catch (Exception $e) {
        throw new Exception ('Interface Map ' . $class . ' Exception during Instantiation: ' . $e->getMessage());
    }

    $map_instance->createMap();
}
