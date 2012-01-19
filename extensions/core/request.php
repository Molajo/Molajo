<?php
/**
 * @package     Molajo
 * @subpackage  Request
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Processes the Request
 *
 * Base class
 */
class MolajoRequest
{
    /**
     *  Request
     *
     * @var object
     * @since 1.0
     */
    public $request;

    /**
     * __construct
     *
     * Class constructor.
     *
     * @param   null    $override_url_request
     * @param   null    $asset_id
     *
     * @return boolean
     *
     * @since  1.0
     */
    public function __construct($override_url_request = null, $asset_id = null)
    {
        /** Request Variables: Passed to Document, Renderers and MVC */
        $this->_initializeRequest();

        /** Specific asset */
        if ((int)$asset_id == 0) {
            $this->request->set('asset_id', 0);
        } else {
            $this->request->set('asset_id', $asset_id);
        }

        /**
         * Specific URL path
         *  Request is stripped of Host, Folder, and Application
         *  Path ex. index.php?option=login or access/groups
         */
        if ($override_url_request == null) {
            $path = MOLAJO_PAGE_REQUEST;
        } else {
            $path = $override_url_request;
        }

        /** duplicate content: URLs without the .html */
        if ($this->request->get('application_sef_suffix') == 'html'
            && substr($path, -11) == '/index.html'
        ) {
            $path = substr($path, 0, (strlen($path) - 11));
        }
        if ($this->request->get('application_sef_suffix') == 'html'
            && substr($path, -5) == '.html'
        ) {
            $path = substr($path, 0, (strlen($path) - 5));
        }

        /** populate value used in query  */
        $this->request->set('url_query_request', $path);

        /** home: duplicate content - redirect */
        if ($this->request->get('url_query_request', '') == 'index.php'
            || $this->request->get('url_query_request', '') == 'index.php/'
            || $this->request->get('url_query_request', '') == 'index.php?'
            || $this->request->get('url_query_request', '') == '/index.php/'
        ) {
            MolajoController::getApplication()->redirect('', 301);
            return $this->request;
        }

        /** Home */
        if ($this->request->get('url_query_request', '') == ''
            && (int)$this->request->get('asset_id', 0) == 0
        ) {
            $this->request->set('asset_id', MolajoController::getApplication()->get('home_asset_id', 0));
            $this->request->set('url_home', true);
        }

        return;
    }

