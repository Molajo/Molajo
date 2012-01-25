<?php
/**
 * @package     Molajo
 * @subpackage  Extension
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Extension
 *
 * Various queries for extension support
 *
 * MOLAJO_ASSET_TYPE_EXTENSION_BEGIN 1000
 *
 * MOLAJO_ASSET_TYPE_EXTENSION_COMPONENT 1050
 * MOLAJO_ASSET_TYPE_EXTENSION_LANGUAGE 1100
 * MOLAJO_ASSET_TYPE_EXTENSION_VIEW 1150
 * MOLAJO_ASSET_TYPE_EXTENSION_MENU 1300
 * MOLAJO_ASSET_TYPE_EXTENSION_MODULE 1350
 * MOLAJO_ASSET_TYPE_EXTENSION_PLUGIN 1450
 * MOLAJO_ASSET_TYPE_EXTENSION_TEMPLATE 1500
 *
 * MOLAJO_ASSET_TYPE_EXTENSION_END 1999
 *
 * @package     Molajo
 * @subpackage  Extensions
 * @since       1.0
 */
abstract class MolajoExtensionHelper
{
    /**
     * get
     *
     * Retrieves Extension data from the extension and extension instances
     * In the case of menu items, joins to content table
     * Adds ACL View Access data to query
     * Adds verification for site and application access
     *
     * @static
     * @param $asset_type_id
     * @param null $extension
     * @param null $extension_type
     * @return bool|mixed
     */
    public static function get($asset_type_id, $extension = null, $extension_type = null)
    {
        $db = MolajoController::getDbo();
        $query = $db->getQuery(true);
        $date = MolajoController::getDate();
        $now = $date->toMySQL();
        $nullDate = $db->getNullDate();
        $acl = new MolajoACL ();
        if ($asset_type_id == MOLAJO_ASSET_TYPE_EXTENSION_POSITION) {
            $queryAssetTypeID = MOLAJO_ASSET_TYPE_EXTENSION_MODULE;
        } else {
            $queryAssetTypeID = $asset_type_id;
        }

        /**
         *  Extensions Table
         */
        $query->select('a.' . $db->namequote('id') . ' as extension_id');
        $query->select('a.' . $db->namequote('name') . ' as extension_name');
        $query->select('a.' . $db->namequote('subtype'));

        $query->from($db->namequote('#__extensions') . ' as a');
        $query->where('a.' . $db->namequote('asset_type_id') . ' = ' . (int)$queryAssetTypeID);

        /** plugins and views have subtypes */
        if ($extension_type == null) {
        } else {
            $query->where('(a.' . $db->namequote('subtype') . ' = ' . $db->quote($extension_type) . ')');
        }

        /** Extension Instances Table */
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
        $query->select('b.' . $db->namequote('metadata'));
        $query->select('b.' . $db->namequote('position'));
        $query->select('b.' . $db->namequote('ordering'));
        $query->select('b.' . $db->namequote('language'));

        $query->from($db->namequote('#__extension_instances') . ' as b');

        $query->where('a.' . $db->namequote('id') . ' = b.' . $db->namequote('extension_id'));
        $query->where('b.' . $db->namequote('asset_type_id') . ' = ' . (int)$queryAssetTypeID);

        if ($asset_type_id == MOLAJO_ASSET_TYPE_EXTENSION_POSITION) {
            $query->where('b.' . $db->namequote('position') . ' = ' . $db->quote($extension));
            $query->order('b.' . $db->namequote('ordering'));
        } else if ($extension == null) {
        } else {
            $query->where('(b.' . $db->namequote('title') . ' = ' . $db->quote($extension) .
                ' OR ' . 'b.' . $db->namequote('id') . ' = ' . (int)$extension . ')');
        }

        $query->where('b.' . $db->namequote('status') . ' = ' . MOLAJO_STATUS_PUBLISHED);
        $query->where('(b.start_publishing_datetime = ' . $db->Quote($nullDate) . ' OR b.start_publishing_datetime <= ' . $db->Quote($now) . ')');
        $query->where('(b.stop_publishing_datetime = ' . $db->Quote($nullDate) . ' OR b.stop_publishing_datetime >= ' . $db->Quote($now) . ')');

        $query->select('b_assets.' . $db->namequote('id') . ' as extension_instance_asset_id');
        $query->select('b_assets.' . $db->namequote('view_group_id') . ' as extension_instance_view_group_id');
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
            $query->select('c_assets.' . $db->namequote('page_id') . ' as menu_item_page_id');

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
/**
if ($asset_type_id == MOLAJO_ASSET_TYPE_EXTENSION_POSITION) {
    if ($extension == 'footer') {
        echo $query->__toString();
    }
}
*/
        $extensions = $db->loadObjectList();

        if ($error = $db->getErrorMsg()) {
            MolajoError::raiseWarning(500, $error);
            return false;
        }

        return $extensions;
    }

