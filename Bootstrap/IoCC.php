<?php
/**
 * Bootstrap Application: IoCC
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */

// 1. Service Provider Namespaces and Aliases
require_once $base_path . '/vendor/molajo/ioc/Source/MapServiceProviders.php';
$service_provider_folders = array();
$temp                     = readJsonFile(
    $base_path . '/vendor/molajo/ioc/Source/Files/Input/ServiceFolders.json'
);
foreach ($temp as $folder) {
    $service_provider_folders[] = $base_path . '/' . $folder;
}

$folder_namespace                = 'Molajo\\Services';
$service_provider_alias_filename = $base_path . '/vendor/molajo/ioc/Source/Files/Output/ServiceProviderAliases.json';

$map_class = 'Molajo\\IoC\\MapServiceProviders';
$map       = new $map_class (
    $service_provider_folders,
    $folder_namespace,
    $service_provider_alias_filename
);

$map->createMap();

// 2. IoC Container
require_once $base_path . '/vendor/molajo/ioc/Source/Container.php';
$classDependencies   = $base_path . '/vendor/molajo/resource/Source/Files/Output/ClassDependencies.json';
$ioc_container_class = 'Molajo\\IoC\\Container';
$ioc_container       = new $ioc_container_class (
    readJsonFile($service_provider_alias_filename),
    $classDependencies
);