    /**
     * _initializeRequest
     *
     * Create and Initialize the request
     *
     * Request Object which is passed on to Document, Renderers and the MVC classes
     *
     * @static
     * @return array
     * @since 1.0
     */
    private function _initializeRequest()
    {
        $this->request = new JObject();

        /**  site information */
        $this->request->set('site_id', MOLAJO_SITE_ID);
        $this->request->set('site_name', MOLAJO_SITE_NAME);
        $this->request->set('site_asset_type_id', MOLAJO_ASSET_TYPE_BASE_SITE);
        $this->request->set('site_asset_id', (int)
        MolajoAssetHelper::getAssetID($this->request->get('site_asset_type_id'),
            MOLAJO_SITE_ID));

        /**  application information */
        $this->request->set('application_id', MOLAJO_APPLICATION_ID);
        $this->request->set('application_name', MolajoController::getApplication()->get('application_name', MOLAJO_APPLICATION));
        $this->request->set('application_asset_type_id', MOLAJO_ASSET_TYPE_BASE_APPLICATION);
        $this->request->set('application_asset_id', (int)
        MolajoAssetHelper::getAssetID($this->request->get('application_asset_type_id'),
            MOLAJO_APPLICATION_ID));

        $this->request->set('application_sef', MolajoController::getApplication()->get('sef', 1));
        $this->request->set('application_sef_rewrite', MolajoController::getApplication()->get('sef_rewrite', 0));
        $this->request->set('application_sef_suffix', MolajoController::getApplication()->get('sef_suffix', 'html'));
        $this->request->set('application_unicode_slugs', MolajoController::getApplication()->get('unicode_slugs', 0));
        $this->request->set('application_force_ssl', MolajoController::getApplication()->get('force_ssl', 0));

        $this->request->set('application_media_priority_site', (int)
        MolajoController::getApplication()->get('media_priority_site', 100));
        $this->request->set('application_media_priority_application', (int)
        MolajoController::getApplication()->get('media_priority_application', 200));
        $this->request->set('application_media_priority_user', (int)
        MolajoController::getApplication()->get('media_priority_user', 300));
        $this->request->set('application_media_priority_module', (int)
        MolajoController::getApplication()->get('media_priority_module', 400));
        $this->request->set('application_media_priority_plugin', (int)
        MolajoController::getApplication()->get('media_priority_plugin', 400));
        $this->request->set('application_media_priority_component', (int)
        MolajoController::getApplication()->get('media_priority_component', 500));
        $this->request->set('application_media_priority_template', (int)
        MolajoController::getApplication()->get('media_priority_template', 600));
        $this->request->set('application_media_priority_primary_category', (int)
        MolajoController::getApplication()->get('media_priority_primary_category', 700));
        $this->request->set('application_media_priority_menu_item', (int)
        MolajoController::getApplication()->get('media_priority_menu_item', 800));
        $this->request->set('application_media_priority_source_data', (int)
        MolajoController::getApplication()->get('media_priority_source_data', 900));

        $this->request->set('application_default_template_name',
            MolajoController::getApplication()->get('default_template', 'system'));
        $this->request->set('application_default_page_name',
            MolajoController::getApplication()->get('default_page', 'default'));

        $this->request->set('application_default_static_view_name',
            MolajoController::getApplication()->get('default_static_view', 'dummy'));
        $this->request->set('application_default_static_wrap_name',
            MolajoController::getApplication()->get('default_static_wrap', 'none'));
        $this->request->set('application_default_items_view_name',
            MolajoController::getApplication()->get('default_items_view', 'items'));
        $this->request->set('application_default_items_wrap_name',
            MolajoController::getApplication()->get('default_items_wrap', 'div'));
        $this->request->set('application_default_item_view_name',
            MolajoController::getApplication()->get('default_item_view', 'item'));
        $this->request->set('application_default_item_wrap_name',
            MolajoController::getApplication()->get('default_item_wrap', 'div'));
        $this->request->set('application_default_edit_view_name',
            MolajoController::getApplication()->get('default_edit_view', 'edit'));
        $this->request->set('application_default_edit_wrap_name',
            MolajoController::getApplication()->get('default_edit_wrap', 'div'));

        /**  user information */
        $this->request->set('user_id', (int)MolajoController::getUser()->get('id'), 0);
        $this->request->set('user_username', MolajoController::getUser()->get('username'), 'guest');
        $this->request->set('user_guest', (boolean)MolajoController::getUser()->get('guest'), true);
        $this->request->set('user_asset_type_id', MOLAJO_ASSET_TYPE_USER);
        $this->request->set('user_asset_id', (int)
        MolajoAssetHelper::getAssetID($this->request->get('user_asset_type_id'),
            $this->request->get('user_id')));
        $this->request->set('user_parameters', MolajoController::getUser()->parameters);
        $this->request->set('user_custom_fields', MolajoController::getUser()->custom_fields);
        $this->request->set('user_view_groups', MolajoController::getUser()->view_groups);
        $this->request->set('user_template_id', '');
        $this->request->set('user_page_id', '');

        /** request */
        $this->request->set('url_base', MOLAJO_BASE_URL);
        $this->request->set('url_query_request', '');
        $this->request->set('url_request', '');
        $this->request->set('url_sef_request', '');
        $this->request->set('url_redirect_to_id', 0);
        $this->request->set('url_home', 0);

        /** template */
        $this->request->set('template_id', 0);
        $this->request->set('template_name', '');
        $this->request->set('template_parameters', array());
        $this->request->set('template_path', '');
        $this->request->set('template_include', '');
        $this->request->set('template_favicon', '');
        $this->request->set('template_asset_id', 0);

        /** page */
        $this->request->set('page_id', 0);
        $this->request->set('page_name', '');
        $this->request->set('page_path', '');
        $this->request->set('page_path_url', '');
        $this->request->set('page_template_include_statement', '');
        $this->request->set('page_asset_id', 0);

        /** merged metadata */
        $this->request->set('metadata_title', '');
        $this->request->set('metadata_description', '');
        $this->request->set('metadata_generator', MolajoController::getApplication()->get('generator', 'Molajo'));
        $this->request->set('metadata_keywords', '');
        $this->request->set('metadata_author', '');
        $this->request->set('metadata_content_rights', '');
        $this->request->set('metadata_robots', '');
        $this->request->set('metadata_additional_array', array());

        /** component parameters */
        $this->request->set('component_extension_instance_id', '');
        $this->request->set('component_extension_instance_name', '');
        $this->request->set('component_controller', '');
        $this->request->set('component_model', '');
        $this->request->set('component_plugin_type', '');
        $this->request->set('component_task', '');
        $this->request->set('component_view_id', '');
        $this->request->set('component_view_name', '');
        $this->request->set('component_wrap_id', '');
        $this->request->set('component_wrap_name', '');
        $this->request->set('component_parameters', array());
        $this->request->set('component_metadata', array());
        $this->request->set('component_id', 0);
        $this->request->set('component_category_id', 0);
        $this->request->set('component_asset_type_id', MOLAJO_ASSET_TYPE_MENU_ITEM_COMPONENT);
        $this->request->set('component_asset_id', 0);
        $this->request->set('component_view_group_id', 0);

        /** menu item data */
        $this->request->set('menu_item_id', 0);
        $this->request->set('menu_item_title', '');
        $this->request->set('menu_item_parameters', array());
        $this->request->set('menu_item_metadata', array());
        $this->request->set('menu_item_asset_type_id', MOLAJO_ASSET_TYPE_MENU_ITEM_COMPONENT);
        $this->request->set('menu_item_asset_id', 0);
        $this->request->set('menu_item_language', '');
        $this->request->set('menu_item_translation_of_id', 0);
        $this->request->set('menu_item_view_group_id', 0);
        $this->request->set('menu_item_template_id', '');
        $this->request->set('menu_item_page_id', '');
        $this->request->set('menu_item_view_id', '');
        $this->request->set('menu_item_wrap_id', '');

        /** primary category */
        $this->request->set('category_id', 0);
        $this->request->set('category_title', '');
        $this->request->set('category_parameters', array());
        $this->request->set('category_metadata', array());
        $this->request->set('category_asset_type_id', MOLAJO_ASSET_TYPE_CATEGORY_LIST);
        $this->request->set('category_asset_id', 0);

        /** source data */
        $this->request->set('source_table', '');
        $this->request->set('source_id', 0);
        $this->request->set('source_title', '');
        $this->request->set('source_last_modified', getDate());
        $this->request->set('source_parameters', array());
        $this->request->set('source_metadata', array());
        $this->request->set('source_asset_type_id', 0);
        $this->request->set('source_asset_id', 0);
        $this->request->set('source_language', '');
        $this->request->set('source_translation_of_id', 0);
        $this->request->set('source_view_group_id', 0);

        /** above this line does not change */
        $this->request->set('data_above_does_not_change_for_page_load', 'no delta');
        /**  */
        $this->request->set('data_below_changes_for_each_extension_renderer', 'delta');
        /** below this line changes for each extension / renderer */

        /** status */
        $this->request->set('status_error', false);
        $this->request->set('status_authorised', false);
        $this->request->set('status_found', false);

        /** extension */
        $this->request->set('extension_instance_id', 0);
        $this->request->set('extension_title', '');
        $this->request->set('extension_parameters', array());
        $this->request->set('extension_metadata', array());
        $this->request->set('extension_path', '');
        $this->request->set('extension_type', '');
        $this->request->set('extension_folder', '');
        $this->request->set('extension_asset_type_id', MOLAJO_ASSET_TYPE_USER);
        $this->request->set('extension_asset_id', 0);
        $this->request->set('extension_suppress_no_results', false);

        /** mvc parameters */
        $this->request->set('mvc_extension_instance_id', '');
        $this->request->set('mvc_extension_instance_name', '');
        $this->request->set('mvc_controller', '');
        $this->request->set('mvc_model', '');
        $this->request->set('mvc_model_no_data', '');
        $this->request->set('mvc_plugin_type', '');
        $this->request->set('mvc_task', '');
        $this->request->set('mvc_view_id', '');
        $this->request->set('mvc_view_name', '');
        $this->request->set('mvc_wrap_id', '');
        $this->request->set('mvc_wrap_name', '');
        $this->request->set('mvc_parameters', array());
        $this->request->set('mvc_metadata', array());
        $this->request->set('mvc_id', 0);
        $this->request->set('mvc_category_id', 0);
        $this->request->set('mvc_asset_type_id', MOLAJO_ASSET_TYPE_MENU_ITEM_COMPONENT);
        $this->request->set('mvc_asset_id', 0);
        $this->request->set('mvc_view_group_id', 0);

        /** view */
        $this->request->set('view_id', 0);
        $this->request->set('view_name', '');
        $this->request->set('view_type', 'extensions');
        $this->request->set('view_path', '');
        $this->request->set('view_path_url', '');
        $this->request->set('view_asset_type_id', MOLAJO_ASSET_TYPE_EXTENSION_VIEW);
        $this->request->set('view_asset_id', 0);

        /** wrap */
        $this->request->set('wrap_id', 0);
        $this->request->set('wrap_name', '');
        $this->request->set('wrap_path', '');
        $this->request->set('wrap_path_url', '');
        $this->request->set('wrap_id', '');
        $this->request->set('wrap_class', '');
        $this->request->set('wrap_asset_type_id', MOLAJO_ASSET_TYPE_EXTENSION_VIEW);
        $this->request->set('wrap_asset_id', 0);

        /** results */
        $this->request->set('results', '');

        return $this->request;
    }

