<?php
/**
 * @package     Molajo
 * @subpackage  Helper
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Menu
 *
 * @package     Molajo
 * @subpackage  Extensions
 * @since       1.0
 */
abstract class MolajoMenuHelper
{
    /**
     * get
     *
     * Retrieves Menu data from the Menu and Menu instances
     * In the case of menu items, joins to content table
     * Adds ACL View Access data to query
     * Adds verification for site and application access
     *
     * @static
     * @param   $asset_type_id
     * @param   $Menu
     * @param   $subtype
     *
     * @return  bool|mixed
     * @since   1.0
     */
    public static function get($asset_type_id, $Menu = null, $subtype = null)
    {
        $db = MolajoController::getDbo();
        $query = $db->getQuery(true);
        $date = MolajoController::getDate();
        $now = $date->toMySQL();
        $nullDate = $db->getNullDate();

        $acl = new MolajoACL ();
        if ($asset_type_id == MOLAJO_ASSET_TYPE_MENU_POSITION) {
            $queryAssetTypeID = MOLAJO_ASSET_TYPE_MENU_MODULE;
        } else {
            $queryAssetTypeID = $asset_type_id;
        }

        /**
         *  a. Menus Table
         *      Plugins and Views have folders which are defined in the subtype of an Menu
         */
        $query->select('a.' . $db->namequote('id') . ' as menu_id');
        $query->select('a.' . $db->namequote('name') . ' as menu_name');
        $query->select('a.' . $db->namequote('subtype'));

        $query->from($db->namequote('#__content') . ' as a');
        $query->where('a.' . $db->namequote('asset_type_id') . ' = ' . (int)$queryAssetTypeID);

        /** plugins and views have subtypes */
        if ($subtype == null) {
        } else {
            $query->where('(a.' . $db->namequote('subtype') . ' = ' . $db->quote($subtype) . ')');
        }

        /**
         *  b. Menus Instances Table
         *      Primary content for an Menu use
         */
        $query->select('b.' . $db->namequote('id') . ' as Menu_instance_id');
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
        $query->select('b.' . $db->namequote('ordering'));
        $query->select('b.' . $db->namequote('language'));

        $query->from($db->namequote('#__extension_instances') . ' as b');

        $query->where('a.' . $db->namequote('id') . ' = b.' . $db->namequote('Menu_id'));
        $query->where('b.' . $db->namequote('asset_type_id') . ' = ' . (int)$queryAssetTypeID);

        $query->where('b.' . $db->namequote('status') . ' = ' . MOLAJO_STATUS_PUBLISHED);
        $query->where('(b.start_publishing_datetime = ' . $db->Quote($nullDate) . ' OR b.start_publishing_datetime <= ' . $db->Quote($now) . ')');
        $query->where('(b.stop_publishing_datetime = ' . $db->Quote($nullDate) . ' OR b.stop_publishing_datetime >= ' . $db->Quote($now) . ')');

        $query->select('b_assets.' . $db->namequote('id') . ' as Menu_instance_asset_id');
        $query->select('b_assets.' . $db->namequote('view_group_id') . ' as Menu_instance_view_group_id');
        $query->from($db->namequote('#__assets') . ' as b_assets');
        $query->from($db->namequote('#__asset_types') . ' as b_ctype');
        $query->where('b_assets.asset_type_id = b_ctype.id');
        $query->where('b_ctype.' . $db->namequote('source_table') . ' = "__Menu_instances"');
        $query->where('b_assets.source_id = b.id');

        /** Menu Instance ACL */
        $acl->getQueryInformation('', $query, 'viewaccess', array('table_prefix' => 'b_assets'));

        /**
         *  c. Content Table for Menu Items
         */
        if ($asset_type_id == MOLAJO_ASSET_TYPE_Menu_MENU) {
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

            $query->where('b.' . $db->namequote('id') . ' = c.' . $db->namequote('Menu_instance_id'));

            $query->where('c.' . $db->namequote('status') . ' = ' . MOLAJO_STATUS_PUBLISHED);
            $query->where('(c.start_publishing_datetime = ' . $db->Quote($nullDate) . ' OR c.start_publishing_datetime <= ' . $db->Quote($now) . ')');
            $query->where('(c.stop_publishing_datetime = ' . $db->Quote($nullDate) . ' OR c.stop_publishing_datetime >= ' . $db->Quote($now) . ')');

            $query->select('c_assets.' . $db->namequote('sef_request'));
            $query->select('c_assets.' . $db->namequote('request'));
            $query->select('c_assets.' . $db->namequote('theme_id') . ' as menu_item_theme_id');
            $query->select('c_assets.' . $db->namequote('view_page_id') . ' as menu_item_view_page_id');

            $query->from($db->namequote('#__assets') . ' as c_assets');
            $query->from($db->namequote('#__asset_types') . ' as c_ctype');
            $query->where('c_assets.asset_type_id = c_ctype.id');
            $query->where('c_ctype.' . $db->namequote('source_table') . ' = "__content"');
            $query->where('c_assets.source_id = c.id');

            /** Menu Item ACL */
            $acl->getQueryInformation('', $query, 'viewaccess', array('table_prefix' => 'c_assets'));

            $query->order('b.title, c.lft');
        }

        /**
         *  d. Content Table for Tags
         *      <include:tag name=tag-name ... />
         */
        if ($asset_type_id == MOLAJO_ASSET_TYPE_CATEGORY_TAG) {
            $query->select('d.' . $db->namequote('id') . ' as tag_id');
            $query->select('d.' . $db->namequote('asset_type_id') . ' as tag_asset_type_id');
            $query->select('d.' . $db->namequote('title') . ' as tag_title');
            $query->select('d.' . $db->namequote('subtitle') . ' as tag_subtitle');
            $query->select('d.' . $db->namequote('alias') . ' as tag_alias');
            $query->select('d.' . $db->namequote('content_text') . ' as tag_content_text');
            $query->select('d.' . $db->namequote('protected') . ' as tag_protected');
            $query->select('d.' . $db->namequote('featured') . ' as tag_featured');
            $query->select('d.' . $db->namequote('stickied') . ' as tag_stickied');
            $query->select('d.' . $db->namequote('status') . ' as tag_status');
            $query->select('d.' . $db->namequote('custom_fields') . ' as tag_custom_fields');
            $query->select('d.' . $db->namequote('parameters') . ' as tag_parameters');
            $query->select('d.' . $db->namequote('ordering') . ' as tag_ordering');
            $query->select('d.' . $db->namequote('home') . ' as tag_home');
            $query->select('d.' . $db->namequote('parent_id') . ' as tag_parent_id');
            $query->select('d.' . $db->namequote('lft') . ' as tag_lft');
            $query->select('d.' . $db->namequote('rgt') . ' as tag_rgt');
            $query->select('d.' . $db->namequote('lvl') . ' as tag_lvl');
            $query->select('d.' . $db->namequote('metadata') . ' as tag_metadata');
            $query->select('d.' . $db->namequote('language') . ' as tag_language');

            $query->from($db->namequote('#__content') . ' as d');

            $query->where('b.' . $db->namequote('id') . ' = d.' . $db->namequote('Menu_instance_id'));

            $query->where('d.' . $db->namequote('status') . ' = ' . MOLAJO_STATUS_PUBLISHED);
            $query->where('(d.start_publishing_datetime = ' . $db->Quote($nullDate) . ' OR d.start_publishing_datetime <= ' . $db->Quote($now) . ')');
            $query->where('(d.stop_publishing_datetime = ' . $db->Quote($nullDate) . ' OR d.stop_publishing_datetime >= ' . $db->Quote($now) . ')');

            $query->from($db->namequote('#__assets') . ' as d_assets');
            $query->where('d_assets.source_id = d.id');

            /** Menu Item ACL */
            $acl->getQueryInformation('', $query, 'viewaccess', array('table_prefix' => 'd_assets'));
        }

        /**
         *  e. Application Table
         *      Menu Instances must be enabled for the Application
         */
        $query->from($db->namequote('#__application_Menu_instances') . ' as e');
        $query->where('b.' . $db->namequote('id') . ' = e.' . $db->namequote('Menu_instance_id'));
        $query->where('e.' . $db->namequote('application_id') . ' = ' . MOLAJO_APPLICATION_ID);

        /**
         *  f. Site Table
         *      Menu Instances must be enabled for the Site
         */
        $query->from($db->namequote('#__site_Menu_instances') . ' as e');
        $query->where('b.' . $db->namequote('id') . ' = e.' . $db->namequote('Menu_instance_id'));
        $query->where('e.' . $db->namequote('site_id') . ' = ' . MOLAJO_SITE_ID);

        $db->setQuery($query->__toString());
        /**
        if ($asset_type_id == MOLAJO_ASSET_TYPE_Menu_POSITION) {
        if ($Menu == 'footer') {
        echo $query->__toString();
        }
        }
         */
        $Menus = $db->loadObjectList();

        if ($error = $db->getErrorMsg()) {
            MolajoError::raiseWarning(500, $error);
            return false;
        }

        return $Menus;
    }

