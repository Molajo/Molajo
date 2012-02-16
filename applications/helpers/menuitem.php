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
        $query->select('a.' . $db->namequote('id') . ' as menu_item_id');
        $query->select('a.' . $db->namequote('asset_type_id') . ' as menu_item_asset_type_id');
        $query->select('a.' . $db->namequote('title') . ' as menu_item_title');
        $query->select('a.' . $db->namequote('custom_fields') . ' as menu_item_custom_fields');
        $query->select('a.' . $db->namequote('parameters') . ' as menu_item_parameters');
        $query->select('a.' . $db->namequote('metadata') . ' as menu_item_metadata');
        $query->select('a.' . $db->namequote('translation_of_id') . ' as menu_item_translation_of_id');
        $query->select('a.' . $db->namequote('language') . ' as menu_item_language');

        $query->select('a_assets.' . $db->namequote('id') . ' as menu_item_asset_id');
        $query->select('a_assets.' . $db->namequote('view_group_id') . ' as menu_item_view_group_id');

        $query->from($db->namequote('#__content') . ' as a');

        $query->where('a.' . $db->namequote('extension_instance_id') .
            ' = b.' . $db->namequote('id'));
        $query->where('a.' . $db->namequote('id') . ' = ' . (int)$menu_item_id);

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
        $query->select('b.' . $db->namequote('id') . ' as menu_id');
        $query->select('b.' . $db->namequote('asset_type_id'). 'as menu_asset_type_id');
        $query->select('b.' . $db->namequote('title'). ' as menu_title');
        $query->select('b.' . $db->namequote('parameters'). 'as menu_parameters');
        $query->select('b.' . $db->namequote('metadata'). 'as menu_metadata');

        $query->select('b_assets.' . $db->namequote('id') . ' as menu_asset_id');
        $query->select('b_assets.' . $db->namequote('view_group_id') . ' as menu_view_group_id');

        $query->from($db->namequote('#__extension_instances') . ' as b');

        $query->where('b.' . $db->namequote('status') . ' = ' . MOLAJO_STATUS_PUBLISHED);
        $query->where('(b.start_publishing_datetime = ' .
                $db->quote($nullDate) .
                ' OR b.start_publishing_datetime <= ' . $db->quote($now) . ')'
        );
        $query->where('(b.stop_publishing_datetime = ' .
                $db->quote($nullDate) .
                ' OR b.stop_publishing_datetime >= ' . $db->quote($now) . ')'
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
        $query->from($db->namequote('#__application_extension_instances') .
            ' as c');
        $query->where('c.' . $db->namequote('extension_instance_id') .
            ' = b.' . $db->namequote('id'));
        $query->where('c.' . $db->namequote('application_id') .
            ' = ' . MOLAJO_APPLICATION_ID);

        /**
         *  d. Site Table
         *      Extension Instances must be enabled for the Site
         */
        $query->from($db->namequote('#__site_extension_instances') .
            ' as d');
        $query->where('d.' . $db->namequote('extension_instance_id') .
            ' = b.' . $db->namequote('id'));
        $query->where('d.' . $db->namequote('site_id') .
            ' = ' . MOLAJO_SITE_ID);

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
