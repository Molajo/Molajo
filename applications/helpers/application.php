<?php
/**
 * @package     Molajo
 * @subpackage  Helper
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Application
 *
 * @package     Molajo
 * @subpackage  Helper
 * @since       1.0
 */
class MolajoApplicationHelper
{
    /**
     * getApplicationInfo
     *
     * Retrieves Application info from database

     * @param  $application
     *
     * @return  boolean
     * @since   1.0
     */
    public static function getApplicationInfo()
    {
        $row = new stdClass();

        if (MOLAJO_APPLICATION == 'installation') {

            $id = 0;
            $row->id = 0;
            $row->name = MOLAJO_APPLICATION;
            $row->path = MOLAJO_APPLICATION;
            $row->asset_type_id = MOLAJO_ASSET_TYPE_BASE_APPLICATION;
            $row->description = '';
            $row->custom_fields = '';
            $row->parameters = '';
            $row->metadata = '';

        }  else {

            $db = Molajo::Application()->get('jdb', 'service');
            $query = $db->getQuery(true);

            $query->select($db->namequote('id'));
            $query->select($db->namequote('asset_type_id'));
            $query->select($db->namequote('name'));
            $query->select($db->namequote('path'));
            $query->select($db->namequote('description'));
            $query->select($db->namequote('custom_fields'));
            $query->select($db->namequote('parameters'));
            $query->select($db->namequote('metadata'));
            $query->from($db->namequote('#__applications'));
            $query->where($db->namequote('name').
                ' = '.$db->quote(MOLAJO_APPLICATION));
            $db->setQuery($query->__toString());
            $results = $db->loadObjectList();

            if ($db->getErrorNum()) {
                return new MolajoException($db->getErrorMsg());
            }

            if (count($results) == 0) {
                // todo: amy error;
            }

            foreach ($results as $result) {
                $row->id = $result->id;
                $id = $result->id;
                $row->name = $result->name;
                $row->path = $result->path;
                $row->asset_type_id = $result->asset_type_id;
                $row->description = $result->description;
                $row->custom_fields = $result->custom_fields;
                $row->parameters = $result->parameters;
                $row->metadata = $result->metadata;
            }
        }

        if (defined('MOLAJO_APPLICATION_ID')) {
        } else {
            define('MOLAJO_APPLICATION_ID', $id);
        }

        /** unsuccessful */
        return true;
    }
}
