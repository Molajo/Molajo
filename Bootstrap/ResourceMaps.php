<?php
/**
 * Build Resource Maps
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http:/www.opensource.org/licenses/mit-license.html MIT License
 */

if (is_file($resource_map_filename . 'abcdefghijklmnopqrstuvwxyz')) {
} else {

    require_once $base_path . '/vendor/commonapi/resource/MapInterface.php';
    require_once $base_path . '/vendor/molajo/resource/Source/ResourceMap.php';

    $class = 'Molajo\\Resource\\ResourceMap';

    $resource_adapter = new $class (
        $base_path,
        $resource_map_filename = __DIR__ . '/Files/Output/ResourceMap.json',
        $interface_map_filename = __DIR__ . '/Files/Output/ClassMap.json',
        $interface_map_filename = __DIR__ . '/Files/Input/ExcludeFolders.json'
    );

    include __DIR__ . '/Files/Input/SetNamespace.php';

    $resource_adapter->createMap();

    require_once $base_path . '/vendor/molajo/resource/Source/ClassMap.php';

    $class = 'Molajo\\Resource\\ClassMap';

    try {
        $map_instance = new $class (
            __DIR__ . '/Files/Output/ClassMap.json',
            __DIR__ . '/Files/Output/Interfaces.json',
            __DIR__ . '/Files/Output/ClassDependencies.json',
            __DIR__ . '/Files/Output/Events.json',
            __DIR__ . '/Files/Output/Stats.json'
        );
    } catch (Exception $e) {
        throw new Exception ('Interface Map ' . $class . ' Exception during Instantiation: ' . $e->getMessage());
    }

    $map_instance->createMap();
}

