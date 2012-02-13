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
 * @subpackage  Helper
 * @since       1.0
 */
abstract class MolajoContentModelHelper
{

    /**
     * checkedOut
     *
     * Verify that the row has been checked out for update by the user
     *
     * @return  boolean  True if checked out to user
     * @since   1.0
     */
    public function checkedOut($table)
    {
        if ($table->checked_out == Services::User()->get('id')) {
            return true;
        } else {
            return false;
        }
    }
}
