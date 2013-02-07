<?php
/**
 * Filesystem
 *
 * @package   Molajo
 * @copyright 2013 Amy Stephen. All rights reserved.
 * @license   MIT
 */
define('MOLAJO', 'This is a Molajo Distribution');

if (substr($_SERVER['DOCUMENT_ROOT'], - 1) == '/') {
    define('ROOT_FOLDER', $_SERVER['DOCUMENT_ROOT']);
} else {
    define('ROOT_FOLDER', $_SERVER['DOCUMENT_ROOT'] . '/');
}

define('BASE_FOLDER', __DIR__);

/** Load Classloader */
include BASE_FOLDER . '/' . 'ClassLoader.php';
include BASE_FOLDER . '/Tests/Integration/Data.php';

$loader = new ClassLoader();
$loader->add('Molajo\Filesystem', BASE_FOLDER . '/Src');
$loader->add('Tests\Integration', BASE_FOLDER . '/Tests');

$loader->register();
