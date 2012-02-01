<?php
/**
 * @package     Molajo
 * @subpackage  Table
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Model Table Class
 *
 * @package     Molajo
 * @subpackage  Table
 * @since       1.0
 */
abstract class MolajoTableModel extends MolajoTable
{
    /**
     * check
     *
     * Edit data to ensure correctness before storing in database
     *
     * @return  boolean  True if the instance is sane and able to be stored in the database.
     * @since   1.0
     */
    public function check()
    {
        return true;
    }
}
