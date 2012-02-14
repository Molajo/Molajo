<?php
/**
 * @package     Molajo
 * @subpackage  Helper
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Model
 *
 * @package     Molajo
 * @subpackage  Helper
 * @since       1.0
 */
class MolajoModelHelper
{
    /**
     * validateCheckedOut
     *
     * Verify that the row has been checked out for update by the user
     *
     * @return  boolean  True if checked out to user
     * @since   1.0
     */
    public function validateCheckedOut($table)
    {
        if ($table->checked_out == Services::User()->get('id')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * validateAlias
     *
     * Verify that the alias is unique for this component
     *
     * @return  boolean
     * @since   1.0
     */
    public function validateAlias($table)
    {

    }

    /**
     * validateDates
     *
     * Verify and set defaults for dates
     *
     * @return  boolean
     * @since   1.0
     */
    public function validateDates($table)
    {

    }

    /**
     * validateLanguage
     *
     * Verify language setting
     *
     * @return  boolean
     * @since   1.0
     */
    public function validateLanguage($table)
    {

    }


}
