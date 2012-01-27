<?php
/**
 * @package     Molajo
 * @subpackage  Request
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * MolajoRequest
 *
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
     * @param   null    $override_request_url
     * @param   null    $asset_id
     *
     * @return boolean
     *
     * @since  1.0
     */
    public function __construct($override_request_url = null, $asset_id = null)
    {
        $this->_initializeRequest();

        /** Specific asset */
        if ((int)$asset_id == 0) {
            $this->request->set('request_asset_id', 0);
        } else {
            $this->request->set('request_asset_id', $asset_id);
        }

        /**
         * Specific URL path
         *  Request is stripped of Host, Folder, and Application
         *  Path ex. index.php?option=login or access/groups
         */
        if ($override_request_url == null) {
            $path = MOLAJO_PAGE_REQUEST;
        } else {
            $path = $override_request_url;
        }

        /** duplicate content: URLs without the .html */
        if ((int)MolajoController::getApplication()->get('sef_suffix', 1) == 1
            && substr($path, -11) == '/index.html'
        ) {
            $path = substr($path, 0, (strlen($path) - 11));
        }
        if ((int)MolajoController::getApplication()->get('sef_suffix', 1) == 1
            && substr($path, -5) == '.html'
        ) {
            $path = substr($path, 0, (strlen($path) - 5));
        }

        /** populate value used in query  */
        $this->request->set('request_url_query', $path);

        /** home: duplicate content - redirect */
        if ($this->request->get('request_url_query', '') == 'index.php'
            || $this->request->get('request_url_query', '') == 'index.php/'
            || $this->request->get('request_url_query', '') == 'index.php?'
            || $this->request->get('request_url_query', '') == '/index.php/'
        ) {
            MolajoController::getApplication()->redirect('', 301);
            return $this->request;
        }

        /** Home */
        if ($this->request->get('request_url_query', '') == ''
            && (int)$this->request->get('request_asset_id', 0) == 0
        ) {
            $this->request->set('request_asset_id',
                MolajoController::getApplication()->get('home_asset_id', 0));
            $this->request->set('request_url_home', true);
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

        /** request */
        $this->request->set('request_url_base', MOLAJO_BASE_URL);
        $this->request->set('request_asset_id', 0);
        $this->request->set('request_asset_type_id', 0);
        $this->request->set('request_url_query', '');
        $this->request->set('request_url', '');
        $this->request->set('request_url_sef', '');
        $this->request->set('request_url_redirect_to_id', 0);
        $this->request->set('request_url_home', false);

        /** menu item data */
        $this->request->set('menu_item_id', 0);
        $this->request->set('menu_item_title', '');
        $this->request->set('menu_item_asset_type_id',
            MOLAJO_ASSET_TYPE_MENU_ITEM_COMPONENT);
        $this->request->set('menu_item_asset_id', 0);
        $this->request->set('menu_item_view_group_id', 0);
        $this->request->set('menu_item_custom_fields', array());
        $this->request->set('menu_item_parameters', array());
        $this->request->set('menu_item_metadata', array());
        $this->request->set('menu_item_language', '');
        $this->request->set('menu_item_translation_of_id', 0);

        /** primary category */
        $this->request->set('category_id', 0);
        $this->request->set('category_title', '');
        $this->request->set('category_asset_type_id',
            MOLAJO_ASSET_TYPE_CATEGORY_LIST);
        $this->request->set('category_asset_id', 0);
        $this->request->set('category_view_group_id', 0);
        $this->request->set('category_custom_fields', array());
        $this->request->set('category_parameters', array());
        $this->request->set('category_metadata', array());
        $this->request->set('category_language', '');
        $this->request->set('category_translation_of_id', 0);

        /** source data */
        $this->request->set('source_id', 0);
        $this->request->set('source_title', '');
        $this->request->set('source_asset_type_id', 0);
        $this->request->set('source_asset_id', 0);
        $this->request->set('source_view_group_id', 0);
        $this->request->set('source_custom_fields', array());
        $this->request->set('source_parameters', array());
        $this->request->set('source_metadata', array());
        $this->request->set('source_language', '');
        $this->request->set('source_translation_of_id', 0);
        $this->request->set('source_table', '');
        $this->request->set('source_last_modified', getDate());

        /** extension */
        $this->request->set('extension_instance_id', 0);
        $this->request->set('extension_instance_name', '');
        $this->request->set('extension_asset_type_id', 0);
        $this->request->set('extension_asset_id', 0);
        $this->request->set('extension_view_group_id', 0);
        $this->request->set('extension_custom_fields', array());
        $this->request->set('extension_metadata', array());
        $this->request->set('extension_parameters', array());
        $this->request->set('extension_path', '');
        $this->request->set('extension_type', '');
        $this->request->set('extension_folder', '');
        $this->request->set('extension_event_type', '');

        /** merged */
        $this->request->set('parameters', array());

        $this->request->set('metadata_title', '');
        $this->request->set('metadata_description', '');
        $this->request->set('metadata_generator',
            MolajoController::getApplication()->get('generator', 'Molajo'));
        $this->request->set('metadata_keywords', '');
        $this->request->set('metadata_author', '');
        $this->request->set('metadata_content_rights', '');
        $this->request->set('metadata_robots', '');
        $this->request->set('metadata_additional_array', array());

        /** template */
        $this->request->set('template_id', 0);
        $this->request->set('template_name', '');
        $this->request->set('template_asset_type_id',
            MOLAJO_ASSET_TYPE_EXTENSION_TEMPLATE);
        $this->request->set('template_asset_id', 0);
        $this->request->set('template_view_group_id', 0);
        $this->request->set('template_custom_fields', array());
        $this->request->set('template_metadata', array());
        $this->request->set('template_parameters', array());
        $this->request->set('template_path', '');
        $this->request->set('template_path_url', '');
        $this->request->set('template_include', '');
        $this->request->set('template_favicon', '');

        /** page */
        $this->request->set('page_id', 0);
        $this->request->set('page_name', '');
        $this->request->set('page_css_id', '');
        $this->request->set('page_css_class', '');
        $this->request->set('page_asset_type_id', MOLAJO_ASSET_TYPE_EXTENSION_TEMPLATE);
        $this->request->set('page_asset_id', 0);
        $this->request->set('page_path', '');
        $this->request->set('page_path_url', '');
        $this->request->set('page_include', '');

        /** view */
        $this->request->set('view_id', 0);
        $this->request->set('view_name', '');
        $this->request->set('view_css_id', '');
        $this->request->set('view_css_class', '');
        $this->request->set('view_asset_type_id', MOLAJO_ASSET_TYPE_EXTENSION_VIEW);
        $this->request->set('view_asset_id', 0);
        $this->request->set('view_path', '');
        $this->request->set('view_path_url', '');

        /** wrap */
        $this->request->set('wrap_id', 0);
        $this->request->set('wrap_name', '');
        $this->request->set('wrap_css_id', '');
        $this->request->set('wrap_css_class', '');
        $this->request->set('wrap_asset_type_id', MOLAJO_ASSET_TYPE_EXTENSION_VIEW);
        $this->request->set('wrap_asset_id', 0);
        $this->request->set('wrap_path', '');
        $this->request->set('wrap_path_url', '');

        /** mvc parameters */
        $this->request->set('mvc_controller', '');
        $this->request->set('mvc_task', '');
        $this->request->set('mvc_model', '');
        $this->request->set('mvc_id', 0);
        $this->request->set('mvc_category_id', 0);
        $this->request->set('mvc_url_parameters', array());
        $this->request->set('mvc_suppress_no_results', false);

        /** results */
        $this->request->set('status_error', false);
        $this->request->set('status_authorised', false);
        $this->request->set('status_found', false);

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
     * @return bool
     * @since  1.0
     */
    public function process()
    {
        /** offline */
        if (MolajoController::getApplication()->get('offline', 0) == 1) {
            $this->_error(503);

        } else {
            $this->_getAsset();
            $this->_routeRequest();
            $this->_authoriseTask();
        }

        /** display */
        if ($this->request->get('mvc_task') == 'add'
            || $this->request->get('mvc_task') == 'edit'
            || $this->request->get('mvc_task') == 'display'
        ) {
            $this->_renderDocument();

            /** action */
        } else {
            $this->_processTask();
        }

        return $this->request;
    }

    /**
     * _getAsset
     *
     * Retrieve Asset and Asset Type data for a specific asset id or query request
     *
     * @return   boolean
     * @since    1.0
     */
    protected function _getAsset()
    {
        $results = MolajoAssetHelper::getAssetRequestObject($this->request);

        /** not found: exit */
        if ($results === false) {
            return $this->request->set('status_found', false);
        }
        $this->request = $results;

        /** menu item */
        if ($this->request->get('request_asset_type_id')
            == MOLAJO_ASSET_TYPE_MENU_ITEM_COMPONENT
        ) {
            $this->_getMenuItem();
        }

        /** source data */
        if ($this->request->get('source_id', 0) == 0) {
        } else {
            $this->_getSource();
        }

        /** primary category */
        if ($this->request->get('category_id', 0) == 0) {
        } else {
            $this->request->set('mvc_category_id', $this->request->get('category_id'));
            $this->_getPrimaryCategory();
        }

        /** extension */
        if ($this->request->get('extension_instance_id', 0) == 0) {
            return $this->request->set('status_found', false); //todo: amy 500 error
        } else {
            $this->_getExtension();
        }

        return $this->request->get('status_found');
    }

    /**
     * _getMenuItem
     *
     * Retrieve the Menu Item Data
     *
     * @return  boolean
     * @since   1.0
     */
    protected function _getMenuItem()
    {
        $row = MolajoContentHelper::get(
            (int)$this->request->get('menu_item_id'),
            $this->request->get('source_table'));

        /** todo: amy 500 error */
        if (count($row) == 0) {
            return $this->request->set('status_found', false);
        }

        $this->request->set('menu_item_title', $row->title);
        $this->request->set('menu_item_asset_type_id', $row->asset_type_id);
        $this->request->set('menu_item_asset_id', $row->asset_id);
        $this->request->set('menu_item_view_group_id', $row->view_group_id);

        $parameters = new JRegistry;
        $parameters->loadString($row->parameters);
        $this->request->set('menu_item_parameters', $parameters);

        $custom_fields = new JRegistry;
        $custom_fields->loadString($row->custom_fields);
        $this->request->set('menu_item_custom_fields', $custom_fields);

        $metadata = new JRegistry;
        $metadata->loadString($row->metadata);
        $this->request->set('menu_item_metadata', $metadata);

        $this->request->set('menu_item_language', $row->language);
        $this->request->set('menu_item_translation_of_id', $row->translation_of_id);

        $this->_setPageValues($this->request->get('menu_item_parameters',
            $this->request->get('menu_item_metadata')));

        /** mvc */
        if ($this->request->get('mvc_controller', '') == '') {
            $this->request->set('mvc_controller', $parameters->def('controller', 'display'));
        }
        if ($this->request->get('mvc_task', '') == '') {
            $this->request->set('mvc_task', $parameters->def('task', 'display'));
        }
        if ($this->request->get('extension_instance_name', '') == '') {
            $this->request->set('extension_instance_name', $parameters->def('option', 0));
        }
        if ($this->request->get('mvc_model', '') == '') {
            $this->request->set('mvc_model', $parameters->def('model', 'display'));
        }
        if ((int)$this->request->get('mvc_id', 0) == 0) {
            $this->request->set('mvc_id', $parameters->def('id', 0));
        }
        if ((int)$this->request->get('mvc_category_id', 0) == 0) {
            $this->request->set('mvc_category_id', $parameters->def('category_id', 0));
        }
        if ((int)$this->request->get('mvc_suppress_no_results', 0) == 0) {
            $this->request->set('mvc_suppress_no_results', $parameters->def('suppress_no_results', 0));
        }

        /** extension */
        $this->request->set('extension_instance_id', $parameters->def('extension_instance_id', 0));

        /** source */
        $this->request->set('source_id', $parameters->def('id', 0));

        return $this->request->set('status_found', true);
    }

    /**
     * _getSource
     *
     * Retrieve Parameters and Metadata for Source Detail
     *
     * @return  bool
     * @since   1.0
     */
    protected function _getSource()
    {
        $row = MolajoContentHelper::get(
            (int)$this->request->get('source_id'),
            $this->request->get('source_table'));

        /** not found: exit */
        if (count($row) == 0) {
            return $this->request->set('status_found', false);
        }

        /** match found */
        $this->request->set('source_title', $row->asset_type_id);
        $this->request->set('source_asset_type_id', $row->asset_type_id);
        $this->request->set('source_asset_id', $row->asset_id);
        $this->request->set('source_view_group_id', $row->view_group_id);
        $this->request->set('source_language', $row->language);
        $this->request->set('source_translation_of_id', $row->translation_of_id);
        $this->request->set('source_last_modified', $row->modified_datetime);

        $this->request->set('extension_instance_id', $row->extension_instance_id);

        $custom_fields = new JRegistry;
        $custom_fields->loadString($row->custom_fields);
        $this->request->set('source_custom_fields', $custom_fields);

        $metadata = new JRegistry;
        $metadata->loadString($row->metadata);
        $this->request->set('source_metadata', $metadata);

        $parameters = new JRegistry;
        $parameters->loadString($row->parameters);
        $this->request->set('source_parameters', $parameters);

        /** mvc */
        if ($this->request->get('mvc_controller', '') == '') {
            $this->request->set('mvc_controller', $parameters->def('controller', 'display'));
        }
        if ($this->request->get('mvc_task', '') == '') {
            $this->request->set('mvc_task', $parameters->def('task', 'display'));
        }
        if ($this->request->get('extension_instance_name', '') == '') {
            $this->request->set('extension_instance_name', $parameters->def('option', 0));
        }
        if ($this->request->get('mvc_model', '') == '') {
            $this->request->set('mvc_model', $parameters->def('model', 'display'));
        }
        if ((int)$this->request->get('mvc_id', 0) == 0) {
            $this->request->set('mvc_id', $parameters->def('id', 0));
        }
        if ((int)$this->request->get('mvc_category_id', 0) == 0) {
            $this->request->set('mvc_category_id', $parameters->def('category_id', 0));
        }
        if ((int)$this->request->get('mvc_suppress_no_results', 0) == 0) {
            $this->request->set('mvc_suppress_no_results', $parameters->def('suppress_no_results', 0));
        }

        $this->_setPageValues($this->request->get('source_parameters',
            $this->request->get('source_metadata')));

        return $this->request->set('status_found', true);
    }

    /**
     * _getPrimaryCategory
     *
     * Retrieve the Menu Item Parameters and Meta Data
     *
     * @return  boolean
     * @since   1.0
     */
    protected function _getPrimaryCategory()
    {
        $row = MolajoContentHelper::get(
            (int)$this->request->get('category_id'),
            '__content');

        /** not found: exit */
        if (count($row) == 0) {
            return $this->request->set('status_found', false);
        }
        $this->request->set('category_title', $row->title);
        $this->request->set('category_asset_type_id', $row->asset_type_id);
        $this->request->set('category_asset_id', $row->asset_id);
        $this->request->set('category_view_group_id', $row->view_group_id);
        $this->request->set('category_language', $row->language);
        $this->request->set('category_translation_of_id', $row->translation_of_id);

        $custom_fields = new JRegistry;
        $custom_fields->loadString($row->custom_fields);
        $this->request->set('category_custom_fields', $custom_fields);

        $metadata = new JRegistry;
        $metadata->loadString($row->metadata);
        $this->request->set('category_metadata', $metadata);

        $parameters = new JRegistry;
        $parameters->loadString($row->parameters);
        $this->request->set('category_parameters', $parameters);

        $this->_setPageValues($this->request->get('category_parameters',
            $this->request->get('category_metadata')));

        return $this->request->set('status_found', true);
    }

    /**
     * _getExtension
     *
     * Retrieve Component information using either the ID
     *
     * @return bool
     * @since 1.0
     */
    protected function _getExtension()
    {
        $results = MolajoExtensionHelper::getExtensionRequestObject($this->request);
        if ($results === false) {
            return $this->request->set('status_found', false);
        }
        $this->request = $results;

        $this->_setPageValues($this->request->get('extension_parameters',
            $this->request->get('extension_metadata')));

        return $this->request->set('status_found', true);
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

        if ((int)$this->request->get('template_id', 0) == 0) {
            $this->request->set('template_id', $params->def('template_id', 0));
        }

        if ((int)$this->request->get('page_id', 0) == 0) {
            $this->request->set('page_id', $params->def('page_id', 0));
        }

        if ((int)$this->request->get('view_id', 0) == 0) {
            $this->request->set('view_id', $params->def('view_id', 0));
        }

        if ((int)$this->request->get('wrap_id', 0) == 0) {
            $this->request->set('wrap_id', $params->def('wrap_id', 0));
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
        /** not found */
        if ($this->request->get('status_found') === false) {
            $this->_error(404);
        }

        /** redirect */
        if ($this->request->get('request_url_redirect_to_id', 0) == 0) {
        } else {
            MolajoController::getApplication()->redirect(MolajoAssetHelper::getURL($this->request->get('request_url_redirect_to_id')), 301);
        }

        /** must be logged on */
        if (MolajoController::getApplication()->get('logon_requirement', 0) > 0
            && MolajoController::getUser()->get('guest', true) === true
            && $this->request->get('request_asset_id')
                <> MolajoController::getApplication()->get('logon_requirement', 0)
        ) {
            MolajoController::getApplication()->redirect(
                MolajoController::getApplication()->get('logon_requirement', 0),
                303);
        }

        return;
    }

    /**
     * _authoriseTask
     *
     * Verify user authorization for task
     *
     * @return  boolean
     * @since   1.0
     */
    protected function _authoriseTask()
    {
        $this->request->set('status_authorised',
            MolajoAccess::authoriseTask(
                $this->request->get('mvc_task'),
                $this->request->get('mvc_model'),
                $this->request->get('mvc_asset_id')));

        if ($this->request->get('status_authorised') === true) {
        } else {
            $this->_error(403);
        }
    }

    /**
     *  _renderDocument
     *
     *  Retrieves and sets parameter values in order of priority
     *  Then, execute Document Class (which executes renderers and MVC classes)
     *
     * @return void
     * @since  1.0
     */
    protected function _renderDocument()
    {
        if ($this->request->get('status_error') === true) {
        } else {
            //            $this->request = MolajoExtensionHelper::getOptions($this->request);
        }

        $this->_getUser();

        $this->_getApplicationDefaults();

        $this->_getTemplate();

        $this->_getPage();

        $this->_getView();

        $this->_getWrap();

        $this->_mergeParameters();

        /** Render Document */
        new MolajoDocument ($this->request);
        return true;
    }

    /**
     * _getUser
     *
     * Get Template Name using either the Template ID or the Template Name
     *
     * @return bool
     * @since 1.0
     */
    protected function _getUser()
    {
        $parameters = new JRegistry;
        $parameters->loadString(MolajoController::getUser()->parameters);

        if ($this->request->get('template_id', 0) == 0) {
            $this->request->set('template_id', $parameters->def('user_template_id', 0));
        }
        if ($this->request->get('page_id', 0) == 0) {
            $this->request->set('page_id', $parameters->def('user_page_id', 0));
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
        if ($this->request->get('template_id', 0) == 0) {
            $this->request->set('template_id',
                MolajoController::getApplication()->get('default_template_id', ''));
        }

        if ($this->request->get('page_id', 0) == 0) {
            $this->request->set('page_id',
                MolajoController::getApplication()->get('default_page_id', ''));
        }

        /** view */
        if ((int) $this->request->get('view_id', 0) == '') {
            $this->request->set('view_id',
                MolajoViewHelper::getViewDefaults('view',
                    $this->request->get('mvc_model'),
                    $this->request->get('mvc_task', ''),
                    (int)$this->request->get('mvc_id', 0))
            );
        }

        /** wrap */
        if ((int) $this->request->get('wrap_id', 0) == '') {
            $this->request->set('wrap_id',
                MolajoViewHelper::getViewDefaults('wrap',
                    $this->request->get('mvc_model'),
                    $this->request->get('mvc_task', ''),
                    (int)$this->request->get('mvc_id', 0))
            );
        }

        /** controller */
        if ($this->request->get('mvc_task', '') == 'add'
            || $this->request->get('mvc_task', '') == 'edit'
            || $this->request->get('mvc_task', '') == 'display'
        ) {
            $this->request->set('mvc_controller', 'display');

        } else if ((int)$this->request->get('mvc_task') == 'login') {
            $this->request->set('mvc_controller', 'login');

        } else {
            $this->request->set('mvc_controller', 'update');
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
            $this->request->set('metadata_description',
                MolajoController::getApplication()->get('metadata_description', ''));
        }

        if ($this->request->get('metadata_keywords', '') == '') {
            $this->request->set('metadata_keywords',
                MolajoController::getApplication()->get('metadata_keywords', ''));
        }

        if ($this->request->get('metadata_author', '') == '') {
            $this->request->set('metadata_author',
                MolajoController::getApplication()->get('metadata_author', ''));
        }

        if ($this->request->get('metadata_content_rights', '') == '') {
            $this->request->set('metadata_content_rights',
                MolajoController::getApplication()->get('metadata_content_rights', ''));
        }

        if ($this->request->get('metadata_robots', '') == '') {
            $this->request->set('metadata_robots',
                MolajoController::getApplication()->get('metadata_robots', ''));
        }

        return;
    }

    /**
     * _getTemplate
     *
     * Get Template Name using either the Template ID or the Template Name
     *
     * @return bool
     * @since 1.0
     */
    protected function _getTemplate()
    {
        $row = MolajoTemplateHelper::get($this->request->get('template_id'));

        if (count($row) == 0) {
            if ($this->request->set('template_name') == 'system') {
                // error
            } else {
                $this->request->set('template_name', 'system');
                $row = MolajoTemplateHelper::get($this->request->get('template_name'));
                if (count($row) > 0) {
                    // error
                }
            }
        }
        $this->request->set('template_name', $row->title);
        $this->request->set('template_id', $row->extension_id);

        $this->request->set('template_asset_type_id', MOLAJO_ASSET_TYPE_EXTENSION_TEMPLATE);
        $this->request->set('template_asset_id', $row->extension_instance_asset_id);
        $this->request->set('template_view_group_id', $row->extension_instance_view_group_id);
        $this->request->set('template_language', $row->language);

        $this->request->set('template_custom_fields', $row->custom_fields);
        $this->request->set('template_metadata', $row->metadata);

        $parameters = new JRegistry;
        $parameters->loadString($row->parameters);
        $this->request->set('template_parameters', $parameters);

        if ($this->request->get('page_id', 0) == 0) {
            $this->request->set('page_id', $parameters->get('page_id', 0));
        }

        $this->request->set('template_path',
            MolajoTemplateHelper::getPath($this->request->get('template_name')));
        $this->request->set('template_path_url',
            MolajoTemplateHelper::getPathURL($this->request->get('template_name')));
        $this->request->set('template_favicon',
            MolajoTemplateHelper::loadFavicon($this->request->get('template_name')));

        return;
    }

    /**
     * _getPage
     *
     * Get Page Name using either the Page ID or the Page Name
     *
     * @return bool
     * @since 1.0
     */
    protected function _getPage()
    {
        /** Get Name */
        $this->request->set('page_name',
            MolajoExtensionHelper::getInstanceTitle($this->request->get('page_id')));

        /** Page Path */
        $viewHelper = new MolajoViewHelper($this->request->get('page_name'),
            'pages',
            $this->request->get('extension_instance_name'),
            $this->request->get('extension_type'),
            $this->request->get('extension_folder'),
            $this->request->get('template_name')
        );
        $this->request->set('page_path', $viewHelper->view_path);
        $this->request->set('page_path_url', $viewHelper->view_path_url);
        $this->request->set('page_include', $viewHelper->view_path . '/index.php');

        return;
    }

    /**
     * _getView
     *
     * Get View Paths
     *
     * @return bool
     * @since 1.0
     */
    protected function _getView()
    {
        $this->request->set('view_type', 'extensions');

        $this->request->set('view_name',
            MolajoExtensionHelper::getInstanceTitle($this->request->get('view_id')));

        $viewHelper = new MolajoViewHelper($this->request->get('view_name'),
            $this->request->get('view_type'),
            $this->request->get('extension_title'),
            $this->request->get('extension_instance_name'),
            ' ',
            $this->request->get('template_name'));
        $this->request->set('view_path', $viewHelper->view_path);
        $this->request->set('view_path_url', $viewHelper->view_path_url);

        return;
    }

    /**
     * _getWrap
     *
     * Get View Paths
     *
     * @return bool
     * @since 1.0
     */
    protected function _getWrap()
    {
        $this->request->set('wrap_name',
            MolajoExtensionHelper::getInstanceTitle($this->request->get('wrap_id')));

        $wrapHelper = new MolajoViewHelper($this->request->get('wrap_name'),
            'wraps',
            $this->request->get('extension_title'),
            $this->request->get('extension_instance_name'),
            ' ',
            $this->request->get('template_name'));
        $this->request->set('wrap_path', $wrapHelper->view_path);
        $this->request->set('wrap_path_url', $wrapHelper->view_path_url);

        return;
    }

    /**
     *  _processTask
     *
     * @return
     * @since  1.0
     */
    protected function _processTask()
    {
        return;
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
     *  _mergeParameters
     */
    protected function _mergeParameters()
    {
        return true;

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
        echo '</pre>';

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
     * _error
     *
     * Process an error condition
     *
     * @param   $code
     * @param   null $message
     * @return  mixed
     * @since   1.0
     */
    protected function _error($code, $message = null)
    {
        $this->request->set('status_error', true);
        $this->request->set('mvc_task', 'display');

        /** default error template and page */
        $this->request->set('template_id',
            MolajoController::getApplication()->get('error_template_id', 'system'));
        $this->request->set('page_id',
            MolajoController::getApplication()->get('error_page_id', 'error'));

        /** set header status, message and override template/page, if needed */
        if ($code == 503) {
            MolajoController::getApplication()->setHeader('Status',
                '503 Service Temporarily Unavailable',
                'true');
            MolajoController::getApplication()->setMessage(
                MolajoController::getApplication()->get('offline_message',
                    'This site is not available.<br /> Please check back again soon.'),
                MOLAJO_MESSAGE_TYPE_WARNING,
                503);
            $this->request->set('template_id',
                MolajoController::getApplication()->get('offline_template_id', 'system'));
            $this->request->set('page_id',
                MolajoController::getApplication()->get('offline_page_id', 'offline'));

        } else if ($code == 403) {
            MolajoController::getApplication()->setHeader('Status',
                '403 Not Authorised',
                'true');
            MolajoController::getApplication()->setMessage(
                MolajoController::getApplication()->get('error_403_message', 'Not Authorised.'),
                MOLAJO_MESSAGE_TYPE_ERROR,
                403);

        } else if ($code = 404) {
            MolajoController::getApplication()->setHeader('Status',
                '404 Not Found',
                'true');
            MolajoController::getApplication()->setMessage(
                MolajoController::getApplication()->get('error_404_message', 'Page not found.'),
                MOLAJO_MESSAGE_TYPE_ERROR,
                404);

        } else {
            MolajoController::getApplication()->setHeader('Status',
                '500 Not Found',
                'true');
            MolajoController::getApplication()->setMessage(
                MolajoController::getApplication()->get('error_500_message', 'Pass the specific error in.'),
                MOLAJO_MESSAGE_TYPE_ERROR,
                500);
        }

        return;
    }
}
