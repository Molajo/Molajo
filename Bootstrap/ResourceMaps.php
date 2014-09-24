<?php
/**
 * Build Resource Maps
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http:/www.opensource.org/licenses/mit-license.html MIT License
 */
$classmap_filename     = __DIR__ . '/Files/Output/ClassMap.json';
$interfaces_filename   = __DIR__ . '/Files/Output/Interfaces.json';
$resource_map_filename = __DIR__ . '/Files/Output/ResourceMap.json';
$classmap_files        = array();
$resourcemap_files     = array();

$force_rebuild1 = '';
$force_rebuild2 = '';

/**
 *  Create ClassMap and ResourceMap output
 */
if (file_exists($classmap_filename . $force_rebuild1)
    && file_exists($resource_map_filename . $force_rebuild1)
) {
    $resourcemap_files = readJsonFile($resource_map_filename);

} else {

    $exclude_folders_array = readJsonFile(__DIR__ . '/Files/Input/ExcludeFolders.json');

    $class = 'Molajo\\Resource\\ResourceMap';

    $resource_adapter = new $class (
    // Input
        $base_path,
        $exclude_folders_array,
        // Output
        $classmap_filename,
        $resource_map_filename
    );

    // Executes setNamespace commands
    require_once __DIR__ . '/SetNamespace.php';

    $classmap_files    = $resource_adapter->createMap();
    $resourcemap_files = $resource_adapter->getResourceMap();
}

/**
 *  Classmap - Interfaces - Class Dependencies - Events
 */
if (file_exists($interfaces_filename . $force_rebuild2)) {
} else {

    if (count($classmap_files) === 0) {
        $classmap_files = readJsonFile($classmap_filename);
    }

    $class = 'Molajo\\Resource\\ClassMap';

    try {
        $map_instance = new $class (
        // Input
            $classmap_files,
            // Output
            $interfaces_filename,
            __DIR__ . '/Files/Output/ClassDependencies.json',
            __DIR__ . '/Files/Output/Events.json',
            $base_path
        );
    } catch (Exception $e) {
        throw new Exception('Interface Map ' . $class . ' Exception during Instantiation: ' . $e->getMessage());
    }

    $map_instance->createMap();
}
