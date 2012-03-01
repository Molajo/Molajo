<?php
/**
 * @package     Molajo
 * @subpackage  Helper
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
namespace Molajo\Application\Helper;

defined('MOLAJO') or die;

/**
 * User
 *
 * @package     Molajo
 * @subpackage  Helper
 * @since       1.0
 */
abstract class UserHelper
{
    /**
     * getId
     *
     * Retrieves User ID given the ID or Username
     *
     * @param   $id
     *
     * @return  bool|mixed
     * @since   1.0
     */
    public static function getId($id)
    {
        $m = new UsersModel();
        $m->query->where('(' . $m->db->qn('id') . ' = ' . (int)$id .
            ' OR ' . $m->db->qn('username') . ' = ' . $m->db->q($id) . ')');
        return $m->loadResult();
    }
}

