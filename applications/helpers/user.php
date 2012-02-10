<?php
/**
 * @package     Molajo
 * @subpackage  Helper
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * User
 *
 * @package     Molajo
 * @subpackage  Helper
 * @since       1.0
 */
abstract class MolajoUserHelper
{
    /**
     * getUserInstanceID
     *
     * Retrieves User ID given the ID or Username
     *
     * @param   $id
     *
     * @return  bool|mixed
     * @since   1.0
     */
    public static function getUserInstanceID($id)
    {
        if ((int)$id == 0 && trim($id)) {
            return false;
        }
        $db = Molajo::Services()->connect('jdb');
        $query = $db->getQuery(true);
        $date = Molajo::Services()->connect('Date');
        $now = $date->toMySQL();
        $nullDate = $db->getNullDate();

        /**
         *  Users Table
         */
        $query->select('a.' . $db->namequote('id') . ' as extension_instance_id');
        $query->from($db->namequote('#__users') . ' as a');
        $query->where('(a.' . $db->namequote('id') . ' = ' . (int)$id .
            ' OR a.username = ' . $db->quote($id).')');

        /**
         *  Run Query
         */
        $db->setQuery($query->__toString());
        $userid = $db->loadResult();

        if ($error = $db->getErrorMsg()) {
            MolajoError::raiseWarning(500, $error);
            return false;
        }

        return $userid;
    }
}

