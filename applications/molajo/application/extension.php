<?php
/**
 * @package     Molajo
 * @subpackage  Application
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Extension
 *
 * Queries the extension tables for various rendered extension types
 *
 * MOLAJO_ASSET_TYPE_EXTENSION_BEGIN 1000
 * MOLAJO_ASSET_TYPE_EXTENSION_COMPONENT 1050
 * MOLAJO_ASSET_TYPE_EXTENSION_LANGUAGE 1100
 * MOLAJO_ASSET_TYPE_EXTENSION_LAYOUT 1150
 * MOLAJO_ASSET_TYPE_EXTENSION_MENU 1300
 * MOLAJO_ASSET_TYPE_EXTENSION_MODULE 1350
 * MOLAJO_ASSET_TYPE_EXTENSION_PLUGIN 1450
 * MOLAJO_ASSET_TYPE_EXTENSION_TEMPLATE 1500
 * MOLAJO_ASSET_TYPE_EXTENSION_END 1999
 *
 * @package     Molajo
 * @subpackage  Application
 * @since       1.0
 */
abstract class MolajoExtension
{
    /**
     * getExtensions
     *
     * Retrieves requested Extension
     *
     * @static
     * @param $asset_type_id
     * @param null $extension
     * @return bool|mixed
     */
    static public function getExtensions($asset_type_id, $extension = null)
    {
        $db = MolajoFactory::getDbo();
        $query = $db->getQuery(true);
        $date = MolajoFactory::getDate();
        $now = $date->toMySQL();
        $nullDate = $db->getNullDate();
        $acl = new MolajoACL ();

        /** fix and remove */
        $query->where('a.' . $db->namequote('name') . ' != "sef"');
        $query->where('a.' . $db->namequote('name') . ' != "joomla"');
        $query->where('a.' . $db->namequote('name') . ' != "example"');
        $query->where('a.' . $db->namequote('name') . ' != "system"');
        $query->where('a.' . $db->namequote('name') . ' != "webservices"');
        $query->where('a.' . $db->namequote('name') . ' != "broadcast"');
        $query->where('a.' . $db->namequote('name') . ' != "content"');
        $query->where('a.' . $db->namequote('name') . ' != "links"');
        $query->where('a.' . $db->namequote('name') . ' != "media"');
        $query->where('a.' . $db->namequote('name') . ' != "protect"');
        $query->where('a.' . $db->namequote('name') . ' != "responses"');
        $query->where('a.' . $db->namequote('name') . ' != "broadcast"');

        /** Extensions */
        $query->select('a.' . $db->namequote('id') . ' as extension_id');
        $query->select('a.' . $db->namequote('name') . ' as extension_name');
        $query->select('a.' . $db->namequote('element'));
        $query->select('a.' . $db->namequote('folder'));

        $query->from($db->namequote('#__extensions') . ' as a');

        $query->where('a.' . $db->namequote('asset_type_id') . ' = ' . (int)$asset_type_id);

        /** Extension Instances */
        $query->select('b.' . $db->namequote('id') . ' as extension_instance_id');
        $query->select('b.' . $db->namequote('title'));
        $query->select('b.' . $db->namequote('subtitle'));
        $query->select('b.' . $db->namequote('alias'));
        $query->select('b.' . $db->namequote('content_text'));
        $query->select('b.' . $db->namequote('protected'));
        $query->select('b.' . $db->namequote('featured'));
        $query->select('b.' . $db->namequote('stickied'));
        $query->select('b.' . $db->namequote('status'));
        $query->select('b.' . $db->namequote('custom_fields'));
        $query->select('b.' . $db->namequote('parameters'));
        $query->select('b.' . $db->namequote('position'));
        $query->select('b.' . $db->namequote('ordering'));
        $query->select('b.' . $db->namequote('language'));

        $query->from($db->namequote('#__extension_instances') . ' as b');

        if ($extension == null) {
        } else {
            $query->where('(b.' . $db->namequote('title') . ' = ' . $db->quote($extension) .
                          ' OR ' . 'b.' . $db->namequote('id') . ' = ' . (int)$extension . ')');
        }

        $query->where('a.' . $db->namequote('id') . ' = b.' . $db->namequote('extension_id'));
        $query->where('b.' . $db->namequote('asset_type_id') . ' = ' . (int)$asset_type_id);

        $query->where('b.' . $db->namequote('status') . ' = ' . MOLAJO_STATUS_PUBLISHED);
        $query->where('(b.start_publishing_datetime = ' . $db->Quote($nullDate) . ' OR b.start_publishing_datetime <= ' . $db->Quote($now) . ')');
        $query->where('(b.stop_publishing_datetime = ' . $db->Quote($nullDate) . ' OR b.stop_publishing_datetime >= ' . $db->Quote($now) . ')');

        $query->from($db->namequote('#__assets') . ' as b_assets');
        $query->from($db->namequote('#__asset_types') . ' as b_ctype');
        $query->where('b_assets.asset_type_id = b_ctype.id');
        $query->where('b_ctype.' . $db->namequote('source_table') . ' = "__extension_instances"');
        $query->where('b_assets.source_id = b.id');

        /** Extension Instance ACL */
        $acl->getQueryInformation('', $query, 'viewaccess', array('table_prefix' => 'b_assets'));

        /** Extension Instance Options */
        if ($asset_type_id == MOLAJO_ASSET_TYPE_EXTENSION_MENU) {
            $query->select('c.' . $db->namequote('id') . ' as id');
            $query->select('c.' . $db->namequote('asset_type_id') . ' as asset_type_id');
            $query->select('c.' . $db->namequote('title') . ' as menu_item_title');
            $query->select('c.' . $db->namequote('subtitle') . ' as menu_item_subtitle');
            $query->select('c.' . $db->namequote('alias') . ' as menu_item_alias');
            $query->select('c.' . $db->namequote('content_text') . ' as menu_item_content_text');
            $query->select('c.' . $db->namequote('protected') . ' as menu_item_protected');
            $query->select('c.' . $db->namequote('featured') . ' as menu_item_featured');
            $query->select('c.' . $db->namequote('stickied') . ' as menu_item_stickied');
            $query->select('c.' . $db->namequote('status') . ' as menu_item_status');
            $query->select('c.' . $db->namequote('custom_fields') . ' as menu_item_custom_fields');
            $query->select('c.' . $db->namequote('parameters') . ' as menu_item_parameters');
            $query->select('c.' . $db->namequote('ordering') . ' as menu_item_ordering');
            $query->select('c.' . $db->namequote('home') . ' as menu_item_home');
            $query->select('c.' . $db->namequote('parent_id') . ' as menu_item_parent_id');
            $query->select('c.' . $db->namequote('lft') . ' as menu_item_lft');
            $query->select('c.' . $db->namequote('rgt') . ' as menu_item_rgt');
            $query->select('c.' . $db->namequote('lvl') . ' as menu_item_lvl');
            $query->select('c.' . $db->namequote('metadata') . ' as menu_item_metadata');
            $query->select('c.' . $db->namequote('language') . ' as menu_item_language');

            $query->from($db->namequote('#__content') . ' as c');

            $query->where('b.' . $db->namequote('id') . ' = c.' . $db->namequote('extension_instance_id'));

            $query->where('c.' . $db->namequote('status') . ' = ' . MOLAJO_STATUS_PUBLISHED);
            $query->where('(c.start_publishing_datetime = ' . $db->Quote($nullDate) . ' OR c.start_publishing_datetime <= ' . $db->Quote($now) . ')');
            $query->where('(c.stop_publishing_datetime = ' . $db->Quote($nullDate) . ' OR c.stop_publishing_datetime >= ' . $db->Quote($now) . ')');

            $query->select('c_assets.' . $db->namequote('sef_request'));
            $query->select('c_assets.' . $db->namequote('request'));
            $query->select('c_assets.' . $db->namequote('template_id') . ' as menu_item_template_id');

            $query->from($db->namequote('#__assets') . ' as c_assets');
            $query->from($db->namequote('#__asset_types') . ' as c_ctype');
            $query->where('c_assets.asset_type_id = c_ctype.id');
            $query->where('c_ctype.' . $db->namequote('source_table') . ' = "__content"');
            $query->where('c_assets.source_id = c.id');

            /** Menu Item ACL */
            $acl->getQueryInformation('', $query, 'viewaccess', array('table_prefix' => 'c_assets'));

            $query->order('b.title, c.lft');
        }

        /** Assigned to the Application */
        $query->from($db->namequote('#__application_extension_instances') . ' as d');
        $query->where('b.' . $db->namequote('id') . ' = d.' . $db->namequote('extension_instance_id'));
        $query->where('d.' . $db->namequote('application_id') . ' = ' . MOLAJO_APPLICATION_ID);

        /** Assigned to the Site */
        $query->from($db->namequote('#__site_extension_instances') . ' as e');
        $query->where('b.' . $db->namequote('id') . ' = e.' . $db->namequote('extension_instance_id'));
        $query->where('e.' . $db->namequote('site_id') . ' = ' . MOLAJO_SITE_ID);

        $db->setQuery($query->__toString());

        if ($asset_type_id == MOLAJO_ASSET_TYPE_EXTENSION_MENU) {
            $extensions = $db->loadObjectList('id');
        } else {
            $extensions = $db->loadObjectList();
        }

        if ($error = $db->getErrorMsg()) {
            MolajoError::raiseWarning(500, $error);
            return false;
        }

        return $extensions;
    }
}