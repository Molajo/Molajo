<?php
/**
 * @package     Molajo
 * @subpackage  Helper
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Menuitem
 *
 * @package     Molajo
 * @subpackage  Helper
 * @since       1.0
 */
abstract class MolajoMenuitemHelper
{
    /**
     * get
     *
     * Retrieves Menu item data and verifies access for the extension instances
     * user, application and site
     *
     * @param   $asset_type_id
     * @param   $extension
     *
     * @return  bool|mixed
     * @since   1.0
     */
    public static function get($menu_item_id)
    {
        $db = Services::DB();
        $query = $db->getQuery(true);
        $now = Services::Date()->getDate()->toSql();
        $nullDate = $db->getNullDate();

        /**
         *  a. Content Table
         *      Menu Items
         */
        $query->select('a.' . $db->nq('id') . ' as menu_item_id');
        $query->select('a.' . $db->nq('asset_type_id') . ' as menu_item_asset_type_id');
        $query->select('a.' . $db->nq('title') . ' as menu_item_title');
        $query->select('a.' . $db->nq('custom_fields') . ' as menu_item_custom_fields');
        $query->select('a.' . $db->nq('parameters') . ' as menu_item_parameters');
        $query->select('a.' . $db->nq('metadata') . ' as menu_item_metadata');
        $query->select('a.' . $db->nq('translation_of_id') . ' as menu_item_translation_of_id');
        $query->select('a.' . $db->nq('language') . ' as menu_item_language');

        $query->select('a_assets.' . $db->nq('id') . ' as menu_item_asset_id');
        $query->select('a_assets.' . $db->nq('view_group_id') . ' as menu_item_view_group_id');

        $query->from($db->nq('#__content') . ' as a');

        $query->where('a.' . $db->nq('extension_instance_id') .
            ' = b.' . $db->nq('id'));
        $query->where('a.' . $db->nq('id') . ' = ' . (int)$menu_item_id);

        /** Assets Join and View Access Check */
        Services::Access()
            ->setQueryViewAccess(
            $query,
            array('join_to_prefix' => 'a',
                'join_to_primary_key' => 'id',
                'asset_prefix' => 'a_assets',
                'select' => false
            )
        );

        /**
         *  b. Extensions Instances Table
         */
        $query->select('b.' . $db->nq('id') . ' as menu_id');
        $query->select('b.' . $db->nq('asset_type_id'). 'as menu_asset_type_id');
        $query->select('b.' . $db->nq('title'). ' as menu_title');
        $query->select('b.' . $db->nq('parameters'). 'as menu_parameters');
        $query->select('b.' . $db->nq('metadata'). 'as menu_metadata');

        $query->select('b_assets.' . $db->nq('id') . ' as menu_asset_id');
        $query->select('b_assets.' . $db->nq('view_group_id') . ' as menu_view_group_id');

        $query->from($db->nq('#__extension_instances') . ' as b');

        $query->where('b.' . $db->nq('status') . ' = ' . MOLAJO_STATUS_PUBLISHED);
        $query->where('(b.start_publishing_datetime = ' .
                $db->q($nullDate) .
                ' OR b.start_publishing_datetime <= ' . $db->q($now) . ')'
        );
        $query->where('(b.stop_publishing_datetime = ' .
                $db->q($nullDate) .
                ' OR b.stop_publishing_datetime >= ' . $db->q($now) . ')'
        );

        Services::Access()
            ->setQueryViewAccess(
            $query,
            array('join_to_prefix' => 'b',
                'join_to_primary_key' => 'id',
                'asset_prefix' => 'b_assets',
                'select' => false
            )
        );

        /**
         *  c. Application Table
         *      Extension Instances must be enabled for the Application
         */
        $query->from($db->nq('#__application_extension_instances') .
            ' as c');
        $query->where('c.' . $db->nq('extension_instance_id') .
            ' = b.' . $db->nq('id'));
        $query->where('c.' . $db->nq('application_id') .
            ' = ' . MOLAJO_APPLICATION_ID);

        /**
         *  d. Site Table
         *      Extension Instances must be enabled for the Site
         */
        $query->from($db->nq('#__site_extension_instances') .
            ' as d');
        $query->where('d.' . $db->nq('extension_instance_id') .
            ' = b.' . $db->nq('id'));
        $query->where('d.' . $db->nq('site_id') .
            ' = ' . SITE_ID);

        /**
         *  Run Query
         */
        $db->setQuery($query->__toString());
        $menuitems = $db->loadObjectList();

        if ($error = $db->getErrorMsg()) {
            MolajoError::raiseWarning(500, $error);
            return false;
        }

        $menuitem = array();
        foreach ($menuitems as $menuitem) {}
        return $menuitem;
    }
}