    /**
     * process
     *
     * Using the MOLAJO_PAGE_REQUEST value,
     *  retrieve the asset record,
     *  set the variables needed to render output,
     *  execute document renders and MVC
     *
     * @return bool|null
     */
    public function process()
    {
        if (MolajoController::getApplication()->get('offline', 0) == 1) {
            $this->request->set('status_error', true);
            $this->request->set('mvc_task', 'display');
            MolajoController::getApplication()->setHeader('Status', '503 Service Temporarily Unavailable', 'true');
            $message = MolajoController::getApplication()->get('offline_message', 'This site is not available.<br /> Please check back again soon.');
            MolajoController::getApplication()->setMessage($message, MOLAJO_MESSAGE_TYPE_WARNING . 503);
            $this->request->set('template_name', MolajoController::getApplication()->get('offline_template', 'system'));
            $this->request->set('page_name', MolajoController::getApplication()->get('offline_page', 'offline'));

        } else {
            $results = $this->_getAsset();
            if ((int)$this->request->get('menu_item_id') > 0
                && (int)$this->request->get('source_asset_id') > 0
            ) {
                // AMY //
            }
            $this->_routeRequest();
            $this->_authoriseTask();
        }

        if ($this->request->get('mvc_task') == 'add'
            || $this->request->get('mvc_task') == 'edit'
            || $this->request->get('mvc_task') == 'display'
        ) {
            return $this->_renderDocument();
        } else {
            return $this->_processTask();
        }

        return;
    }

