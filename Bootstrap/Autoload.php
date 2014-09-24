<?php
/**
 * Autoload
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http:/www.opensource.org/licenses/mit-license.html MIT License
 */

$class                           = 'Molajo\\Resource\\Adapter\\ClassLoader';
$handler                         = new $class($base_path, $resourcemap_files, array(), array('.php'));
$handler_instance                = array();
$handler_instance['ClassLoader'] = $handler;

$file             = __DIR__ . '/Files/Input/SchemeClass.json';
$class            = 'Molajo\\Resource\\Scheme';
$scheme           = new $class($file);
$class            = 'Molajo\\Resource\\Driver';
$resource_adapter = new $class($scheme, $handler_instance);
