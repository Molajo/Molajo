<?php
/**
 * @package     Molajo
 * @subpackage  Site Helper
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * MolajoSiteHelper
 *
 * @package     Molajo
 * @subpackage  Site Helper
 * @since       1.0
 */
class MolajoSiteHelper
{
    /**
     * @var null $_sites
     *
     * @since 1.0
     */
    protected static $_sites = null;

    /**
     * getSiteInfo
     *
     * Retrieves Site info from database
     *
     * This method will return a site information array if called
     * with no arguments which can be used to add custom site information.
     *
     * @param   integer  $id        A site identifier, can be ID or Name
     * @param   boolean  $byName    If True, find the site by its name
     *
     * @return  boolean  True if the information is added. False on error
     * @since   1.0
     */
    public static function getSiteInfo($id = null)
    {
        if ($id == null) {
            $id = MOLAJO_SITE_ID;
        }

        if (self::$_sites === null) {

            $obj = new stdClass();

            $db = MolajoController::getDbo();

            $query = $db->getQuery(true);

            $query->select('id');
            $query->select('name');
            $query->select('description');
            $query->select('path');
            $query->select('parameters');
            $query->select('custom_fields');
            $query->select('base_url');
            $query->from($db->namequote('#__sites'));

            $db->setQuery($query->__toString());

            $results = $db->loadObjectList();

            if ($db->getErrorNum()) {
                return new MolajoException($db->getErrorMsg());
            }

            foreach ($results as $result) {

                $obj->id = $result->id;
                $obj->name = $result->name;
                $obj->description = $result->description;
                $obj->path = $result->path;
                $obj->parameters = $result->parameters;
                $obj->custom_fields = $result->custom_fields;
                $obj->base_url = $result->base_url;

                self::$_sites[$result->id] = clone $obj;
            }
        }

        if (isset(self::$_sites[$id])) {
            return self::$_sites[$id];
        }

        return null;
    }

    /**
     * getSiteApplications
     *
     * Retrieves Applications for which the site is authorized to see
     *
     * @param   integer  $id        A site id
     *
     * @return  array
     * @since   1.0
     */
    public static function getSiteApplications($id = null)
    {
        $db = MolajoController::getDbo();
        $query = $db->getQuery(true);

        if ($id == null) {
            $id = MOLAJO_SITE_ID;
        }

        $query->select('application_id');
        $query->from($db->namequote('#__site_applications'));
        $query->where($db->namequote('site_id') . ' = ' . (int) $id);

        $db->setQuery($query->__toString());

        $results = $db->loadObjectList();

        if ($db->getErrorNum()) {
            return new MolajoException($db->getErrorMsg());
        }

        return $results;
    }
}