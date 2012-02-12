<?php
/**
 * @package     Molajo
 * @subpackage  Helper
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Site
 *
 * @package     Molajo
 * @subpackage  Helper
 * @since       1.0
 */
class MolajoSiteHelper
{
    /**
     * get
     *
     * Retrieves Site info from database
     *
     * @return  array
     * @since   1.0
     */
    public static function get()
    {
        $db = Services::DB();
        $query = $db->getQuery(true);
        $now = Services::Date()->getDate()->toSql();
        $nullDate = $db->getNullDate();

        $query->select($db->quoteName('id'));
        $query->select($db->quoteName('name'));
        $query->select($db->quoteName('description'));
        $query->select($db->quoteName('path'));
        $query->select($db->quoteName('parameters'));
        $query->select($db->quoteName('custom_fields'));
        $query->select($db->quoteName('metadata'));
        $query->select($db->quoteName('base_url'));
        $query->from($db->quoteName('#__sites'));
        $query->where($db->quoteName('id') . ' = ' . (int)MOLAJO_SITE_ID);

        $db->setQuery($query->__toString());

        $results = $db->loadObjectList();

        if ($db->getErrorNum()) {
            return new MolajoException($db->getErrorMsg());
        }

        foreach ($results as $result)
        {
        }
        return $result;
    }

    /**
     * getApplications
     *
     * Retrieves Applications for which the site is authorized to see
     *
     * @return  array
     * @since   1.0
     */
    public static function getApplications()
    {
        $db = Services::DB();
        $query = $db->getQuery(true);
        $now = Services::Date()->getDate()->toSql();
        $nullDate = $db->getNullDate();

        $query->select($db->quoteName('application_id'));
        $query->from($db->quoteName('#__site_applications'));
        $query->where($db->quoteName('site_id') . ' = ' . (int)MOLAJO_SITE_ID);

        $db->setQuery($query->__toString());

        $results = $db->loadObjectList();

        if ($db->getErrorNum()) {
            return new MolajoException($db->getErrorMsg());
        }

        return $results;
    }
}
