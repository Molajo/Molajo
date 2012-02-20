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
        $m = new MolajoDisplayModel ();

        /**
         *  a. Content Table
         *      Menu Items
         */
        $m->query->select('a.' . $m->db->qn('id') . ' as menu_item_id');
        $m->query->select('a.' . $m->db->qn('asset_type_id') . ' as menu_item_asset_type_id');
        $m->query->select('a.' . $m->db->qn('title') . ' as menu_item_title');
        $m->query->select('a.' . $m->db->qn('custom_fields') . ' as menu_item_custom_fields');
        $m->query->select('a.' . $m->db->qn('parameters') . ' as menu_item_parameters');
        $m->query->select('a.' . $m->db->qn('metadata') . ' as menu_item_metadata');
        $m->query->select('a.' . $m->db->qn('translation_of_id') . ' as menu_item_translation_of_id');
        $m->query->select('a.' . $m->db->qn('language') . ' as menu_item_language');

        $m->query->select('a_assets.' . $m->db->qn('id') . ' as menu_item_asset_id');
        $m->query->select('a_assets.' . $m->db->qn('view_group_id') . ' as menu_item_view_group_id');

        $m->query->from($m->db->qn('#__content') . ' as a');

        $m->query->where('a.' . $m->db->qn('extension_instance_id') .
            ' = b.' . $m->db->qn('id'));
        $m->query->where('a.' . $m->db->qn('id') . ' = ' . (int)$menu_item_id);

        /** Assets Join and View Access Check */
        Services::Access()
            ->setQueryViewAccess(
            $m->query,
            array('join_to_prefix' => 'a',
                'join_to_primary_key' => 'id',
                'asset_prefix' => 'a_assets',
                'select' => false
            )
        );

        /**
         *  b. Extensions Instances Table
         */
        $m->query->select('b.' . $m->db->qn('id') . ' as menu_id');
        $m->query->select('b.' . $m->db->qn('asset_type_id'). 'as menu_asset_type_id');
        $m->query->select('b.' . $m->db->qn('title'). ' as menu_title');
        $m->query->select('b.' . $m->db->qn('parameters'). 'as menu_parameters');
        $m->query->select('b.' . $m->db->qn('metadata'). 'as menu_metadata');

        $m->query->select('b_assets.' . $m->db->qn('id') . ' as menu_asset_id');
        $m->query->select('b_assets.' . $m->db->qn('view_group_id') . ' as menu_view_group_id');

        $m->query->from($m->db->qn('#__extension_instances') . ' as b');

        $m->query->where('b.' . $m->db->qn('status') . ' = ' . MOLAJO_STATUS_PUBLISHED);
        $m->query->where('(b.start_publishing_datetime = ' .
                $m->db->q($m->nullDate) .
                ' OR b.start_publishing_datetime <= ' . $m->db->q($m->now) . ')'
        );
        $m->query->where('(b.stop_publishing_datetime = ' .
                $m->db->q($m->nullDate) .
                ' OR b.stop_publishing_datetime >= ' . $m->db->q($m->now) . ')'
        );

        Services::Access()
            ->setQueryViewAccess(
            $m->query,
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
        $m->query->from($m->db->qn('#__application_extension_instances') . ' as c');
        $m->query->where('c.' . $m->db->qn('extension_instance_id') .
            ' = b.' . $m->db->qn('id'));
        $m->query->where('c.' . $m->db->qn('application_id') .
            ' = ' . MOLAJO_APPLICATION_ID);

        /**
         *  d. Site Table
         *      Extension Instances must be enabled for the Site
         */
        $m->query->from($m->db->qn('#__site_extension_instances') .' as d');
        $m->query->where('d.' . $m->db->qn('extension_instance_id') .
            ' = b.' . $m->db->qn('id'));
        $m->query->where('d.' . $m->db->qn('site_id') .
            ' = ' . SITE_ID);

        /**
         *  Run Query
         */
        $menuitems = $m->runQuery();
        $menuitem = array();
        foreach ($menuitems as $menuitem) {}
        return $menuitem;
    }
}