    /**
     * _getAsset
     *
     * Retrieve Asset and Asset Type data for a specific asset id or query request
     *
     * @results  null
     * @since    1.0
     */
    protected function _getAsset()
    {
        $row = MolajoAssetHelper::getAsset($this->request->get('asset_id'),
            $this->request->get('url_query_request'));

        /** Not found: exit */
        if (count($row) == 0) {
            return $this->request->set('status_found', false);
        }

        $this->request->set('status_found', true);
        $this->request->set('template_id', $row->template_id);
        $this->request->set('page_id', $row->page_id);

        if ($row->asset_type_id == MOLAJO_ASSET_TYPE_MENU_ITEM_COMPONENT) {
            $this->request->set('menu_item_id', $row->source_id);
            $this->request->set('menu_item_title', $row->title);
            $this->request->set('menu_item_parameters', array());
            $this->request->set('menu_item_metadata', array());
            $this->request->set('menu_item_asset_type_id', $row->asset_type_id);
            $this->request->set('menu_item_asset_id', $row->asset_id);
            $this->request->set('menu_item_language', $row->language);
            $this->request->set('menu_item_translation_of_id', $row->translation_of_id);
            $this->request->set('menu_item_view_group_id', $row->view_group_id);

            $this->_getMenuItemParameters();

        } else {
            $this->request->set('source_table', $row->source_table);
            $this->request->set('source_id', $row->source_id);
            $this->request->set('source_asset_id', $row->asset_id);
            $this->request->set('source_asset_type_id', $row->asset_type_id);
            $this->request->set('source_title', $row->title);
            $this->request->set('source_parameters', array());
            $this->request->set('source_metadata', array());
            $this->request->set('source_asset_type_id', $row->asset_type_id);
            $this->request->set('source_asset_id', $row->asset_id);
            $this->request->set('source_language', $row->language);
            $this->request->set('source_translation_of_id', $row->translation_of_id);
            $this->request->set('source_view_group_id', $row->view_group_id);

            $this->_getSourceParameters();
        }

        /** process asset */
        if ((int)$this->request->get('asset_id', 0)
            == MolajoController::getApplication()->get('home_extension_id')
        ) {
            $this->request->set('url_home', true);
        } else {
            $this->request->set('url_home', false);
        }

        $this->request->set('url_request', $row->request);
        $this->request->set('url_sef_request', $row->sef_request);
        $this->request->set('url_redirect_to_id', $row->redirect_to_id);

                $parameterArray = array();
                $temp = substr($this->request->get('url_request'),
                    10, (strlen($this->request->get('url_request')) - 10));
                $parameterArray = explode('&', $temp);
                $url_parameters = array();

                if (count($parameterArray) > 0) {
                    foreach ($parameterArray as $q) {

                        $pair = explode('=', $q);

                        if ($pair[0] == 'task') {
                            $this->request->set('mvc_task', $pair[1]);

                        } elseif ($pair[0] == 'option') {
                            $this->request->set('extension_title', $pair[1]);

                        } elseif ($pair[0] == 'view_id') {
                            $this->request->set('view_id', $pair[1]);

                        } elseif ($pair[0] == 'wrap_id') {
                            $this->request->set('wrap_id', $pair[1]);

                        } elseif ($pair[0] == 'template_id') {
                            $this->request->set('template_id', $pair[1]);

                        } elseif ($pair[0] == 'page_id') {
                            $this->request->set('page_id', $pair[1]);

                        } elseif ($pair[0] == 'static') {
                            $this->request->set('mvc_model_no_data', (boolean) $pair[1]);

                        } elseif ($pair[0] == 'category_id') {
                            $this->request->set('mvc_category_id', $pair[1]);

                        } elseif ($pair[0] == 'extension_instance_id') {
                            $this->request->set('extension_instance_id', $pair[1]);

                        } elseif ($pair[0] == 'id') {
                            $this->request->set('mvc_id', $pair[1]);
                        }
                        $url_parameters[$pair[0]] = $pair[1];
                    }
                }
                $this->request->set('mvc_url_parameters', $url_parameters);

        return;
    }

    /**
     * _getMenuItemParameters
     *
     * Retrieve the Menu Item Parameters and Meta Data
     *
     * @return  boolean
     * @since   1.0
     */
    protected function _getMenuItemParameters()
    {
        $results = MolajoExtensionHelper::getMenuItem((int)$this->request->get('menu_item_id'));
        if (count($results) > 0) {
        } else {
            return;
        }

        foreach ($results as $result) {
            // $result populated: only a single row returned
        }

        $this->request->set('menu_item_title', $result->menu_item_title);

        $parameters = new JRegistry;
        $parameters->loadString($result->menu_item_parameters);
        $this->request->set('menu_item_parameters', $parameters);

        $this->request->set('menu_item_metadata', $result->menu_item_metadata);
        $this->request->set('menu_item_custom_fields', $result->menu_item_custom_fields);

        if (isset($parameters->static)
            && $parameters->static === true
        ) {
            $this->request->set('mvc_model_no_data', true);
        } else {
            $this->request->set('mvc_model_no_data', false);
        }

        $id = $parameters->def('id', 0);
        if (is_array($id)) {
        } else if ((int)$id == 0) {
            $this->request->set('source_id', $id);
        } else {
            $this->request->set('source_id', $id);
        }

        $table = $parameters->def('source_table', '__content');
        $this->request->set('source_table', $table);

        $this->request->set('mvc_id', $id);
        $this->request->set('mvc_category_id', $parameters->def('category_id', 0));

        $this->request->set('extension_path', MOLAJO_EXTENSIONS_COMPONENTS . '/' . $this->request->set('extension_title'));
        $this->request->set('extension_type', 'component');
        $this->request->set('extension_folder', '');


        $this->_setPageValues($this->request->get('menu_item_parameters',
            $this->request->get('menu_item_metadata')));

        return;
    }

