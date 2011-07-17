<?php
/**
 * @version     $id: file.php
 * @package     Molajo
 * @subpackage  Helper
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die();

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