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

/** Molajo */
require_once $base_path . '/vendor/commonapi/exception/ExceptionInterface.php';
require_once $base_path . '/vendor/commonapi/exception/RuntimeException.php';

require_once $base_path . '/vendor/commonapi/resource/ClassLoaderInterface.php';
require_once $base_path . '/vendor/commonapi/resource/LocatorInterface.php';
require_once $base_path . '/vendor/commonapi/resource/NamespaceInterface.php';
require_once $base_path . '/vendor/commonapi/resource/SchemeInterface.php';
require_once $base_path . '/vendor/commonapi/resource/ResourceInterface.php';
require_once $base_path . '/vendor/commonapi/resource/AdapterInterface.php';

require_once $base_path . '/vendor/molajo/resource/Source/Adapter/AbstractAdapter.php';
require_once $base_path . '/vendor/molajo/resource/Source/Adapter/ClassLoader.php';
require_once $base_path . '/vendor/molajo/resource/Source/Driver.php';
require_once $base_path . '/vendor/molajo/resource/Source/Scheme.php';

$resource_map_filename = __DIR__ . '/Files/Output/ResourceMap.json';
if (file_exists($resource_map_filename)) {
    $resource_map = readJsonFile($resource_map_filename);
} else {
    $resource_map = array();
}

/** Class Loader */
$class                           = 'Molajo\\Resource\\Adapter\\ClassLoader';
$handler                         = new $class($base_path, $resource_map, array(), array('.php'));
$handler_instance                = array();
$handler_instance['ClassLoader'] = $handler;
$file                            = __DIR__ . '/Files/Input/SchemeClass.json';
$class                           = 'Molajo\\Resource\\Scheme';
$scheme                          = new $class($file);
$class                           = 'Molajo\\Resource\\Driver';
$resource_adapter                = new $class($scheme, $handler_instance);

include __DIR__ . '/SetNamespace.php';

/** Composer */
include $base_path . '/vendor/autoload.php';
