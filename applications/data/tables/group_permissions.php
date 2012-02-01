<?php
/**
 * @package     Molajo
 * @subpackage  Table
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Group Permissions
 *
 * @package     Molajo
 * @subpackage  Table
 * @since       1.0
 * @link
 */
class MolajoTableGroupPermissions extends MolajoTable
{
    /**
     * Constructor
     *
     * @param   object  Database object
     *
     * @return  MolajoTableGroup
     *
     * @since   1.0
     */
    public function __construct($db)
    {
        parent::__construct('#__group_permissions', 'id', $db);
    }

}
