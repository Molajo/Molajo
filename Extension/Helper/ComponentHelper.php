<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
namespace Molajo\Extension\Helper;

use Molajo\Services;

defined('MOLAJO') or die;

/**
 * Component
 *
 * @package   Molajo
 * @subpackage  Component
 * @since       1.0
 */
abstract class ComponentHelper
{
    /**
     * get
     *
     * @return  array
     * @since   1.0
     */
    static public function get($name)
    {
        $row = ExtensionHelper::get(
            CATALOG_TYPE_EXTENSION_COMPONENT,
            $name
        );
        if (count($row) == 0) {
            return array();
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
    static public function getPath($name)
    {
        return EXTENSIONS_COMPONENTS . '/' . $name;
    }
}
