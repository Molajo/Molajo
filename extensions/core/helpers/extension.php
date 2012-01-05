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

        /** Extensions */
        $query->select('a.' . $db->namequote('id') . ' as extension_id');
        $query->select('a.' . $db->namequote('name') . ' as extension_name');
        $query->select('a.' . $db->namequote('element'));
        $query->select('a.' . $db->namequote('folder'));

        $query->from($db->namequote('#__extensions') . ' as a');

        $query->where('a.' . $db->namequote('asset_type_id') . ' = ' . (int)$asset_type_id);
        if ($extension_type == null) {
        } else {
            $query->where('(a.' . $db->namequote('folder') . ' = ' . $db->quote($extension_type) . ')');
        }

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
     * createRequestArray
     *
     * Create and Initialize the requestArray
     *
     * Array shared between format, rendering and MVC classes
     *
     * @static
     * @return array
     * @since 1.0
     */
    public static function createRequestArray()
    {
        /** @var $requestArray */
        $requestArray = array();

        /** request URL */
        $requestArray['query_request'] = '';
        $requestArray['request'] = '';
        $requestArray['sef_request'] = '';
        $requestArray['redirect_to_id'] = 0;
        $requestArray['home'] = 0;

        $requestArray['sef'] = 0;
        $requestArray['sef_rewrite'] = 0;
        $requestArray['sef_suffix'] = 0;
        $requestArray['unicodeslugs'] = 0;
        $requestArray['force_ssl'] = 0;

        /** render parameters */
        $requestArray['controller'] = '';
        $requestArray['static'] = '';
        $requestArray['model'] = '';
        $requestArray['option'] = '';
        $requestArray['format'] = '';
        $requestArray['task'] = '';

        $requestArray['view'] = '';
        $requestArray['view_type'] = 'extensions';
        $requestArray['view_path'] = '';
        $requestArray['view_path_url'] = '';

        $requestArray['wrap'] = '';
        $requestArray['wrap_path'] = '';
        $requestArray['wrap_path_url'] = '';
        $requestArray['wrap_id'] = '';
        $requestArray['wrap_class'] = '';

        $requestArray['page'] = '';
        $requestArray['page_path'] = '';
        $requestArray['page_path_url'] = '';

        $requestArray['format'] = '';
        $requestArray['id'] = 0;
        $requestArray['ids'] = array();

        $requestArray['plugin_type'] = '';
        $requestArray['acl_implementation'] = '';
        $requestArray['other_parameters'] = array();

        /** template */
        $requestArray['template_id'] = 0;
        $requestArray['template_name'] = '';
        $requestArray['template_parameters'] = array();

        /** head */
        $requestArray['metadata_title'] = '';
        $requestArray['metadata_description'] = '';
        $requestArray['metadata_keywords'] = '';
        $requestArray['metadata_author'] = '';
        $requestArray['metadata_rights'] = '';
        $requestArray['metadata_robots'] = '';
        $requestArray['metadata_additional_array'] = array();

        /** asset */
        $requestArray['asset_id'] = 0;
        $requestArray['asset_type_id'] = 0;
        $requestArray['source_language'] = '';
        $requestArray['translation_of_id'] = 0;
        $requestArray['view_group_id'] = 0;

        /** source data */
        $requestArray['source_table'] = '';
        $requestArray['source_id'] = 0;
        $requestArray['source_parameters'] = array();
        $requestArray['source_metadata'] = array();

        /** primary category */
        $requestArray['category'] = 0;
        $requestArray['category_title'] = '';
        $requestArray['category_parameters'] = array();
        $requestArray['category_metadata'] = array();

        /** extension */
        $requestArray['extension_instance_id'] = 0;
        $requestArray['extension_title'] = '';
        $requestArray['extension_parameters'] = array();
        $requestArray['extension_metadata'] = array();
        $requestArray['extension_path'] = '';
        $requestArray['extension_type'] = '';
        $requestArray['extension_folder'] = '';

        /** results */
        $requestArray['results'] = '';

        return $requestArray;
    }

    /**
     * getAsset
     *
     * Function to retrieve asset information for the Request or Asset ID
     *
     * @static
     * @param $requestArray
     * @return bool
     * @since 1.0
     */
    public static function getAsset($requestArray)
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

        if ((int)$requestArray['asset_id'] == 0) {
            if (MolajoController::getApplication()->get('sef', 1) == 1) {
                $query->where('a.' . $db->nameQuote('sef_request') . ' = ' . $db->Quote($requestArray['query_request']));
            } else {
                $query->where('a.' . $db->nameQuote('request') . ' = ' . $db->Quote($requestArray['query_request']));
            }
        } else {
            $query->where('a.' . $db->nameQuote('id') . ' = ' . (int)$requestArray['asset_id']);
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

                if ($requestArray['asset_id'] == MolajoController::getApplication()->get('home_asset_id')) {
                    $requestArray['home'] = true;
                } else {
                    $requestArray['home'] = false;
                }
                $requestArray['option'] = $result->option;
                $requestArray['template_id'] = $result->template_id;
                $requestArray['page'] = $result->template_page;
                $requestArray['asset_id'] = $result->asset_id;
                $requestArray['asset_type_id'] = $result->asset_type_id;
                $requestArray['source_table'] = $result->source_table;
                $requestArray['source_id'] = $result->source_id;
                $requestArray['source_language'] = $result->language;
                $requestArray['translation_of_id'] = $result->translation_of_id;
                $requestArray['view_group_id'] = $result->view_group_id;
                $requestArray['category'] = $result->primary_category_id;
                $requestArray['redirect_to_id'] = $result->redirect_to_id;

                $requestArray['request'] = $result->request;
                $requestArray['sef_request'] = $result->sef_request;

                $parameterArray = array();
                $temp = substr($requestArray['request'], 10, (strlen($requestArray['request']) - 10));
                $parameterArray = explode('&', $temp);
                $other_parameters = array();

                foreach ($parameterArray as $parameter) {

                    $pair = explode('=', $parameter);

                    if ($pair[0] == 'task') {
                        $requestArray['task'] = $pair[1];

                    } elseif ($pair[0] == 'format') {
                        $requestArray['format'] = $pair[1];

                    } elseif ($pair[0] == 'option') {
                        $requestArray['option'] = $pair[1];

                    } elseif ($pair[0] == 'view') {
                        $requestArray['view'] = $pair[1];

                    } elseif ($pair[0] == 'wrap') {
                        $requestArray['wrap'] = $pair[1];

                    } elseif ($pair[0] == 'template') {
                        $requestArray['template_name'] = $pair[1];

                    } elseif ($pair[0] == 'page') {
                        $requestArray['page'] = $pair[1];

                    } elseif ($pair[0] == 'static') {
                        $requestArray['wrap'] = $pair[1];

                    } elseif ($pair[0] == 'ids') {
                        $requestArray['ids'] = $pair[1];

                    } elseif ($pair[0] == 'id') {
                        $requestArray['id'] = $pair[1];

                    } else {
                        $other_parameters[] = $pair[0]->$pair[1];
                    }
                }
                $requestArray['other_parameters'] = $other_parameters;
            }
        }
        $requestArray['found'] = $found;
        return $requestArray;
    }

    /**
     * getExtensionOptions
     *
     * Construct the Request Array for the MVC
     *
     * @return bool
     */
    public static function getExtensionOptions($requestArray)
    {
        $requestArray['controller'] = '';
        $requestArray['model'] = '';
        $requestArray['plugin_type'] = '';
        $requestArray['acl_implementation'] = '';
        $requestArray['component_table'] = '';

        /** Configuration model */
        $configModel = new MolajoModelConfiguration ($requestArray['option']);

        /** Task */
        if (isset($requestArray['task']) && ($requestArray['task'] != '')) {
        } else {
            $requestArray['task'] = 'display';
        }

        /** Controller (while validating Task) */
        $requestArray['controller'] = $configModel->getOptionLiteralValue(MOLAJO_EXTENSION_OPTION_ID_TASKS_CONTROLLER, $requestArray['task']);
        if ($requestArray['controller'] === false) {
            MolajoError::raiseError(500, MolajoTextHelper::_('MOLAJO_INVALID_TASK_CONTROLLER') . ' ' . $requestArray['task']);
            $requestArray['results'] = false;
            return $requestArray;
        }

        /** IDs */
        if (isset($requestArray['id'])) {
        } else {
            $requestArray['id'] = 0;
        }
        if (isset($requestArray['ids'])) {
        } else {
            $requestArray['ids'] = array();
        }
        if (isset($requestArray['category'])) {
        } else {
            $requestArray['category'] = 0;
        }

        if ($requestArray['task'] == 'add') {

            if ((int)$requestArray['id'] == 0 && count($requestArray['ids']) == 0) {
            } else {
                MolajoError::raiseError(500, MolajoTextHelper::_('MOLAJO_ERROR_ADD_TASK_MUST_NOT_HAVE_ID'));
                $requestArray['results'] = false;
                return $requestArray;
            }

        } else if ($requestArray['task'] == 'edit' || $requestArray['task'] == 'restore') {

            if ($requestArray['id'] > 0 && count($requestArray['ids']) == 0) {

            } else if ((int)$requestArray['id'] == 0 && count($requestArray['ids']) == 1) {
                $requestArray['id'] = $requestArray['ids'][0];
                $requestArray['ids'] = array();

            } else if ((int)$requestArray['id'] == 0 && count($requestArray['ids']) == 0) {
                MolajoError::raiseError(500, MolajoTextHelper::_('MOLAJO_ERROR_EDIT_TASK_MUST_HAVE_ID'));
                $requestArray['results'] = false;
                return $requestArray;

            } else if (count($requestArray['ids']) > 1) {
                MolajoError::raiseError(500, MolajoTextHelper::_('MOLAJO_ERROR_TASK_MAY_NOT_HAVE_MULTIPLE_IDS'));
                $requestArray['results'] = false;
                return $requestArray;
            }
        }

        /** Model */
        $model = '';
        if ($requestArray['controller'] == 'display') {
            if ($requestArray['static'] === true) {
                $model = 'dummy';
            } else {
                $model = 'display';
            }
        } else {
            $model = 'edit';
        }
        $requestArray['model'] = ucfirst($requestArray['option']) . 'Model' .ucfirst($model);

        if ($requestArray['controller'] == 'display') {

            /** 6. Format */
            if ($requestArray['format'] == '') {
                $results = false;
            } else {
                $results = $configModel->getOptionLiteralValue(MOLAJO_EXTENSION_OPTION_ID_FORMATS, $requestArray['format']);
            }

            /** get default format */
            if ($results === false) {
                $requestArray['format'] = $configModel->getOptionValue(MOLAJO_EXTENSION_OPTION_ID_FORMATS_DEFAULT);
                if ($requestArray['format'] === false) {
                    $requestArray['format'] = MolajoController::getApplication()->get('default_format', 'html');
                }
            }

            /** View **/
            if ($requestArray['static'] === true) {
                $option = 3300;

            } else if ($requestArray['id'] > 0) {
                if ($requestArray['task'] == 'display') {
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

            if ($requestArray['view'] == '') {
                $results = false;
            } else {
                $results = $configModel->getOptionLiteralValue($option, $requestArray['view']);
            }

            $option = $option + 10;
/* todo: amy fix/remove if ($results === false) {
                $requestArray['view'] = $configModel->getOptionValue($option);
                if ($requestArray['view'] === false) {
                    MolajoController::getApplication()->setMessage(MolajoTextHelper::_('MOLAJO_NO_VIEWS_DEFAULT_DEFINED'), 'error');
                    $requestArray['results'] = false;
                    return $requestArray;
                }
            }
**/
            /** Wrap **/
            $option = $option + 10;
            if ($requestArray['wrap'] == null) {
                $results = false;
            } else {
                $results = $configModel->getOptionLiteralValue($option, $requestArray['wrap']);
            }

            $option = $option + 10;
            if ($results === false) {
                $requestArray['wrap'] = $configModel->getOptionValue($option);
                if ($requestArray['wrap'] === false) {
                    $requestArray['wrap'] = 'none';
                }
            }

            /** Page **/
            $option = $option + 10;
            if ($requestArray['page'] == null) {
                $results = false;
            } else {
                $results = $configModel->getOptionLiteralValue($option, $requestArray['page']);
            }

            $option = $option + 10;
            if ($results === false) {
                $requestArray['page'] = $configModel->getOptionValue($option);
                if ($requestArray['page'] === false) {
                    $requestArray['page'] = 'full';
                }
            }
        }
        /** todo: amy: come back and get redirect */

        /** ACL implementation */
        $requestArray['acl_implementation'] = $configModel->getOptionValue(MOLAJO_EXTENSION_OPTION_ID_ACL_IMPLEMENTATION);
        if ($requestArray['acl_implementation'] === false) {
            $requestArray['acl_implementation'] = 'core';
        }

        /** Component table */
        $requestArray['component_table'] = $configModel->getOptionValue(MOLAJO_EXTENSION_OPTION_ID_TABLE);
        if ($requestArray['component_table'] === false) {
            $requestArray['component_table'] = '__content';
        }

        /** Plugin helper */
        $requestArray['plugin_type'] = $configModel->getOptionValue(MOLAJO_EXTENSION_OPTION_ID_PLUGIN_TYPE);
        if ($requestArray['plugin_type'] === false) {
            $requestArray['plugin_type'] = 'content';
        }

        $requestArray['results'] = true;
        return $requestArray;
    }
}