    /**
     * getInstanceTitle
     *
     * Retrieves Extension Name, given the extension_instance_id
     *
     * @static
     * @param   $extension_instance_id
     *
     * @return  bool|mixed
     * @since   1.0
     */
    public static function getInstanceTitle($extension_instance_id)
    {
        $db = MolajoController::getDbo();
        $query = $db->getQuery(true);
        $date = MolajoController::getDate();
        $now = $date->toMySQL();
        $nullDate = $db->getNullDate();
        $acl = new MolajoACL ();

        $query->select('a.' . $db->namequote('title'));
        $query->from($db->namequote('#__extension_instances') . ' as a');
        $query->where('a.' . $db->namequote('id') . ' = ' . (int)$extension_instance_id);

        $query->where('a.' . $db->namequote('status') . ' = ' . MOLAJO_STATUS_PUBLISHED);
        $query->where('(a.start_publishing_datetime = ' . $db->Quote($nullDate) . ' OR a.start_publishing_datetime <= ' . $db->Quote($now) . ')');
        $query->where('(a.stop_publishing_datetime = ' . $db->Quote($nullDate) . ' OR a.stop_publishing_datetime >= ' . $db->Quote($now) . ')');

        /** assets */
        $query->from($db->namequote('#__assets') . ' as b_assets');
        $acl->getQueryInformation('', $query, 'viewaccess', array('table_prefix' => 'b_assets'));

        $db->setQuery($query->__toString());
        $name = $db->loadResult();

        if ($error = $db->getErrorMsg()) {
            MolajoError::raiseWarning(500, $error);
            return false;
        }

        return $name;
    }

