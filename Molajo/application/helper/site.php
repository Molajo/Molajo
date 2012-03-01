<?php
/**
 * @package     Molajo
 * @subpackage  API
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
namespace Molajo\Application\Helper;

defined('MOLAJO') or die;

/**
 * Site
 *
 * @package     Molajo
 * @subpackage  Helper
 * @since       1.0
 */
class SiteHelper
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
        $m = new MolajoSitesModel ();
        $m->query->where($m->db->qn('id') . ' = ' . (int)SITE_ID);
        $results = $m->runQuery();
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
        $m = new MolajoSiteApplicationsModel();

        $m->query->select($m->db->qn('application_id'));
        $m->query->where($m->db->qn('site_id') . ' = ' . (int)SITE_ID);

        return $m->runQuery();
    }
}
