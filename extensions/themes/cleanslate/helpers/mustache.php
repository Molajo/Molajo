<?php
/**
 * @package     Molajo
 * @subpackage  Helper
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Mustache
 *
 * @package     Molajo
 * @subpackage  Helper
 * @since       1.0
 */
class MolajoCleanslateMustacheHelper extends MolajoMustacheHelper
{
    /**
     * hello
     *
     * Example demonstrating how to override the core helper
     *
     * @results  object
     * @since    1.0
     */
    public static function hello($context)
    {
     //   echo $context[0]->author;
//        return 'Hello1112';
        $rc = new MolajoModuleRenderer ('module', 'module');
        $attributes = array();
        $attributes['name'] = 'dashboard';
        $attributes['template'] = 'dashboard';
        $attributes['wrap'] = 'section';
        return $rc->process($attributes);

        //return array ('MolajoCleanslateMustacheHelper', 'Hello2');
    }

    /**
     * hello2
     *
     * Example demonstrating how to add new functions
     *
     * @results  object
     * @since    1.0
     */
    public static function hello2()
    {
        return 'Hello2';
    }
}
