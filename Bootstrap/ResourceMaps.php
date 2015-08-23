<?php
/**
 * Build Resource Maps
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */

$classmap_filename          = __DIR__ . '/Files/Output/ClassMap.json';
$interfaces_filename        = __DIR__ . '/Files/Output/Interfaces.json';
$resource_map_filename      = __DIR__ . '/Files/Output/ResourceMap.json';
$extension_folders_filename = __DIR__ . '/Files/Output/ExtensionFolders.json';
$classmap_files             = array();
$resourcemap_files          = array();
$extension_folders_files    = array();

$force_rebuild1 = '';
$force_rebuild2 = '';

/**
 *  Creates ClassMap.json, ResourceMap.json and ExtensionFolders.json Output files
 */
if (file_exists($classmap_filename . $force_rebuild1)
    && file_exists($resource_map_filename . $force_rebuild1)
) {
    $resourcemap_files = readJsonFile($resource_map_filename);

    require_once $base_path . '/vendor/molajo/fieldhandler/Source/Constraint/Library/kses.php';

} else {
//todo: remove once back in composer
    require_once $base_path . '/vendor/molajo/resource/Source/ResourceMap/Base.php';
    require_once $base_path . '/vendor/molajo/resource/Source/ResourceMap/Folders.php';
    require_once $base_path . '/vendor/molajo/resource/Source/ResourceMap/Prefixes.php';
    require_once $base_path . '/vendor/molajo/resource/Source/ResourceMap.php';

    $exclude_folders_array = readJsonFile(__DIR__ . '/Files/Input/ExcludeFolders.json');

    $class = 'Molajo\\Resource\\ResourceMap';

    $resource_adapter = new $class (
    // Input
        $base_path,
        $exclude_folders_array,
        // Output
        $classmap_filename,
        $resource_map_filename,
        $extension_folders_filename
    );

    // Executes setNamespace commands
    require_once __DIR__ . '/SetNamespace.php';

    $resource_adapter->createMap();
}

/**
 *  Creates Interfaces.json, ClassDependencies.json and Events.json Output files
 */
if (file_exists($interfaces_filename . $force_rebuild2)) {
} else {

//todo: remove once back in composer
    require_once $base_path . '/vendor/molajo/resource/Source/ClassMap/Base.php';
    require_once $base_path . '/vendor/molajo/resource/Source/ClassMap/Events.php';
    require_once $base_path . '/vendor/molajo/resource/Source/ClassMap/Aggregate.php';
    require_once $base_path . '/vendor/molajo/resource/Source/ClassMap/Items.php';
    require_once $base_path . '/vendor/molajo/resource/Source/ClassMap.php';

    $classmap_files = readJsonFile($classmap_filename);

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