    /**
     * _routeRequest
     *
     * Route the application.
     *
     * Routing is the process of examining the request environment to determine which
     * component should receive the request. The component optional parameters
     * are then set in the request object to be processed when the application is being
     * dispatched.
     *
     * @return  void;
     * @since  1.0
     */
    protected function _routeRequest()
    {
        //        MolajoPluginHelper::importPlugin('system');
        //        MolajoController::getApplication()->triggerEvent('onAfterRoute');

        /** 404 Not Found */
        if ($this->request->get('status_found') === false) {
            $this->request->set('status_error', true);
            $this->request->set('mvc_task', 'display');
            MolajoController::getApplication()->setHeader('Status', '404 Not Found', 'true');
            $message = MolajoController::getApplication()->get('error_404_message', 'Page not found.');
            MolajoController::getApplication()->setMessage($message, MOLAJO_MESSAGE_TYPE_ERROR, 404);
            $this->request->set('template_name', MolajoController::getApplication()->get('error_template', 'system'));
            $this->request->set('page_name', MolajoController::getApplication()->get('error_page', 'error'));
        }

        /** redirect_to_id */
        if ($this->request->get('url_redirect_to_id', 0) == 0) {
        } else {
            MolajoController::getApplication()->redirect($this->request->set('url_redirect_to_id', 301));
        }

        /** Must be Logged on Requirement */
        if (MolajoController::getApplication()->get('logon_requirement', 0) > 0
            && MolajoController::getUser()->get('guest', true) === true
            && $this->request->get('asset_id') <> MolajoController::getApplication()->get('logon_requirement', 0)
        ) {
            $this->request->set('status_error', true);
            $this->request->set('mvc_task', 'display');
            MolajoController::getApplication()->redirect(MolajoController::getApplication()->get('logon_requirement', 0), 303);
        }

        return;
    }

    /**
     * _authoriseTask
     *
     * Test user is authorised to view page
     *
     * @return  boolean
     * @since   1.0
     */
    protected function _authoriseTask()
    {
        if (in_array($this->request->get('asset_view_group_id'), $this->request->get('user_view_groups'))) {
            $this->request->set('status_authorised', true);
        } else {
            $this->request->set('status_authorised', false);
        }

        if ($this->request->get('status_authorised') === false) {
            $this->request->set('status_error', true);
            $this->request->set('mvc_task', 'display');
            MolajoController::getApplication()->setHeader('Status', '403 Not Authorised', 'true');
            $message = MolajoController::getApplication()->get('error_403_message', 'Not Authorised.');
            MolajoController::getApplication()->setMessage($message, MOLAJO_MESSAGE_TYPE_ERROR, 403);
            $this->request->set('template_name', MolajoController::getApplication()->get('error_template', 'system'));
            $this->request->set('page_name', MolajoController::getApplication()->get('error_page', 'error'));
        }
    }

    /**
     *  _renderDocument
     *
     *  Retrieves and sets parameter values in order of priority
     *  Then, execute Document Class (which executes Renderers and MVC Classes)
     *
     * @return void
     * @since  1.0
     */
    protected function _renderDocument()
    {
        if ($this->request->get('status_error') === true) {
        } else {
            $this->_getSourceParameters();
            $this->_getCategoryParameters();
            $this->_getComponentParameters();
            $this->request = MolajoExtensionHelper::getExtensionOptions($this->request);
        }

        $this->_getUserParameters();

        $this->_getApplicationDefaults();

        $this->_getTemplateParameters();

        $this->_mergeParameters();

        $this->_setRenderingPaths();

        /**
        $temp = (array)$this->request;
        echo '<pre>';
        var_dump($temp);
        echo '</pre>';
        die;
         **/
        /** Render Document */
        new MolajoDocument ($this->request);
        return $this->request;
    }

    /**
     *  _processTask
     *
     * @return
     * @since  1.0
     */
    protected function _processTask()
    {
    }

