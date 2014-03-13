<?php
/**
 * Bootstrap Application: IoCC
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */

// 1. Factory Method Namespaces and Aliases
require_once $base_path . '/vendor/molajo/ioc/Source/MapFactories.php';
$factory_method_folders = array();
$temp                   = readJsonFile(__DIR__ . '/Files/Input/Factories.json');
foreach ($temp as $folder) {
    $factory_method_folders[] = $base_path . '/' . $folder;
}

$folder_namespace       = 'Molajo\\Factories';
$adapter_alias_filename = __DIR__ . '/Files/Output/FactoryMethodAliases.json';

$map_class = 'Molajo\\IoC\\MapFactories';
$map       = new $map_class ($factory_method_folders, $folder_namespace, $adapter_alias_filename);

$map->createMap();

// 2. IoC Container
require_once $base_path . '/vendor/molajo/ioc/Source/Container.php';
$classDependencies   = __DIR__ . '/Files/Output/ClassDependencies.json';
$ioc_container_class = 'Molajo\\IoC\\Container';
$ioc_container       = new $ioc_container_class (readJsonFile($adapter_alias_filename), $classDependencies);
