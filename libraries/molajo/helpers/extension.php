<?php
/**
 * @package     Molajo
 * @subpackage  Extension Helper
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Extension Helper
 *
 * Queries the extension tables for various rendered extension types
 *
 * MOLAJO_EXTENSION_TYPE_CORE 0
 * MOLAJO_EXTENSION_TYPE_COMPONENTS 1
 * MOLAJO_EXTENSION_TYPE_LANGUAGES 2
 * MOLAJO_EXTENSION_TYPE_LAYOUTS 3
 * MOLAJO_EXTENSION_TYPE_LIBRARIES 10
 * MOLAJO_EXTENSION_TYPE_MANIFESTS 4
 * MOLAJO_EXTENSION_TYPE_MENUS 5
 * MOLAJO_EXTENSION_TYPE_MODULES 6
 * MOLAJO_EXTENSION_TYPE_PARAMETERS 7
 * MOLAJO_EXTENSION_TYPE_PLUGINS 8
 * MOLAJO_EXTENSION_TYPE_TEMPLATES 9
 *
 * @package     Molajo
 * @subpackage  Extension Helper
 * @since       1.0
 */
abstract class MolajoExtensionHelper
{
    /**
     * getExtensions
     *
     * Retrieves requested Extension
     *
     * @static
     * @param $extension_type_id
     * @param null $extension
     * @return bool|mixed
     */
    static public function getExtensions($extension_type_id, $extension = null)
    {
        $db = MolajoFactory::getDbo();
        $query = $db->getQuery(true);
        $date = MolajoFactory::getDate();
        $now = $date->toMySQL();
        $nullDate = $db->getNullDate();
        $acl = new MolajoACL ();

 /** fix and remove */
        $query->where('a.'.$db->namequote('name').' != "sef"');
        $query->where('a.'.$db->namequote('name').' != "joomla"');
        $query->where('a.'.$db->namequote('name').' != "example"');
        $query->where('a.'.$db->namequote('name').' != "system"');
        $query->where('a.'.$db->namequote('name').' != "webservices"');
        $query->where('a.'.$db->namequote('name').' != "broadcast"');
        $query->where('a.'.$db->namequote('name').' != "content"');
        $query->where('a.'.$db->namequote('name').' != "links"');
        $query->where('a.'.$db->namequote('name').' != "media"');
        $query->where('a.'.$db->namequote('name').' != "protect"');
        $query->where('a.'.$db->namequote('name').' != "responses"');
        $query->where('a.'.$db->namequote('name').' != "broadcast"');


        /** Extensions */
        $query->select('a.'.$db->namequote('id').' as extension_id');
        $query->select('a.'.$db->namequote('name'));
        $query->select('a.'.$db->namequote('element'));
        $query->select('a.'.$db->namequote('folder'));
        
        $query->from($db->namequote('#__extensions').' as a');
        
        $query->where('a.'.$db->namequote('extension_type_id').' = '. (int) $extension_type_id);

        /** Extension Instances */
        $query->select('b.'.$db->namequote('id').' as extension_instance_id');
        $query->select('b.'.$db->namequote('title'));
        $query->select('b.'.$db->namequote('subtitle'));
        $query->select('b.'.$db->namequote('alias'));   
        $query->select('b.'.$db->namequote('protected'));       
        $query->select('b.'.$db->namequote('featured'));        
        $query->select('b.'.$db->namequote('stickied')); 
        $query->select('b.'.$db->namequote('status'));                
        $query->select('b.'.$db->namequote('custom_fields'));
        $query->select('b.'.$db->namequote('parameters'));
        $query->select('b.'.$db->namequote('ordering'));

        if ($extension_type_id == MOLAJO_EXTENSION_TYPE_MODULES) { 
            $query->select('b.'.$db->namequote('position'));
            $query->select('b.'.$db->namequote('content_text'));
        }

        $query->from($db->namequote('#__extension_instances').' as b');

        if ($extension == null) {
        } else {
            $query->where('(b.'.$db->namequote('title').' = '.$db->quote($extension).
                          ' OR ' . 'b.'.$db->namequote('id') . ' = ' . (int)$extension . ')');
        }

        $query->where('a.'.$db->namequote('id').' = b.'.$db->namequote('extension_id'));
        $query->where('b.'.$db->namequote('extension_type_id').' = '. (int) $extension_type_id);
        $query->where('b.'.$db->namequote('status').' = '.MOLAJO_STATUS_PUBLISHED);
        $query->where('(b.start_publishing_datetime = '.$db->Quote($nullDate).' OR b.start_publishing_datetime <= '.$db->Quote($now) . ')');
        $query->where('(b.stop_publishing_datetime = '.$db->Quote($nullDate).' OR b.stop_publishing_datetime >= '.$db->Quote($now) . ')');

        /** Extension Instance ACL */
        $query->from($db->namequote('#__assets').' as b_assets');
        $query->from($db->namequote('#__source_tables').' as b_source');
        $query->where('b_assets.source_table_id = b_source.id');
        $query->where('b_source.'.$db->namequote('source_table').' = "__extension_instances"');
        $query->where('b_assets.source_id = b.id');

        $acl->getQueryInformation('', $query, 'viewaccess', array('table_prefix' => 'b_assets'));

        /** Extension Instance Options */
        if ($extension_type_id == MOLAJO_EXTENSION_TYPE_MENUS) {
            $query->select('c.'.$db->namequote('id').' as extension_instance_option_id');
            $query->select('c.'.$db->namequote('title').' as menu_item_title');
            $query->select('c.'.$db->namequote('subtitle').' as menu_item_subtitle');
            $query->select('c.'.$db->namequote('alias').' as menu_item_alias');
            $query->select('c.'.$db->namequote('protected').' as menu_item_protected');
            $query->select('c.'.$db->namequote('featured').' as menu_item_featured');
            $query->select('c.'.$db->namequote('stickied').' as menu_item_stickied');
            $query->select('c.'.$db->namequote('status').' as menu_item_status');
            $query->select('c.'.$db->namequote('custom_fields').' as menu_item_custom_fields');
            $query->select('c.'.$db->namequote('parameters').' as menu_item_parameters');
            $query->select('c.'.$db->namequote('ordering').' as menu_item_ordering');
            $query->select('c.'.$db->namequote('menu_item_type').' as menu_item_type');
            $query->select('c.'.$db->namequote('parent_id').' as menu_item_parent_id');
            $query->select('c.'.$db->namequote('level').' as menu_item_level');
            $query->select('c.'.$db->namequote('lft').' as menu_item_lft');
            $query->select('c.'.$db->namequote('rgt').' as menu_item_rgt');
            $query->select('c.'.$db->namequote('image').' as menu_item_image');

            $query->from($db->namequote('#__extension_instance_options').' as c');

            $query->where('a.'.$db->namequote('id').' = c.'.$db->namequote('extension_id'));
            $query->where('b.'.$db->namequote('id').' = c.'.$db->namequote('extension_instance_id'));
            $query->where('c.'.$db->namequote('extension_type_id').' = '. (int) $extension_type_id);
            $query->where('c.'.$db->namequote('status').' = '.MOLAJO_STATUS_PUBLISHED);
            $query->where('(c.start_publishing_datetime = '.$db->Quote($nullDate).' OR c.start_publishing_datetime <= '.$db->Quote($now) . ')');
            $query->where('(c.stop_publishing_datetime = '.$db->Quote($nullDate).' OR c.stop_publishing_datetime >= '.$db->Quote($now) . ')');

            /** Extension Instance ACL */
            $query->select('c_assets.'.$db->namequote('sef_request'));
            $query->select('c_assets.'.$db->namequote('request'));
            $query->from($db->namequote('#__assets').' as c_assets');
            $query->from($db->namequote('#__source_tables').' as c_source');
            $query->where('c_assets.source_table_id = c_source.id');
            $query->where('c_source.'.$db->namequote('source_table').' = "__extension_instance_options"');
            $query->where('c_assets.source_id = c.id');
            $acl->getQueryInformation('', $query, 'viewaccess', array('table_prefix' => 'c_assets'));

            $query->order('b.title, c.lft');
        }

        /** Assigned to the Application */
        $query->from($db->namequote('#__application_extension_instances').' as d');
        $query->where('b.'.$db->namequote('id') .' = d.'.$db->namequote('extension_instance_id'));
        $query->where('d.'.$db->namequote('application_id').' = '.MOLAJO_APPLICATION_ID);

        /** Assigned to the Site */
        $query->from($db->namequote('#__site_extension_instances').' as e');
        $query->where('b.'.$db->namequote('id') .' = e.'.$db->namequote('extension_instance_id'));
        $query->where('e.'.$db->namequote('site_id').' = '.MOLAJO_SITE_ID);

        $db->setQuery($query->__toString());
        $extensions = $db->loadObjectList();

        if ($error = $db->getErrorMsg()) {
            MolajoError::raiseWarning(500, $error);
            return false;
        }

        return $extensions;
    }
}