    /**
     * getInstanceID
     *
     * Retrieves Menu ID, given title
     *
     * @static
     * @param  $title
     * @param  $asset_type_id
     * @param  $subtype
     *
     * @return  bool|mixed
     * @since   1.0
     */
    public static function getInstanceID($asset_type_id, $title, $subtype = null)
    {
        $db = MolajoController::getDbo();
        $query = $db->getQuery(true);
        $date = MolajoController::getDate();
        $now = $date->toMySQL();
        $nullDate = $db->getNullDate();
        $acl = new MolajoACL ();

        $query->select('a.' . $db->namequote('id'));
        $query->from($db->namequote('#__Menu_instances') . ' as a');
        $query->where('a.' . $db->namequote('title') . ' = ' . $db->quote($title));
        $query->where('a.' . $db->namequote('asset_type_id') . ' = ' . (int)$asset_type_id);

        $query->where('a.' . $db->namequote('status') . ' = ' . MOLAJO_STATUS_PUBLISHED);
        $query->where('(a.start_publishing_datetime = ' . $db->Quote($nullDate) . ' OR a.start_publishing_datetime <= ' . $db->Quote($now) . ')');
        $query->where('(a.stop_publishing_datetime = ' . $db->Quote($nullDate) . ' OR a.stop_publishing_datetime >= ' . $db->Quote($now) . ')');

        /** plugins and views have subtypes */
        if ($subtype == null) {
        } else {
            $query->where('(a.' . $db->namequote('subtype') . ' = ' . $db->quote($subtype) . ')');
        }

        /** assets */
        $query->from($db->namequote('#__assets') . ' as b_assets');
        $acl->getQueryInformation('', $query, 'viewaccess', array('table_prefix' => 'b_assets'));

        $db->setQuery($query->__toString());
        $id = $db->loadResult();

        if ($error = $db->getErrorMsg()) {
            MolajoError::raiseWarning(500, $error);
            return false;
        }

        return $id;
    }

