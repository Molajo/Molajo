<?php
/**
 * Set Namespace Prefixes and Paths
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */

/**
 *  Composer Packages
 */
$map = require $base_path . '/vendor/composer/autoload_psr4.php';

foreach ($map as $namespace => $paths) {
    foreach ($paths as $path) {
        $resource_adapter->setNamespace($namespace, substr($path, strlen($base_path) + 1, 99999) . '/');
    }
}

/**
 *  Plugins
 */
$file_paths = scandir($base_path . '/Source/Plugins');

foreach ($file_paths as $file_path) {
    if (substr($file_path, 0, 1) === '.') {
    } else {
        if (filetype($base_path . '/Source/Plugins/' . $file_path) === 'dir') {

            $resource_adapter->setNamespace(
                'Molajo\\Plugins\\' . $file_path . '\\',
                'Source/Plugins/' . $file_path . '/Source/'
            );
        }
    }
}

/**
 *  Resources
 */
$resource_adapter->setNamespace('Molajo\\Resources\\', $base_path . '/Source/Resources/');

/**
 *  Themes and Views (Pages, Templates, and Wraps)
 */
$file_paths = scandir($base_path . '/Source/Themes');

foreach ($file_paths as $file_path) {
    if (substr($file_path, 0, 1) === '.') {
    } else {
        if (filetype($base_path . '/Source/Themes/' . $file_path) === 'dir') {
            $resource_adapter->setNamespace('Molajo\\Themes\\' . $file_path . '\\',
                'Source/Themes/' . $file_path . '/');
        }
    }
}

/**
 *  Sites
 */
$resource_adapter->setNamespace('Molajo\\Sites\\', 'Sites/');
