<?php
/**
 * @package     Molajo
 * @subpackage  Helper
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * File Helper
 *
 * @package     Molajo
 * @subpackage  File Helper
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
    function requireClassFile ($file, $class)
    {
        if (substr(basename($file), 0, 4) == 'HOLD') {
            return;
        }
        if (class_exists($class)) {
            return;
        }
        if (file_exists($file)) {
            JLoader::register($class, $file);
        } else {
            JError::raiseNotice(500, JText::_('MOLAJO_FILE_NOT_FOUND_FOR_CLASS'.' '.$file.' '.$class), 'error');
            return false;
        }

        if (class_exists($class)) {
            return;
        } else {
            JError::raiseNotice(500, JText::_('MOLAJO_CLASS_NOT_FOUND_IN_FILE'.' '.$class.' '.$file), 'error');
            return false;
        }
    }
}