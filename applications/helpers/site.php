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

        $query->select($db->qn('id'));
        $query->select($db->qn('name'));
        $query->select($db->qn('description'));
        $query->select($db->qn('path'));
        $query->select($db->qn('parameters'));
        $query->select($db->qn('custom_fields'));
        $query->select($db->qn('metadata'));
        $query->select($db->qn('base_url'));
        $query->from($db->qn('#__sites'));
        $query->where($db->qn('id') . ' = ' . (int)SITE_ID);

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

        $query->select($db->qn('application_id'));
        $query->from($db->qn('#__site_applications'));
        $query->where($db->qn('site_id') . ' = ' . (int)SITE_ID);

        $db->setQuery($query->__toString());

        $results = $db->loadObjectList();

        if ($db->getErrorNum()) {
            return new MolajoException($db->getErrorMsg());
        }

        return $results;
    }
}
