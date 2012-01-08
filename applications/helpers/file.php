<?php
/**
 * @package     Molajo
 * @subpackage  Helper
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * File Helper
 *
 * @package     Molajo
 * @subpackage  Helper
 * @since       1.0
 */
class MolajoFileHelper
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
        if (file_exists($file)) {
            JLoader::register($class, $file);
        } else {
            if (MolajoController::$application == null) {
                echo 'MolajoFileHelper Error: file not found ' . $file . ' for Class: ' . $class;
                exit;
            } else {
//            if (class_exists('MolajoError') && class_exists('MolajoTextHelper') && class_exists('MolajoController') && class_exists('MolajoControllerApplication')) {
                MolajoError::raiseNotice(500, MolajoTextHelper::_('MOLAJO_FILE_NOT_FOUND_FOR_CLASS' . ' ' . $file . ' ' . $class), 'error');
                return false;
            }
        }

        if (class_exists($class)) {
            return true;
        } else {
            if (MolajoController::$application == null) {
                echo 'MolajoFileHelper Error class not found ' . $class;
                exit;
            } else {
            //if (class_exists('MolajoError') && class_exists('MolajoTextHelper') && class_exists('MolajoController') && class_exists('MolajoControllerApplication')) {
                MolajoError::raiseNotice(500, MolajoTextHelper::_('MOLAJO_CLASS_NOT_FOUND_IN_FILE' . ' ' . $class . ' ' . $file), 'error');
                return false;
            }
        }
    }
}