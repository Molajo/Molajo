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
class MolajoMustacheHelper extends Mustache
{
    public $data = array();

    public function items() {
        return new ArrayIterator($this->data);
    }


    /**
     * hello
     *
     * Example demonstrating how functions like {{hello}}
     * interact with Mustache
     *
     * @results  object
     * @since    1.0
     */
    public function hello()
    {
        return 'Hello';
    }
}
