<?php
/**
 * Bootstrap Application: IoCC
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */

// 1. Factory Method Namespaces and Aliases
require_once $base_path . '/vendor/molajo/ioc/Source/MapFactories.php';
$factory_method_folders = array();
$temp                   = readJsonFile(__DIR__ . '/Files/Input/Factories.json');
foreach ($temp as $folder) {
    $factory_method_folders[] = $base_path . '/' . $folder;
}

// 2. Factory Method Aliases
$adapter_alias_filename = __DIR__ . '/Files/Output/FactoryMethodAliases.json';
if (is_file($adapter_alias_filename)) {
} else {
    $folder_namespace       = 'Molajo\\Factories';
    $map_class              = 'Molajo\\IoC\\MapFactories';
    $map                    = new $map_class ($factory_method_folders, $folder_namespace, $adapter_alias_filename);

    $map->createMap();
}

// 3. Factory Method Scheduling
$factory_method_aliases      = readJsonFile(__DIR__ . '/Files/Output/FactoryMethodAliases.json');
$class_dependencies_filename = __DIR__ . '/Files/Output/ClassDependencies.json';
$standard_adapter_namespaces = 'Molajo\\IoC\\FactoryMethod\\Standard';

$schedule_factory_class = 'Molajo\\IoC\\Schedule';
$schedule_factory       = new $schedule_factory_class(
    $factory_method_aliases,
    $class_dependencies_filename,
    $standard_adapter_namespaces,
    $base_path
);
