<?php
/**
 * @package     Molajo
 * @subpackage  Helper
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Component
 *
 * @package     Molajo
 * @subpackage  Component
 * @since       1.0
 */
abstract class MolajoComponentHelper
{
    /**
     * get
     *
     * Get the component data of a specific type if no specific component is specified
     * otherwise only the specific component data is returned.
     *
     * @return  mixed  An array of component data objects, or a component data object.
     * @since   1.0
     */
    static public function get($component_name)
    {
        $rows = MolajoExtensionHelper::get(MOLAJO_ASSET_TYPE_EXTENSION_COMPONENT, $component_name);
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
     * Return path for selected Component
     *
     * @return bool|string
     * @since 1.0
     */
    static public function getPath($component_name)
    {
        return MOLAJO_EXTENSIONS_COMPONENTS . '/' . $component_name;
    }
}
