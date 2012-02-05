<?php
/**
 * @package     Molajo
 * @subpackage  Model
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Group Permissions
 *
 * @package     Molajo
 * @subpackage  Model
 * @since       1.0
 * @link
 */
class MolajoGroupPermissionsModel extends MolajoModel
{
    /**
     * Constructor
     *
     * @param   object  Database object
     *
     * @return  MolajoModelGroup
     *
     * @since   1.0
     */
    public function __construct($db)
    {
        parent::__construct('#__group_permissions', 'id', $db);
    }

}