    /**
     * NOT USED
     *
     * getOptions
     *
     * Construct the Request Array for the MVC
     *
     * @return bool
     */
    public static function getOptions(JObject $request)
    {
        $request->set('controller', '');
        $request->set('model', '');
        $request->set('plugin_type', '');
        $request->set('component_table', '');

        /** Configuration model */
        $configModel = new MolajoModelConfiguration ($request->get('mvc_option'));

        /** Task */
        if ($request->get('task', '') == '') {
            $request->get('task', 'display');
        }

        /** Controller (while validating Task) */
        $request->get('controller', $configModel->getOptionLiteralValue(MOLAJO_EXTENSION_OPTION_ID_TASKS_CONTROLLER, $request->get('task')));

        if ($request->get('controller') === false) {
            MolajoError::raiseError(500, MolajoTextHelper::_('MOLAJO_INVALID_TASK_CONTROLLER') . ' ' . $request->get('task'));
            $request->set('results', false);
            return $request;
        }

        /** IDs */
        if ((int)$request->get('id', 0) == 0) {
            $request->set('id', 0);
        }
        if (is_array($request->get('ids'))) {
        } else {
            $request->get('ids', array());
        }
        if ((int)$request->get('category', 0) == 0) {
            $request->set('category', 0);
        }

        if ($request->get('task') == 'add') {

            if ((int)$request->get('id') == 0
                && count($request->get('ids')) == 0
            ) {
            } else {
                MolajoError::raiseError(500, MolajoTextHelper::_('MOLAJO_ERROR_ADD_TASK_MUST_NOT_HAVE_ID'));
                $request->set('results', false);
                return $request;
            }

        } else if ($request->get('task') == 'edit'
            || $request->get('task') == 'restore'
        ) {

            if ($request->get('id') > 0
                && count($request->get('ids')) == 0
            ) {

            } else if ((int)$request->get('id') == 0
                && count($request->get('ids')) == 1
            ) {
                $request->get('id', (int)$request->get('ids'));
                $request->get('ids', array());

            } else if ((int)$request->get('id') == 0
                && count($request->get('ids')) == 0
            ) {
                MolajoError::raiseError(500, MolajoTextHelper::_('MOLAJO_ERROR_EDIT_TASK_MUST_HAVE_ID'));
                $request->set('results', false);
                return $request;

            } else if (count($request->get('ids')) > 1) {
                MolajoError::raiseError(500, MolajoTextHelper::_('MOLAJO_ERROR_TASK_MAY_NOT_HAVE_MULTIPLE_IDS'));
                $request->set('results', false);
                return $request;
            }
        }

        /** Model */
        $model = '';
        if ($request->get('controller') == 'display') {
            if ($request->get('static') === true) {
                $model = 'dummy';
            } else {
                $model = 'display';
            }
        } else {
            $model = 'edit';
        }
        if ($model == 'dummy') {
            $request->set('model', 'MolajoModel');
        } else {
            $request->set('model', ucfirst($request->get('mvc_option')) . 'Model' . ucfirst($model));
        }

        if ($request->get('controller') == 'display') {

            /** View **/
            if ($request->get('static') === true) {
                $option = 3300;

            } else if ($request->get('id') > 0) {
                if ($request->get('task') == 'display') {
                    /** item */
                    $option = 3110;
                } else {
                    /** edit */
                    $option = 3310;
                }
            } else {
                /** items */
                $option = 3210;
            }

            if ($request->get('view') == '') {
                $results = false;
            } else {
                $results = $configModel->getOptionLiteralValue($option, $request->get('view'));
            }

            $option = $option + 10;
            /* todo: amy fix/remove if ($results === false) {
                            $request->get('view', $configModel->getOptionValue($option);
                            if ($request->get('view') === false) {
                                MolajoController::getApplication()->setMessage(MolajoTextHelper::_('MOLAJO_NO_VIEWS_DEFAULT_DEFINED'), 'error');
                                $request->get('results', false;
                                return $request;
                            }
                        }
            **/
            /** Wrap **/
            $option = $option + 10;
            if ($request->get('wrap', '') == '') {
                $results = false;
            } else {
                $results = $configModel->getOptionLiteralValue($option, $request->get('wrap'));
            }

            $option = $option + 10;
            if ($results === false) {
                $request->set('wrap', $configModel->getOptionValue($option));
                if ($request->get('wrap', '') == '') {
                    $request->set('wrap', 'none');
                }
            }

            /** Page **/
            $option = $option + 10;
            if ($request->get('page', '') == '') {
                $results = false;
            } else {
                $results = $configModel->getOptionLiteralValue($option, $request->get('page'));
            }

            $option = $option + 10;
            if ($results === false) {
                $request->set('page', $configModel->getOptionValue($option));
                if ($request->get('page', '') == '') {
                    $request->set('page', 'default');
                }
            }
        }
        /** todo: amy: come back and get redirect */

        /** Component table */
        $request->set('component_table', $configModel->getOptionValue(MOLAJO_EXTENSION_OPTION_ID_TABLE));
        if ($request->get('component_table', '') == '') {
            $request->set('component_table', '__content');
        }

        /** Plugin helper */
        $request->set('plugin_type', $configModel->getOptionValue(MOLAJO_EXTENSION_OPTION_ID_PLUGIN_TYPE));
        if ($request->get('plugin_type', '') == '') {
            $request->set('plugin_type', 'content');
        }

        $request->get('results', true);

        return $request;
    }
}
