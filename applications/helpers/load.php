<?php
/**
 * @package     Molajo
 * @subpackage  Helper
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * File
 *
 * @package     Molajo
 * @subpackage  Helper
 * @since       1.0
 */
class MolajoLoadHelper
{
    /**
     * requireClassFile
     *
     * @param string $file
     * @param string $class
     *
     * @return Boolean
     */
    function requireClassFile($file, $class)
    {
        if (substr(basename($file), 0, 4) == 'HOLD') {
            return true;
        }
        if (class_exists($class)) {
            return true;
        }

        /** Dashes/Underscores removed, next word uppercased for class name */
        if (strpos($class, '_') === false) {
        } else {
            $classArray = explode('_', $class);
            $class = '';
            foreach ($classArray as $item) {
                $class .= ucfirst($item);
            }
        }

        if (file_exists($file)) {
            JLoader::register($class, $file);
        } else {
            if (MolajoBase::$application == null) {
                echo 'MolajoLoadHelper Error: file not found ' . $file . ' for Class: ' . $class;
                exit;
            } else {
//            if (class_exists('MolajoError') && class_exists('MolajoTextService') && class_exists('MolajoController') && class_exists('MolajoApplication')) {
                MolajoError::raiseNotice(500, MolajoTextService::_('MOLAJO_FILE_NOT_FOUND_FOR_CLASS' . ' ' . $file . ' ' . $class), 'error');
                return false;
            }
        }

        if (class_exists($class)) {
//            $api = MOLAJO_BASE_FOLDER.'/api.txt';
//            $apiFile = fopen($api, 'a') or die("Cannot find API file.");
//            $apiData = $class.chr(10);
//            fwrite($apiFile, $apiData);
//            fclose($apiFile);
            return true;
        } else {
            if (MolajoBase::$application == null) {
                echo 'MolajoLoadHelper Error class not found ' . $class;
                exit;
            } else {
            //if (class_exists('MolajoError') && class_exists('MolajoTextService') && class_exists('MolajoController') && class_exists('MolajoApplication')) {
                MolajoError::raiseNotice(500, MolajoTextService::_('MOLAJO_CLASS_NOT_FOUND_IN_FILE' . ' ' . $class . ' ' . $file), 'error');
                return false;
            }
        }
    }
}
