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
 * MOLAJO_EXTENSION_TYPE_COMPONENT 1
 * MOLAJO_EXTENSION_TYPE_LANGUAGES 2
 * MOLAJO_EXTENSION_TYPE_LAYOUTS 3
 * MOLAJO_EXTENSION_TYPE_LIBRARIES 10
 * MOLAJO_EXTENSION_TYPE_MANIFESTS 4
 * MOLAJO_EXTENSION_TYPE_MENU 5
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
     * _load
     *
     * Loads the published plugins.
     *
     * @static
     * @return bool|mixed
     */
    static public function getExtensions($extension_type_id, $specificExtension = null)
    {
        $db = MolajoFactory::getDbo();
        $query = $db->getQuery(true);
        $date = MolajoFactory::getDate();
        $now = $date->toMySQL();
        $nullDate = $db->getNullDate();

        $query->select('a.'.$db->namequote('id'));
        $query->select('a.'.$db->namequote('title'));
        $query->select('a.'.$db->namequote('subtitle'));
        $query->select('a.'.$db->namequote('custom_fields'));
        $query->select('a.'.$db->namequote('parameters'));
        $query->select('a.'.$db->namequote('status'));
        $query->select('a.'.$db->namequote('enabled'));

        $query->select('b.'.$db->namequote('name'));
        $query->select('b.'.$db->namequote('folder').' as type');

        if ($extension_type_id == 4) {      // MOLAJO_EXTENSION_TYPE_MENU
            $query->select('a.'.$db->namequote('menu_item_parent_id'));
            $query->select('a.'.$db->namequote('menu_item_level'));
            $query->select('a.'.$db->namequote('menu_item_type'));
            $query->select('a.'.$db->namequote('menu_item_extension_id'));
            $query->select('a.'.$db->namequote('menu_item_template_id'));
            $query->select('a.'.$db->namequote('menu_item_link_target'));
            $query->select('a.'.$db->namequote('menu_item_lft'));
            $query->select('a.'.$db->namequote('menu_item_rgt'));
            $query->select('a.'.$db->namequote('menu_item_home'));
            $query->select('a.'.$db->namequote('menu_item_path'));
            $query->select('a.'.$db->namequote('menu_item_link'));
        }

        if ($extension_type_id == 6) {      // MOLAJO_EXTENSION_TYPE_MODULES
            $query->select('a.'.$db->namequote('position'));
            $query->select('a.'.$db->namequote('content_text'));
        }

        $query->from($db->namequote('#__extension_instances').' as a');
        $query->from($db->namequote('#__extensions').' as b');
        $query->from($db->namequote('#__application_extensions').' as c');
        $query->from($db->namequote('#__assets').' as assets');
        $query->from($db->namequote('#__source_tables').' as source');

        $query->where('a.'.$db->namequote('extension_type_id').' = '. (int)$extension_type_id);

        $query->where('a.'.$db->namequote('status').' = '.MOLAJO_STATUS_PUBLISHED);
        $query->where('(a.start_publishing_datetime = '.$db->Quote($nullDate).' OR a.start_publishing_datetime <= '.$db->Quote($now) . ')');
        $query->where('(a.stop_publishing_datetime = '.$db->Quote($nullDate).' OR a.stop_publishing_datetime >= '.$db->Quote($now) . ')');

        $query->where('b.'.$db->namequote('id') .' = a.'.$db->namequote('extension_id'));

        $query->where('c.'.$db->namequote('application_id').' = '.MOLAJO_APPLICATION_ID);
        $query->where('c.'.$db->namequote('extension_id').' = b.'.$db->namequote('id'));
        $query->where('c.'.$db->namequote('extension_instance_id').' = a.'.$db->namequote('id'));

        if ($specificExtension == null) {
        } else {
            $query->where('(a.'.$db->namequote('title').' = '.$db->quote($specificExtension).' OR ' . 'a.'.$db->namequote('id') . ' = ' . (int)$specificExtension . ')');
        }

        $query->where('b.'.$db->namequote('name').' != "sef"');
        $query->where('b.'.$db->namequote('name').' != "joomla"');
        $query->where('b.'.$db->namequote('name').' != "example"');
        $query->where('b.'.$db->namequote('name').' != "system"');
        $query->where('b.'.$db->namequote('name').' != "webservices"');
        $query->where('b.'.$db->namequote('name').' != "broadcast"');
        $query->where('b.'.$db->namequote('name').' != "content"');
        $query->where('b.'.$db->namequote('name').' != "links"');
        $query->where('b.'.$db->namequote('name').' != "media"');
        $query->where('b.'.$db->namequote('name').' != "protect"');
        $query->where('b.'.$db->namequote('name').' != "responses"');
        $query->where('b.'.$db->namequote('name').' != "broadcast"');

        $query->where('source.'.$db->namequote('source_table').' = "__extension_instances"');
        $query->where('assets.source_id = a.id');

        $acl = new MolajoACL ();
        $acl->getQueryInformation('', $query, 'viewaccess', array('table_prefix' => 'assets'));

        $db->setQuery($query->__toString());
        $extensions = $db->loadObjectList();

        if ($error = $db->getErrorMsg()) {
            MolajoError::raiseWarning(500, $error);
            return false;
        }

        return $extensions;
    }
}