    /**
     * _getSourceParameters
     *
     * Retrieve Parameters and Metadata for Source Detail
     *
     * @return  bool
     * @since   1.0
     */
    protected function _getSourceParameters()
    {
        if ((int)$this->request->get('source_id') == 0) {
            return;
        }

        $db = MolajoController::getDbo();
        $query = $db->getQuery(true);

        $query->select('a.' . $db->nameQuote('extension_instance_id'));
        $query->select('a.' . $db->nameQuote('title'));
        $query->select('a.' . $db->nameQuote('parameters'));
        $query->select('a.' . $db->nameQuote('metadata'));
        $query->from($db->nameQuote('#' . $this->request->get('source_table')) . ' as a');
        $query->where('a.' . $db->nameQuote('id') . ' = ' . (int)$this->request->get('source_id'));

        $db->setQuery($query->__toString());
        $results = $db->loadObjectList();

        if (count($results) > 0) {
        } else {
            return;
        }

        foreach ($results as $result) {
            $this->request->set('extension_instance_id', $result->extension_instance_id);
            $this->request->set('source_title', $result->title);
            $this->request->set('source_parameters', $result->parameters);
            $this->request->set('source_metadata', $result->metadata);
        }

        $this->_setPageValues($this->request->get('source_parameters',
            $this->request->get('source_metadata')));

        return;
    }

    /**
     * _getCategoryParameters
     *
     * Retrieve the Menu Item Parameters and Meta Data
     *
     * @return  boolean
     * @since   1.0
     */
    protected function _getCategoryParameters()
    {
        if ((int)$this->request->get('category_id', 0) == 0) {
            return;
        }

        $db = MolajoController::getDbo();
        $query = $db->getQuery(true);

        $query->select('a.' . $db->nameQuote('title'));
        $query->select('a.' . $db->nameQuote('parameters'));
        $query->select('a.' . $db->nameQuote('metadata'));
        $query->from($db->nameQuote('#__content') . ' as a');
        $query->where('a.' . $db->nameQuote('id') . ' = ' . (int)$this->request->get('category_id'));

        $db->setQuery($query->__toString());

        $results = $db->loadObjectList();
        if (count($results) > 0) {
        } else {
            return;
        }

        foreach ($results as $result) {
            $this->request->set('category_title', $result->title);
            $this->request->set('category_parameters', $result->parameters);
            $this->request->set('category_metadata', $result->metadata);
        }

        $this->_setPageValues($this->request->get('category_parameters',
            $this->request->get('category_metadata')));

        return;
    }

    /**
     * _getComponentParameters
     *
     * Retrieve Component information using either the ID
     *
     * @return bool
     * @since 1.0
     */
    protected function _getComponentParameters()
    {
        if ((int)$this->request->get('extension_instance_id') == 0) {
            return;
        }

        $results = MolajoExtensionHelper::get(MOLAJO_ASSET_TYPE_EXTENSION_COMPONENT, (int)$this->request->get('extension_instance_id'));

        if (count($results) > 0) {
        } else {
            return;
        }

        foreach ($results as $result) {
            $this->request->set('extension_name', $result->extension_name);
            $this->request->set('extension_title', $result->title);

            $parameters = new JRegistry;
            $parameters->loadString($result->parameters);
            $this->request->set('extension_parameters', $parameters);
            $this->request->set('extension_metadata', $result->metadata);
            $this->request->set('custom_fields', $result->metadata);

            if (isset($parameters->static)
                && $parameters->static === true
            ) {
                $this->request->set('mvc_model_no_data', true);
            } else {
                $this->request->set('mvc_model_no_data', false);
            }
            $this->request->set('extension_path', MOLAJO_EXTENSIONS_COMPONENTS . '/' . $this->request->set('extension_title'));
            $this->request->set('extension_type', 'component');
            $this->request->set('extension_folder', '');
        }

        $this->_setPageValues($this->request->get('extension_parameters',
            $this->request->get('extension_metadata')));

        return;
    }

    /**
     * _setPageValues
     *
     * Set the values needed to generate the page (template, page, view, wrap, and various metadata)
     *
     * @param null $sourceParameters
     * @param null $sourceMetadata
     *
     * @return bool
     * @since 1.0
     */
    protected function _setPageValues($parameters = null, $metadata = null)
    {
        /** rendering parameters */
        $params = new JRegistry;
        $params->loadString($parameters);

        if ($this->request->get('template_name', '') == '') {
            $this->request->set('template_name', $params->def('template_name', ''));
        }

        if ($this->request->get('page_name', '') == '') {
            $this->request->set('page_name', $params->def('page_name', ''));
        }

        if ($this->request->get('view_name', '') == '') {
            $this->request->set('view_name', $params->def('view_name', ''));
        }

        if ($this->request->get('wrap_name', '') == '') {
            $this->request->set('wrap_name', $params->def('wrap_name', ''));
        }

        /** merge meta data */
        $meta = new JRegistry;
        $meta->loadString($metadata);

        if ($this->request->get('metadata_title', '') == '') {
            $this->request->set('metadata_title', $meta->def('metadata_title', ''));
        }
        if ($this->request->get('metadata_description', '') == '') {
            $this->request->set('metadata_description', $meta->def('metadata_description', ''));
        }
        if ($this->request->get('metadata_keywords', '') == '') {
            $this->request->set('metadata_keywords', $meta->def('metadata_keywords', ''));
        }
        if ($this->request->get('metadata_author', '') == '') {
            $this->request->set('metadata_author', $meta->def('metadata_author', ''));
        }
        if ($this->request->get('metadata_content_rights', '') == '') {
            $this->request->set('metadata_content_rights', $meta->def('metadata_content_rights', ''));
        }
        if ($this->request->get('metadata_robots', '') == '') {
            $this->request->set('metadata_robots', $meta->def('metadata_robots', ''));
        }

        return;
    }

