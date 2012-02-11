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
        $cmethods = get_class_methods(__CLASS__);
        var_dump($cmethods);
                $exclude = array('ddd');
                $methods = array();
                foreach($cmethods as $value){
                    if(!in_array($value, $exclude)) {
                        $methods[] = $value;
                    }
                }
                return $methods;
    }
}
