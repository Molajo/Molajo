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
        if (class_exists($class)) {
        } else {
            if (file_exists($file)) {
                JLoader::register($class, $file);
            } else {
                JError::raiseNotice(500, JText::_('PLG_SYSTEM_CREATE_MISSING_CLASS_FILE'.' '.$class.' '.$file), 'error');
                return false;
            }
        }
    }
}