    /**
     * _getUserParameters
     *
     * Get Template Name using either the Template ID or the Template Name
     *
     * @return bool
     * @since 1.0
     */
    protected function _getUserParameters()
    {
        $params = new JRegistry;
        $params->loadString($this->request->get('user_parameters'));

        $this->request->set('user_template_name', $params->def('template_name', ''));

        $this->request->set('user_page_name', $params->def('page_name', ''));

        if ($this->request->get('template_name', '') == '') {
            $this->request->set('template_name', $this->request->get('user_template_name'));
        }

        if ($this->request->get('page_name', '') == '') {
            $this->request->set('page_name', $this->request->get('user_page_name'));
        }

        return;
    }

    /**
     * _getTemplateParameters
     *
     * Get Template Name using either the Template ID or the Template Name
     *
     * @return bool
     * @since 1.0
     */
    protected function _getTemplateParameters()
    {
        if ((int)$this->request->set('template_id') == 0) {
            $template = $this->request->get('template_name');
        } else {
            $template = $this->request->get('template_id');
        }

        $results = MolajoTemplateHelper::getTemplate($template);

        if (count($results) > 0) {
            if ($this->request->get('template_name') == 'system') {
                // error
            } else {
                $this->request->set('template_name', 'system');
                $results = MolajoTemplateHelper::getTemplate('system');
                if (count($results) > 0) {
                    // error
                }
            }
        }

        foreach ($results as $result) {
            $parameters = new JRegistry;
            $parameters->loadString($result->parameters);
            $this->request->set('template_id', $result->extension_id);
            $this->request->set('template_name', $result->title);
            $this->request->set('template_parameters', $parameters);
            $this->request->set('template_asset_id', $result->extension_instance_asset_id);
        }

        if ($this->request->get('page_name', '') == '') {
            $this->request->set('page_name', $parameters->get('page', ''));
        }

        return;
    }

    /**
     *  _getApplicationDefaults
     *
     *  Retrieve Template and Page from the final level of default values, if needed
     *
     * @return bool
     * @since 1.0
     */
    protected function _getApplicationDefaults()
    {
        /** template/page */
        if ($this->request->get('template_name', '') == '') {
            $this->request->set('template_name', MolajoController::getApplication()->get('default_template', ''));
        }
        if ($this->request->get('page_name', '') == '') {
            $this->request->set('page_name', MolajoController::getApplication()->get('default_page', ''));
        }

        /** view */
        if ($this->request->get('view_name', '') == '') {

            if ($this->request->get('mvc_model_no_data', true)) {
                $this->request->set('view_name', MolajoController::getApplication()->get('default_static_view', ''));

            } else if ($this->request->get('mvc_task', '') == 'add'
                || $this->request->get('mvc_task', '') == 'edit'
            ) {
                $this->request->set('mvc_task', MolajoController::getApplication()->get('default_edit_view', ''));

            } else if ((int)$this->request->get('mvc_id', 0) == 0) {
                $this->request->set('view_name', MolajoController::getApplication()->get('default_items_view', ''));

            } else {
                $this->request->set('view_name', MolajoController::getApplication()->get('default_item_view', ''));
            }
        }

        /** wrap */
        if ($this->request->get('wrap_name', '') == '') {

            if ($this->request->get('mvc_model_no_data', false) === true) {
                $this->request->set('wrap_name', MolajoController::getApplication()->get('default_static_wrap', ''));

            } elseif ($this->request->get('mvc_task', '') == 'add'
                || $this->request->get('mvc_task', '') == 'edit'
            ) {
                $this->request->set('mvc_task', MolajoController::getApplication()->get('default_edit_wrap', ''));

            } else if ((int)$this->request->get('mvc_id', 0) == 0) {
                $this->request->set('wrap_name', MolajoController::getApplication()->get('default_items_wrap', ''));

            } else {
                $this->request->set('wrap_name', MolajoController::getApplication()->get('default_item_wrap', ''));
            }
        }

        /** metadata  */
        if ($this->request->get('metadata_title', '') == '') {
            $appname = MolajoController::getApplication()->get('application_name', '');
            $sitename = MolajoController::getApplication()->get('site_name', '');
            if (trim($appname) == trim($sitename)) {
                $temp = $appname;
            } else {
                $temp = $appname . ' - ' . $sitename;
            }
            $this->request->set('metadata_title', $temp);
        }

        if ($this->request->get('metadata_description', '') == '') {
            $this->request->set('metadata_description', MolajoController::getApplication()->get('metadata_description', ''));
        }

        if ($this->request->get('metadata_keywords', '') == '') {
            $this->request->set('metadata_keywords', MolajoController::getApplication()->get('metadata_keywords', ''));
        }

        if ($this->request->get('metadata_author', '') == '') {
            $this->request->set('metadata_author', MolajoController::getApplication()->get('metadata_author', ''));
        }

        if ($this->request->get('metadata_content_rights', '') == '') {
            $this->request->set('metadata_content_rights', MolajoController::getApplication()->get('metadata_content_rights', ''));
        }

        if ($this->request->get('metadata_robots', '') == '') {
            $this->request->set('metadata_robots', MolajoController::getApplication()->get('metadata_robots', ''));
        }
    }

