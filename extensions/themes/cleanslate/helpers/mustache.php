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
class MolajoCleanslateMustacheHelper extends Mustache
{
    /**
     * hello
     *
     * Accesses User Object and returns a text message
     *
     * @results  object
     * @since    1.0
     */
    public function hello()
    {
        return 'Hello '.Services::User()->get('name').'!';
    }

    /**
     * dashboard
     *
     * Renders the Dashboard Module
     *
     * $results  text
     * $since    1.0
     */
    public function dashboard()
    {
        $rc = new MolajoModuleRenderer ('module', 'module');
        $attributes = array();
        $attributes['name'] = 'dashboard';
        $attributes['template'] = 'dashboard';
        $attributes['wrap'] = 'section';

        return $rc->process($attributes);
    }
}
