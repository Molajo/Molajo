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
     * @var    object
     * @since  1.0
     */
    public $request;

    /**
     * __construct
     *
     * Class constructor
     *
     * @param   null  $override_request_url
     * @param   null  $asset_id
     *
     * @return  object
     *
     * @since  1.0
     */
    public function __construct($override_request_url = null, $asset_id = null)
    {
        $this->_setRequest();

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

        return $this->request;
    }

    /**
     * _setRequest
     *
     * Create and Initialize the request
     *
     * Request Object which is passed on to Document, Renderers and the MVC classes
     *
     * @static
     * @return array
     * @since 1.0
     */
    private function _setRequest()
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
        $this->request->set('metadata_keywords', '');
        $this->request->set('metadata_author', '');
        $this->request->set('metadata_content_rights', '');
        $this->request->set('metadata_robots', '');
        $this->request->set('metadata_additional_array', array());

        /** theme */
        $this->request->set('theme_id', 0);
        $this->request->set('theme_name', '');
        $this->request->set('theme_asset_type_id',
            MOLAJO_ASSET_TYPE_EXTENSION_THEME);
        $this->request->set('theme_asset_id', 0);
        $this->request->set('theme_view_group_id', 0);
        $this->request->set('theme_custom_fields', array());
        $this->request->set('theme_metadata', array());
        $this->request->set('theme_parameters', array());
        $this->request->set('theme_path', '');
        $this->request->set('theme_path_url', '');
        $this->request->set('theme_include', '');
        $this->request->set('theme_favicon', '');

        /** page */
        $this->request->set('page_view_id', 0);
        $this->request->set('page_view_name', '');
        $this->request->set('page_view_css_id', '');
        $this->request->set('page_view_css_class', '');
        $this->request->set('page_view_asset_type_id',
            MOLAJO_ASSET_TYPE_EXTENSION_PAGE_VIEW);
        $this->request->set('page_view_asset_id', 0);
        $this->request->set('page_view_path', '');
        $this->request->set('page_view_path_url', '');
        $this->request->set('page_view_include', '');

        /** template */
        $this->request->set('template_view_id', 0);
        $this->request->set('template_view_name', '');
        $this->request->set('template_view_css_id', '');
        $this->request->set('template_view_css_class', '');
        $this->request->set('template_view_asset_type_id',
            MOLAJO_ASSET_TYPE_EXTENSION_TEMPLATE_VIEW);
        $this->request->set('template_view_asset_id', 0);
        $this->request->set('template_view_path', '');
        $this->request->set('template_view_path_url', '');

        /** wrap */
        $this->request->set('wrap_view_id', 0);
        $this->request->set('wrap_view_name', '');
        $this->request->set('wrap_view_css_id', '');
        $this->request->set('wrap_view_css_class', '');
        $this->request->set('wrap_view_asset_type_id',
            MOLAJO_ASSET_TYPE_EXTENSION_WRAP_VIEW);
        $this->request->set('wrap_view_asset_id', 0);
        $this->request->set('wrap_view_path', '');
        $this->request->set('wrap_view_path_url', '');

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
            $this->request->set('mvc_controller', $parameters->def('controller', ''));
        }
        if ($this->request->get('mvc_task', '') == '') {
            $this->request->set('mvc_task', $parameters->def('task', ''));
        }
        if ($this->request->get('extension_instance_name', '') == '') {
            $this->request->set('extension_instance_name', $parameters->def('option', ''));
        }
        if ($this->request->get('mvc_model', '') == '') {
            $this->request->set('mvc_model', $parameters->def('model', ''));
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
            $this->request->set('mvc_controller', $parameters->def('controller', ''));
        }
        if ($this->request->get('mvc_task', '') == '') {
            $this->request->set('mvc_task', $parameters->def('task', ''));
        }
        if ($this->request->get('extension_instance_name', '') == '') {
            $this->request->set('extension_instance_name', $parameters->def('option', ''));
        }
        if ($this->request->get('mvc_model', '') == '') {
            $this->request->set('mvc_model', $parameters->def('model', ''));
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
     * Retrieve extension information for Component Request
     *
     * @return bool
     * @since 1.0
     */
    protected function _getExtension()
    {
        $this->request->set('extension_asset_type_id', MOLAJO_ASSET_TYPE_EXTENSION_COMPONENT);
        $results = MolajoExtensionHelper::getExtensionRequestObject($this->request);

        if ($results === false) {
            return $this->request->set('status_found', false);
        }
        $this->request = $results;

        $this->request->set('extension_path',
            MolajoComponentHelper::getPath(
                strtolower($this->request->get('extension_instance_name'))));

        $this->request->set('extension_folder',
            MolajoComponentHelper::getPath($this->request->get('extension_instance_name')));
        $this->request->set('extension_type', 'component');

        $this->_setPageValues($this->request->get('extension_parameters',
            $this->request->get('extension_metadata')));

        return $this->request->set('status_found', true);
    }

    /**
     * _setPageValues
     *
     * Set the values needed to generate the page (theme, page, view, wrap, and various metadata)
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

        if ((int)$this->request->get('theme_id', 0) == 0) {
            $this->request->set('theme_id', $params->def('theme_id', 0));
        }
        if ((int)$this->request->get('page_view_id', 0) == 0) {
            $this->request->set('page_view_id', $params->def('page_view_id', 0));
        }
        if ((int)$this->request->get('template_view_id', 0) == 0) {
            $this->request->set('template_view_id', $params->def('template_view_id', 0));
        }
        if ((int)$this->request->get('wrap_view_id', 0) == 0) {
            $this->request->set('wrap_view_id', $params->def('wrap_view_id', 0));
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
                $this->request->get('mvc_asset_id'))
        );

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
        $this->_getTheme();
        $this->_getPage();
        $this->_getTemplateView();
        $this->_getWrapView();
        $this->_mergeParameters();

        /** Render Document */
        new MolajoDocument ($this->request);

        return;
    }

    /**
     * _getUser
     *
     * Get Theme Name using either the Theme ID or the Theme Name
     *
     * @return bool
     * @since 1.0
     */
    protected function _getUser()
    {
        $parameters = new JRegistry;
        $parameters->loadString(MolajoController::getUser()->parameters);

        if ($this->request->get('theme_id', 0) == 0) {
            $this->request->set('theme_id', $parameters->def('user_theme_id', 0));
        }
        if ($this->request->get('page_view_id', 0) == 0) {
            $this->request->set('page_view_id', $parameters->def('user_page_view_id', 0));
        }

        return;
    }

    /**
     *  _getApplicationDefaults
     *
     *  Retrieve Theme and Page from the final level of default values, if needed
     *
     * @return bool
     * @since 1.0
     */
    protected function _getApplicationDefaults()
    {
        if ($this->request->get('theme_id', 0) == 0) {
            $this->request->set('theme_id',
                MolajoController::getApplication()->get('default_theme_id', ''));
        }

        if ($this->request->get('page_view_id', 0) == 0) {
            $this->request->set('page_view_id',
                MolajoController::getApplication()->get('default_page_view_id', ''));
        }

        if ((int)$this->request->get('template_view_id', 0) == '') {
            $this->request->set('template_view_id',
                MolajoViewHelper::getViewDefaults('view',
                    $this->request->get('mvc_task', ''),
                    (int)$this->request->get('mvc_id', 0))
            );
        }

        if ((int)$this->request->get('wrap_view_id', 0) == '') {
            $this->request->set('wrap_view_id',
                MolajoViewHelper::getViewDefaults('wrap',
                    $this->request->get('mvc_task', ''),
                    (int)$this->request->get('mvc_id', 0))
            );
        }

        /** metadata  */
        if ($this->request->get('metadata_title', '') == '') {
            $this->request->set('metadata_title',
                MolajoController::getApplication()->get('metadata_title', '', 'metadata'));
        }
        if ($this->request->get('metadata_description', '') == '') {
            $this->request->set('metadata_description',
                MolajoController::getApplication()->get('metadata_description', '', 'metadata'));
        }
        if ($this->request->get('metadata_keywords', '') == '') {
            $this->request->set('metadata_keywords',
                MolajoController::getApplication()->get('metadata_keywords', '', 'metadata'));
        }
        if ($this->request->get('metadata_author', '') == '') {
            $this->request->set('metadata_author',
                MolajoController::getApplication()->get('metadata_author', '', 'metadata'));
        }
        if ($this->request->get('metadata_content_rights', '') == '') {
            $this->request->set('metadata_content_rights',
                MolajoController::getApplication()->get('metadata_content_rights', '', 'metadata'));
        }
        if ($this->request->get('metadata_robots', '') == '') {
            $this->request->set('metadata_robots',
                MolajoController::getApplication()->get('metadata_robots', '', 'metadata'));
        }
        return;
    }

    /**
     * _getTheme
     *
     * Get Theme Name using either the Theme ID or the Theme Name
     *
     * @return bool
     * @since 1.0
     */
    protected function _getTheme()
    {
        $row = MolajoThemeHelper::get($this->request->get('theme_id'));

        if (count($row) == 0) {
            if ($this->request->set('theme_name') == 'system') {
                // error
            } else {
                $this->request->set('theme_name', 'system');
                $row = MolajoThemeHelper::get($this->request->get('theme_name'));
                if (count($row) > 0) {
                    // error
                }
            }
        }
        $this->request->set('theme_name', $row->title);
        $this->request->set('theme_id', $row->extension_id);

        $this->request->set('theme_asset_type_id', MOLAJO_ASSET_TYPE_EXTENSION_THEME);
        $this->request->set('theme_asset_id', $row->asset_id);
        $this->request->set('theme_view_group_id', $row->view_group_id);
        $this->request->set('theme_language', $row->language);

        $this->request->set('theme_custom_fields', $row->custom_fields);
        $this->request->set('theme_metadata', $row->metadata);

        $parameters = new JRegistry;
        $parameters->loadString($row->parameters);
        $this->request->set('theme_parameters', $parameters);

        if ($this->request->get('page_view_id', 0) == 0) {
            $this->request->set('page_view_id', $parameters->get('page_view_id', 0));
        }

        $this->request->set('theme_path',
            MolajoThemeHelper::getPath($this->request->get('theme_name')));
        $this->request->set('theme_path_url',
            MolajoThemeHelper::getPathURL($this->request->get('theme_name')));
        $this->request->set('theme_favicon',
            MolajoThemeHelper::getFavicon($this->request->get('theme_name')));

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
        $this->request->set('page_view_name',
            MolajoExtensionHelper::getInstanceTitle(
                $this->request->get('page_view_id'),
                MOLAJO_ASSET_TYPE_EXTENSION_PAGE_VIEW,
                'pages'
            )
        );

        /** Page Path */
        $viewHelper = new MolajoViewHelper(
            $this->request->get('page_view_name'),
            'pages',
            $this->request->get('extension_instance_name'),
            $this->request->get('extension_type'),
            $this->request->get('extension_subtype'),
            $this->request->get('theme_name')
        );
        $this->request->set('page_view_path', $viewHelper->view_path);
        $this->request->set('page_view_path_url', $viewHelper->view_path_url);
        $this->request->set('page_view_include', $viewHelper->view_path . '/index.php');

        return;
    }

    /**
     * _getTemplateView
     *
     * Get Template View Paths
     *
     * @return bool
     * @since 1.0
     */
    protected function _getTemplateView()
    {
        $this->request->set(
            'template_view_name',
            MolajoExtensionHelper::getInstanceTitle(
                $this->request->get('template_view_id')
            )
        );

        $viewHelper = new MolajoViewHelper(
            $this->request->get('template_view_name'),
            $this->request->get('view_type'),
            $this->request->get('extension_title'),
            $this->request->get('extension_instance_name'),
            ' ',
            $this->request->get('theme_name')
        );
        $this->request->set('template_view_path', $viewHelper->view_path);
        $this->request->set('template_view_path_url', $viewHelper->view_path_url);

        return;
    }

    /**
     * _getWrapView
     *
     * Get View Paths
     *
     * @return bool
     * @since 1.0
     */
    protected function _getWrapView()
    {
        $this->request->set(
            'wrap_view_name',
            MolajoExtensionHelper::getInstanceTitle(
                $this->request->get('wrap_view_id')
            )
        );

        $wrapHelper = new MolajoViewHelper(
            $this->request->get('wrap_view_name'),
            'wraps',
            $this->request->get('extension_title'),
            $this->request->get('extension_instance_name'),
            ' ',
            $this->request->get('theme_name')
        );
        $this->request->set('wrap_view_path', $wrapHelper->view_path);
        $this->request->set('wrap_view_path_url', $wrapHelper->view_path_url);

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

        /** default error theme and page */
        $this->request->set(
            'theme_id',
            MolajoController::getApplication()->get(
                'error_theme_id',
                'system'
            )
        );
        $this->request->set(
            'page_view_id',
            MolajoController::getApplication()->get(
                'error_page_view_id',
                'error'
            )
        );

        /** set header status, message and override theme/page, if needed */
        if ($code == 503) {
            MolajoController::getApplication()->setHeader(
                'Status',
                '503 Service Temporarily Unavailable',
                'true'
            );
            MolajoController::getApplication()->setMessage(
                MolajoController::getApplication()->get(
                    'offline_message',
                    'This site is not available.<br /> Please check back again soon.'
                ),
                MOLAJO_MESSAGE_TYPE_WARNING,
                503
            );
            $this->request->set('theme_id',
                MolajoController::getApplication()->get(
                    'offline_theme_id',
                    'system'
                )
            );
            $this->request->set('page_view_id',
                MolajoController::getApplication()->get(
                    'offline_page_view_id',
                    'offline'
                )
            );

        } else if ($code == 403) {
            MolajoController::getApplication()->setHeader(
                'Status',
                '403 Not Authorised',
                'true'
            );
            MolajoController::getApplication()->setMessage(
                MolajoController::getApplication()->get(
                    'error_403_message',
                    'Not Authorised.'
                ),
                MOLAJO_MESSAGE_TYPE_ERROR,
                403
            );

        } else if ($code = 404) {
            MolajoController::getApplication()->setHeader(
                'Status',
                '404 Not Found',
                'true'
            );
            MolajoController::getApplication()->setMessage(
                MolajoController::getApplication()->get(
                    'error_404_message',
                    'Page not found.'
                ),
                MOLAJO_MESSAGE_TYPE_ERROR,
                404
            );

        } else {
            MolajoController::getApplication()->setHeader(
                'Status',
                '500 Not Found',
                'true'
            );
            MolajoController::getApplication()->setMessage(
                MolajoController::getApplication()->get(
                    'error_500_message',
                    'Pass the specific error in.'
                ),
                MOLAJO_MESSAGE_TYPE_ERROR,
                500
            );
        }
        return;
    }
}
