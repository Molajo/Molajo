<?php
/**
 * Autoload
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
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

require_once $base_path . '/vendor/commonapi/exception/ExceptionInterface.php';
require_once $base_path . '/vendor/commonapi/exception/RuntimeException.php';
require_once $base_path . '/vendor/commonapi/ioc/ContainerInterface.php';
require_once $base_path . '/vendor/commonapi/ioc/FactoryMethodControllerInterface.php';
require_once $base_path . '/vendor/commonapi/ioc/FactoryMethodInterface.php';
require_once $base_path . '/vendor/commonapi/ioc/FactoryMethodBatchSchedulingInterface.php';
require_once $base_path . '/vendor/commonapi/ioc/FactoryMethodSchedulingInterface.php';
require_once $base_path . '/vendor/molajo/ioc/Source/FactoryBase.php';
require_once $base_path . '/vendor/molajo/ioc/Source/StandardFactoryMethod.php';
require_once $base_path . '/vendor/molajo/ioc/Source/Container.php';
require_once $base_path . '/vendor/molajo/ioc/Source/FactoryMethodController.php';

require_once $base_path . '/vendor/molajo/application/Source/Resource/Api/ConfigurationDataInterface.php';
require_once $base_path . '/vendor/molajo/application/Source/Resource/Api/ConfigurationInterface.php';
require_once $base_path . '/vendor/molajo/application/Source/Resource/Api/RegistryInterface.php';

require_once $base_path . '/vendor/commonapi/resource/ClassHandlerInterface.php';
require_once $base_path . '/vendor/commonapi/resource/ClassMapInterface.php';
require_once $base_path . '/vendor/commonapi/resource/ExtensionsInterface.php';
require_once $base_path . '/vendor/commonapi/resource/HandlerInterface.php';
require_once $base_path . '/vendor/commonapi/resource/LocatorInterface.php';
require_once $base_path . '/vendor/commonapi/resource/MapInterface.php';
require_once $base_path . '/vendor/commonapi/resource/NamespaceInterface.php';
require_once $base_path . '/vendor/commonapi/resource/SchemeInterface.php';
// AdapterInterface must follow other interfaces
require_once $base_path . '/vendor/commonapi/resource/AdapterInterface.php';
// carry on ...
require_once $base_path . '/vendor/molajo/resource/Source/Handler/AbstractHandler.php';
require_once $base_path . '/vendor/molajo/resource/Source/Handler/ClassHandler.php';
require_once $base_path . '/vendor/molajo/resource/Source/Adapter.php';
require_once $base_path . '/vendor/molajo/resource/Source/Scheme.php';

//require_once $base_path . '/vendor/molajo/application/Factories/Runtimedata/RuntimedataFactoryMethod.php';
//require_once $base_path . '/vendor/molajo/application/Factories/Plugindata/PlugindataFactoryMethod.php';
//require_once $base_path . '/vendor/molajo/resource/Factories/Resource/ResourceFactoryMethod.php';
//require_once $base_path . '/vendor/molajo/resource/Factories/Resourcedata/ResourcedataFactoryMethod.php';
//require_once $base_path . '/vendor/molajo/fieldhandler/Factories/Fieldhandler/FieldhandlerFactoryMethod.php';

$resource_map_filename = __DIR__ . '/Files/Output/ResourceMap.json';
if (file_exists($resource_map_filename)) {
    $resource_map = readJsonFile($resource_map_filename);
} else {
    $resource_map = array();
}

/** Composer */
include $base_path . '/vendor/autoload.php';

/** Class Loader */
$class                            = 'Molajo\\Resource\\Handler\\ClassHandler';
$handler                          = new $class($base_path, $resource_map, array(), array('.php'));
$handler_instance                 = array();
$handler_instance['ClassHandler'] = $handler;
$file                             = __DIR__ . '/Files/Input/SchemeClass.json';
$class                            = 'Molajo\\Resource\\Scheme';
$scheme                           = new $class($file);
$class                            = 'Molajo\\Resource\\Adapter';
$resource_adapter                 = new $class($scheme, $handler_instance);

include __DIR__ . '/SetNamespace.php';

$hold_resource_adapter = $resource_adapter;

/** Resource Map */
if (is_file($resource_map_filename . 'abcdefghijklmnopqrstuvwxyz')) {
} else {

    require_once $base_path . '/vendor/molajo/resource/Source/ResourceMap.php';

    $class = 'Molajo\\Resource\\ResourceMap';

    $resource_adapter = new $class (
        $base_path,
        $resource_map_filename = __DIR__ . '/Files/Output/ResourceMap.json',
        $interface_map_filename = __DIR__ . '/Files/Output/ClassMap.json',
        $interface_map_filename = __DIR__ . '/Files/Input/ExcludeFolders.json'
    );

    include __DIR__ . '/SetNamespace.php';

    $resource_adapter->createMap();

    require_once $base_path . '/vendor/molajo/resource/Source/ClassMap.php';

    $class = 'Molajo\\Resource\\ClassMap';

    try {
        $map_instance = new $class (
            __DIR__ . '/Files/Output/ClassMap.json',
            __DIR__ . '/Files/Output/Interfaces.json',
            __DIR__ . '/Files/Output/ClassDependencies.json',
            __DIR__ . '/Files/Output/Events.json'
        );
    } catch (Exception $e) {
        throw new Exception ('Interface Map ' . $class . ' Exception during Instantiation: ' . $e->getMessage());
    }

    $map_instance->createMap();
}