    /**
     * getInstanceTitle
     *
     * Retrieves Menu Name, given the Menu_instance_id
     *
     * @static
     * @param   $Menu_instance_id
     *
     * @return  bool|mixed
     * @since   1.0
     */
    public static function getInstanceTitle($Menu_instance_id)
    {
        $db = MolajoController::getDbo();
        $query = $db->getQuery(true);
        $date = MolajoController::getDate();
        $now = $date->toMySQL();
        $nullDate = $db->getNullDate();
        $acl = new MolajoACL ();

        $query->select('a.' . $db->namequote('title'));
        $query->from($db->namequote('#__Menu_instances') . ' as a');
        $query->where('a.' . $db->namequote('id') . ' = ' . (int)$Menu_instance_id);

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
     * _getMenu
     *
     * Retrieve Component information using either the ID
     *
     * @return bool
     * @since 1.0
     */
    static public function getMenuRequestObject($request)
    {
        $row = MolajoComponentHelper::get((int)$request->get('Menu_instance_id'));
        if (count($row) == 0) {
            return false;
        }

        $request->set('Menu_instance_name', $row->title);
        $request->set('Menu_asset_type_id', MOLAJO_ASSET_TYPE_Menu_COMPONENT);
        $request->set('Menu_asset_id', $row->Menu_instance_asset_id);
        $request->set('Menu_view_group_id', $row->Menu_instance_view_group_id);
        $request->set('Menu_path', MolajoComponentHelper::getPath(strtolower($row->title)));
        $request->set('Menu_type', 'component');

        $custom_fields = new JRegistry;
        $custom_fields->loadString($row->custom_fields);
        $request->set('category_custom_fields', $custom_fields);

        $metadata = new JRegistry;
        $metadata->loadString($row->metadata);
        $request->set('category_metadata', $metadata);

        $parameters = new JRegistry;
        $parameters->loadString($row->parameters);
        $request->set('Menu_parameters', $parameters);

        /** mvc */
        if ($request->get('mvc_controller', '') == '') {
            $request->set('mvc_controller', $parameters->def('controller', ''));
        }
        if ($request->get('mvc_task', '') == '') {
            $request->set('mvc_task', $parameters->def('task', 'display'));
        }
        if ($request->get('Menu_instance_name', '') == '') {
            $request->set('Menu_instance_name', $parameters->def('option', 0));
        }
        if ($request->get('mvc_model', '') == '') {
            $request->set('mvc_model', $parameters->def('model', ''));
        }
        if ((int)$request->get('mvc_id', 0) == 0) {
            $request->set('mvc_id', $parameters->def('id', 0));
        }
        if ((int)$request->get('mvc_category_id', 0) == 0) {
            $request->set('mvc_category_id', $parameters->def('category_id', 0));
        }
        if ((int)$request->get('mvc_suppress_no_results', 0) == 0) {
            $request->set('mvc_suppress_no_results', $parameters->def('suppress_no_results', 0));
        }

        $request->set('Menu_event_type', $parameters->def('plugin_type', array('content')));

        if ($request->get('request_suppress_no_results', '') == '') {
            $request->set('request_suppress_no_results', $parameters->def('suppress_no_results'));
        }

        $request->set('Menu_folder',
            MolajoComponentHelper::getPath($request->get('Menu_instance_name')));

        return $request;
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
        $configModel = new MolajoModelConfiguration ($request->get('Menu_instance_name'));

        /** Task */
        if ($request->get('task', '') == '') {
            $request->get('task', 'display');
        }

        /** Controller (while validating Task) */
        $request->get('controller', $configModel->getOptionLiteralValue(MOLAJO_Menu_OPTION_ID_TASKS_CONTROLLER, $request->get('task')));

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
                $model = 'static';
            } else {
                $model = 'display';
            }
        } else {
            $model = 'edit';
        }
        if ($model == 'static') {
            $request->set('model', 'MolajoModel');
        } else {
            $request->set('model', ucfirst($request->get('Menu_instance_name')) . 'Model' . ucfirst($model));
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
        $request->set('component_table', $configModel->getOptionValue(MOLAJO_Menu_OPTION_ID_TABLE));
        if ($request->get('component_table', '') == '') {
            $request->set('component_table', '__content');
        }

        /** Plugin helper */
        $request->set('plugin_type', $configModel->getOptionValue(MOLAJO_Menu_OPTION_ID_PLUGIN_TYPE));
        if ($request->get('plugin_type', '') == '') {
            $request->set('plugin_type', 'content');
        }

        $request->get('results', true);

        return $request;
    }
}
