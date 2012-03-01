<?php
/**
 * @package     Molajo
 * @subpackage  Helper
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Theme
 *
 * @package     Molajo
 * @subpackage  Helper
 * @since       1.0
 */
ClassCleanslateThemeHelper extends MolajoThemeHelper
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
}
