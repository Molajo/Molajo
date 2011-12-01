<?php
/**
 * @package     Molajo
 * @subpackage  Menu
 * @copyright    Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('MOLAJO') or die;

/**
 * @package        Joomla.Administrator
 * @subpackage    online
 * * * @since        1.0
 */
abstract class modOnlineHelper
{
    /**
     * Count the number of users online.
     *
     * @return    mixed    The number of users online, or false on error.
     */
    public static function getOnlineCount()
    {
        $db = MolajoFactory::getDbo();
        $session = MolajoFactory::getSession();
        $sessionId = $session->getId();

        $query = $db->getQuery(true);
        $query->select('COUNT(a.session_id)');
        $query->from('#__sessions AS a');
        $query->where('a.session_id <> ' . $db->Quote($sessionId));
        $db->setQuery($query);
        $result = (int)$db->loadResult();
        if ($error = $db->getErrorMsg()) {
            JError::raiseWarning(500, $error);
            return false;
        }

        return $result;
    }
}
