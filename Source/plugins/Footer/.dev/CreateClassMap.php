<?php
/**
 * Create Class Map
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */

function createClassMap($base, $qcn_prefix, array $exclude_array = array())
{
    $function_class_map = array();

    $objects = new RecursiveIteratorIterator (
        new RecursiveDirectoryIterator($base),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($objects as $file_path => $file_object) {

        $include = true;

        if (count($exclude_array) === 0) {
        } else {
            foreach ($exclude_array as $search_for) {
                if (strrpos($file_object->getPath(), $search_for) === false) {
                } else {
                    $include = false;
                }
            }
        }

        if ($file_object->getExtension() === 'php') {
        } else {
            $include = false;
        }

        if ($include === true) {
            $qcn                      = $qcn_prefix . $file_object->getBaseName('.php');
            $path                     = $file_object->getPath() . '/' . $file_object->getFileName();
            $function_class_map[$qcn] = $path;
        }
    }

    return $function_class_map;
}