    /**
     * getRedirectURL
     *
     * Function to retrieve asset information for the Request or Asset ID
     *
     * @return  string url
     * @since   1.0
     */
    public static function getRedirectURL($asset_id)
    {
        $db = MolajoController::getDbo();
        $query = $db->getQuery(true);

        if ((int)$asset_id == MolajoController::getApplication()->get('home_asset_id', 0)) {
            return '';
        }

        if (MolajoController::getApplication()->get('sef', 1) == 0) {
            $query->select('a.' . $db->nameQuote('sef_request'));
        } else {
            $query->select('a.' . $db->nameQuote('request'));
        }

        $query->from($db->nameQuote('#__assets') . ' as a');
        $query->where('a.' . $db->nameQuote('id') . ' = ' . (int)$asset_id);

        $db->setQuery($query->__toString());

        return $db->loadResult();
    }

    /**
     * _setQueryParameters
     *
     * Retrieve Parameter overrides from URL
     *
     * @return bool
     * @since 1.0
     */
    protected function _setQueryParameters()
    {
        //  todo: amy add parameter to turn this off in the template manager
        //  todo: amy filter input
        $parameterArray = array();
        $temp = substr(MOLAJO_PAGE_REQUEST, 10, (strlen(MOLAJO_PAGE_REQUEST) - 10));
        $parameterArray = explode('&', $temp);

        foreach ($parameterArray as $parameter) {

            $pair = explode('=', $parameter);

            if ($pair[0] == 'view') {
                $this->request->set('view_name', (string)$pair[1]);

            } elseif ($pair[0] == 'wrap') {
                $this->request->set('wrap_name', (string)$pair[1]);

            } elseif ($pair[0] == 'template') {
                $this->request->set('template_name', (string)$pair[1]);

            } elseif ($pair[0] == 'page') {
                $this->request->set('page_name', (string)$pair[1]);
            }
        }
        return true;
    }

    /**
     *  _mergeParameters
     */
    protected function _mergeParameters()
    {
        return;
        /** initialize */
        $temp = array();
        $parameters = array();

        /** load request (without parameter fields) */
        //        $temp = $this->request;
        //        $parameters = $this->_merge($parameters, $temp);

        /** source parameters */
        $temp = array();
        $temp = json_decode($this->request->get('source_parameters'));
        $parameters = $this->_merge($parameters, $temp);

        /** category parameters */
        $temp = array();
        $temp = json_decode($this->request->get('category_parameters'));
        $parameters = $this->_merge($parameters, $temp);

        /** extension parameters */
        $temp = array();
        $temp = json_decode($this->request->get('extension_parameters'));

        $this->parameters = $this->_merge($parameters, $temp);

        echo '<pre>';
        var_dump($this->parameters);
        '</pre>';
        die();
    }

    /**
     *  _merge
     */
    protected function _merge($parameters, $temp)
    {
        if (count($temp) == 0) {
            return $parameters;
        }
        foreach ($temp as $name => $value) {
            if (strpos($name, 'parameter')) {
            } else {
                if (isset($parameters->$name)) {
                    if (trim($parameters->$name) == '') {
                        $parameters->$name = $value;
                    }
                } else {
                    $parameters->$name = $value;
                }
            }
        }
        return $parameters;
    }


    /**
     * _setRenderingPaths
     *
     * Set paths for Template, page, view, and wrap
     *
     * @return mixed
     */
    protected function _setRenderingPaths()
    {
        if ($this->request->get('status_error') === true) {
        } else {
            $this->request->set('view_type', 'extensions');
            $viewHelper = new MolajoViewHelper($this->request->get('view_name'),
                $this->request->get('view_type'),
                $this->request->get('extension_title'),
                $this->request->get('extension_type'),
                ' ',
                $this->request->get('template_name'));
            $this->request->set('view_path', $viewHelper->view_path);
            $this->request->set('view_path_url', $viewHelper->view_path_url);
        }

        if ($this->request->get('status_error') === true) {
        } else {
            $wrapHelper = new MolajoViewHelper($this->request->get('wrap_name'),
                'wraps',
                $this->request->get('extension_title'),
                $this->request->get('extension_type'),
                ' ',
                $this->request->get('template_name'));
            $this->request->set('wrap_path', $wrapHelper->view_path);
            $this->request->set('wrap_path_url', $wrapHelper->view_path_url);
        }

        /** Template Path */
        $path = MolajoTemplateHelper::getPath($this->request->get('template_name'));
        $this->request->set('template_path', $path);

        /** Page Path */
        $pageHelper = new MolajoViewHelper($this->request->get('page_name'),
            'pages',
            $this->request->get('extension_title'),
            $this->request->get('extension_type'),
            ' ',
            $this->request->get('template_name'));
        $this->request->set('page_path', $pageHelper->view_path);
        $this->request->set('page_path_url', $pageHelper->view_path_url);

        return;
    }
}
