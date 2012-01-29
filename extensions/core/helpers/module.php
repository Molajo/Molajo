<?php
/**
 * @package     Molajo
 * @subpackage  Helper
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Module
 *
 * @package     Molajo
 * @subpackage  Module
 * @since       1.0
 */
abstract class MolajoModuleHelper
{

    /**
     * get
     *
     * Retrieve module data
     *
     * @return  mixed    An array of module data objects, or a module data object.
     * @since   1.0
     */
    static public function get($module_name)
    {
        $rows = MolajoExtensionHelper::get(MOLAJO_ASSET_TYPE_EXTENSION_MODULE, $module_name);
        if (count($rows) == 0) {
            return array();
        }

        foreach ($rows as $row) {
        }

        return $row;
    }

    /**
     * getPath
     *
     * Return path for selected Module
     *
     * @return bool|string
     * @since 1.0
     */
    static public function getPath($module_name)
    {
        return MOLAJO_EXTENSIONS_MODULES . '/' . $module_name;
    }
}
