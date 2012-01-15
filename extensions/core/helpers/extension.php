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
     * getExtensions
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

        /** fix and remove */
        if ($asset_type_id == MOLAJO_ASSET_TYPE_EXTENSION_PLUGIN) {

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
        }

        /**
         *  Extensions Table
         */
        $query->select('a.' . $db->namequote('id') . ' as extension_id');
        $query->select('a.' . $db->namequote('name') . ' as extension_name');
        $query->select('a.' . $db->namequote('element'));
        $query->select('a.' . $db->namequote('folder'));

        $query->from($db->namequote('#__extensions') . ' as a');

        if ($asset_type_id == MOLAJO_ASSET_TYPE_EXTENSION_POSITION) {
            $query->where('a.' . $db->namequote('asset_type_id') . ' = ' . (int)$asset_type_id - 1);
        } else {
            $query->where('a.' . $db->namequote('asset_type_id') . ' = ' . (int)$asset_type_id);
        }
        /** plugins and views have folders */
        if ($extension_type == null) {
        } else {
            $query->where('(a.' . $db->namequote('folder') . ' = ' . $db->quote($extension_type) . ')');
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

        if ($extension == null) {
        } else {
            $query->where('(b.' . $db->namequote('title') . ' = ' . $db->quote($extension) .
                ' OR ' . 'b.' . $db->namequote('id') . ' = ' . (int)$extension . ')');
        }

        $query->where('a.' . $db->namequote('id') . ' = b.' . $db->namequote('extension_id'));

        if ($asset_type_id == MOLAJO_ASSET_TYPE_EXTENSION_POSITION) {
            $query->where('b.' . $db->namequote('asset_type_id') . ' = ' . (int)$asset_type_id - 1);
            $query->where('b.' . $db->namequote('position') . ' = ' . $db->quote($extension));
            $query->order('b.' . $db->namequote('ordering'));
        } else {
            $query->where('b.' . $db->namequote('asset_type_id') . ' = ' . (int)$asset_type_id);
        }

        $query->where('b.' . $db->namequote('status') . ' = ' . MOLAJO_STATUS_PUBLISHED);
        $query->where('(b.start_publishing_datetime = ' . $db->Quote($nullDate) . ' OR b.start_publishing_datetime <= ' . $db->Quote($now) . ')');
        $query->where('(b.stop_publishing_datetime = ' . $db->Quote($nullDate) . ' OR b.stop_publishing_datetime >= ' . $db->Quote($now) . ')');

        $query->select('b_assets.' . $db->namequote('id') . ' as extension_instance_asset_id');
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
            $query->select('c_assets.' . $db->namequote('template_page') . ' as menu_item_template_page');

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
        //echo '<pre>';var_dump($extensions);'</pre>';

        return $extensions;
    }

    /**
     * getAsset
     *
     * Function to retrieve asset information for the Request or Asset ID
     *
     * @static
     * @param $request
     * @return bool
     * @since 1.0
     */
    public static function getAsset(JObject $request)
    {
        $db = MolajoController::getDbo();
        $query = $db->getQuery(true);

        $query->select('a.' . $db->nameQuote('id') . ' as asset_id');
        $query->select('a.' . $db->nameQuote('asset_type_id'));
        $query->select('a.' . $db->nameQuote('source_id'));
        $query->select('a.' . $db->nameQuote('sef_request'));
        $query->select('a.' . $db->nameQuote('request'));
        $query->select('a.' . $db->nameQuote('primary_category_id'));
        $query->select('a.' . $db->nameQuote('template_id'));
        $query->select('a.' . $db->nameQuote('template_page'));
        $query->select('a.' . $db->nameQuote('language'));
        $query->select('a.' . $db->nameQuote('translation_of_id'));
        $query->select('a.' . $db->nameQuote('redirect_to_id'));
        $query->select('a.' . $db->nameQuote('view_group_id'));

        $query->select('b.' . $db->nameQuote('component_option') . ' as ' . $db->nameQuote('option'));
        $query->select('b.' . $db->nameQuote('source_table'));

        $query->from($db->nameQuote('#__assets') . ' as a');
        $query->from($db->nameQuote('#__asset_types') . ' as b');

        $query->where('a.' . $db->nameQuote('asset_type_id') . ' = b.' . $db->nameQuote('id'));

        if ((int)$request->get('asset_id', 0) == 0) {
            if (MolajoController::getApplication()->get('sef', 1) == 1) {
                $query->where('a.' . $db->nameQuote('sef_request') . ' = ' . $db->Quote($request->get('query_request')));
            } else {
                $query->where('a.' . $db->nameQuote('request') . ' = ' . $db->Quote($request->get('query_request')));
            }
        } else {
            $query->where('a.' . $db->nameQuote('id') . ' = ' . (int)$request->get('asset_id'));
        }

        $db->setQuery($query->__toString());
        $results = $db->loadObjectList();

        if ($db->getErrorNum() == 0) {
        } else {
            MolajoController::getApplication()->setMessage($db->getErrorMsg(), MOLAJO_MESSAGE_TYPE_ERROR);
            return false;
        }

        if (count($results) == 0) {
            $found = false;

        } else {
            $found = true;

            foreach ($results as $result) {

                if ((int)$request->get('asset_id', 0)
                    == MolajoController::getApplication()->get('home_asset_id')
                ) {
                    $request->set('home', true);
                } else {
                    $request->set('home', false);
                }

                $request->set('option', $result->option);
                $request->set('template_id', $result->template_id);
                $request->set('page', $result->template_page);
                $request->set('asset_id', $result->asset_id);
                $request->set('asset_type_id', $result->asset_type_id);
                $request->set('source_table', $result->source_table);
                $request->set('source_id', $result->source_id);
                $request->set('source_language', $result->language);
                $request->set('translation_of_id', $result->translation_of_id);
                $request->set('view_group_id', $result->view_group_id);
                $request->set('category', $result->primary_category_id);
                $request->set('redirect_to_id', $result->redirect_to_id);

                $request->set('request', $result->request);
                $request->set('sef_request', $result->sef_request);

                $parameterArray = array();
                $temp = substr($request->get('request'), 10, (strlen($request->get('request')) - 10));
                $parameterArray = explode('&', $temp);
                $other_parameters = array();

                foreach ($parameterArray as $parameter) {

                    $pair = explode('=', $parameter);

                    if ($pair[0] == 'task') {
                        $request->get('task', $pair[1]);

                    } elseif ($pair[0] == 'format') {
                        $request->get('format', $pair[1]);

                    } elseif ($pair[0] == 'option') {
                        $request->get('option', $pair[1]);

                    } elseif ($pair[0] == 'view') {
                        $request->get('view', $pair[1]);

                    } elseif ($pair[0] == 'wrap') {
                        $request->get('wrap', $pair[1]);

                    } elseif ($pair[0] == 'template') {
                        $request->get('template_name', $pair[1]);

                    } elseif ($pair[0] == 'page') {
                        $request->get('page', $pair[1]);

                    } elseif ($pair[0] == 'static') {
                        $request->get('wrap', $pair[1]);

                    } elseif ($pair[0] == 'ids') {
                        $request->get('ids', $pair[1]);

                    } elseif ($pair[0] == 'id') {
                        $request->get('id', $pair[1]);

                    } else {

                        $other_parameters[] = $pair[0]->$pair[1];
                    }
                }
                $request->set('other_parameters', $other_parameters);
            }
        }
        $request->set('found', $found);
        return $request;
    }

    /**
     * getExtensionOptions
     *
     * Construct the Request Array for the MVC
     *
     * @return bool
     */
    public static function getExtensionOptions(JObject $request)
    {
        $request->set('controller', '');
        $request->set('model', '');
        $request->set('plugin_type', '');
        $request->set('acl_implementation', '');
        $request->set('component_table', '');

        /** Configuration model */
        $configModel = new MolajoModelConfiguration ($request->get('option'));

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
            $request->set('model', ucfirst($request->get('option')) . 'Model' . ucfirst($model));
        }

        if ($request->get('controller') == 'display') {

            /** 6. Format */
            if ($request->get('format') == '') {
                $results = false;
            } else {
                $results = $configModel->getOptionLiteralValue(MOLAJO_EXTENSION_OPTION_ID_FORMATS, $request->get('format'));
            }

            /** get default format */
            if ($results === false) {
                $request->get('format', $configModel->getOptionValue(MOLAJO_EXTENSION_OPTION_ID_FORMATS_DEFAULT));
                if ($request->get('format') === false) {
                    $request->get('format', MolajoController::getApplication()->get('default_format', 'html'));
                }
            }

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