<?php
/**
 * @package     Molajo
 * @subpackage  API
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * API
 *
 * @package     Molajo
 * @subpackage  Helper
 * @since       1.0
 */
abstract class MolajoAPIHelper
{
    /**
     * getAPI
     *
     * Retrieve a list of classes and methods
     *
     * @results  object
     * @since    1.0
     */
    public static function get()
    {
        $cmethods = get_class_methods('MolajoFileService');
        $exclude = array();
        $methods = array();
        foreach($cmethods as $value){
            echo $value.'<br />';
            if(in_array($value, $exclude)) {
            } else {
                $methods[] = $value;
            }
        }
        return $methods;
    }
}
