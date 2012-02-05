<?php
/**
 * @package     Molajo
 * @subpackage  Request
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Request
 *
 * @package     Molajo
 * @subpackage  Request
 * @since       1.0
 */
class MolajoControllerRequest
{
    /**
     * Application static instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

    /**
     *  Request
     *
     * @var    object
     * @since  1.0
     */
    public static $request;

    /**
     * getInstance
     *
     * Returns a reference to the global request object,
     *  only creating it if it doesn't already exist.
     *
     * @static
     * @param  JRegistry|null $config
     * @param  string $override_request_url
     * @param  string $override_asset_id
     *
     * @return bool|object
     * @since  1.0
     */
    public static function getInstance(JRegistry $request = null,
                                       $override_request_url = null,
                                       $override_asset_id = null)
    {
        if (empty(self::$instance)) {
            self::$instance = new MolajoControllerRequest($request, $override_request_url, $override_asset_id);
        }
        return self::$instance;
    }

    /**
     * __construct
     *
     * Class constructor
     *
     * @static
     * @param  JRegistry|null $config
     * @param  string $override_request_url
     * @param  string $override_asset_id
     *
     * @return null
     * @since  1.0
     */
    public function __construct(JRegistry $request = null,
                                $override_request_url = null,
                                $override_asset_id = null)
    {
        /** request object */
        if ($request instanceof JRegistry) {
            $this->request = $request;
        } else {
            $this->request = new JRegistry;
            $this->_setRequest();
        }

        /** Specific asset */
        if ((int)$override_asset_id == 0) {
            $this->set('request_asset_id', 0);
        } else {
            $this->set('request_asset_id', $override_asset_id);
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
        if ((int)Molajo::App()->get('sef_suffix', 1) == 1
            && substr($path, -11) == '/index.html'
        ) {
            $path = substr($path, 0, (strlen($path) - 11));
        }
        if ((int)Molajo::App()->get('sef_suffix', 1) == 1
            && substr($path, -5) == '.html'
        ) {
            $path = substr($path, 0, (strlen($path) - 5));
        }

        /** populate value used in query  */
        $this->set('request_url_query', $path);

        /** home: duplicate content - redirect */
        if ($this->get('request_url_query', '') == 'index.php'
            || $this->get('request_url_query', '') == 'index.php/'
            || $this->get('request_url_query', '') == 'index.php?'
            || $this->get('request_url_query', '') == '/index.php/'
        ) {
            Molajo::App()->redirect('', 301);
            return $this->request;
        }

        /** Home */
        if ($this->get('request_url_query', '') == ''
            && (int)$this->get('request_asset_id', 0) == 0
        ) {
            $this->set('request_asset_id',
                Molajo::App()->get('home_asset_id', 0));
            $this->set('request_url_home', true);
        }

        return;
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
        /** request */
        $this->set('request_url_base',
            MOLAJO_BASE_URL);
        $this->set('request_asset_id', 0);
        $this->set('request_asset_type_id', 0);
        $this->set('request_url_query', '');
        $this->set('request_url', '');
        $this->set('request_url_sef', '');
        $this->set('request_url_redirect_to_id', 0);
        $this->set('request_url_home', false);

        /** menu item data */
        $this->set('menu_item_id', 0);
        $this->set('menu_item_title', '');
        $this->set('menu_item_asset_type_id',
            MOLAJO_ASSET_TYPE_MENU_ITEM_COMPONENT);
        $this->set('menu_item_asset_id', 0);
        $this->set('menu_item_view_group_id', 0);
        $this->set('menu_item_custom_fields', array());
        $this->set('menu_item_parameters', array());
        $this->set('menu_item_metadata', array());
        $this->set('menu_item_language', '');
        $this->set('menu_item_translation_of_id', 0);

        /** primary category */
        $this->set('category_id', 0);
        $this->set('category_title', '');
        $this->set('category_asset_type_id',
            MOLAJO_ASSET_TYPE_CATEGORY_LIST);
        $this->set('category_asset_id', 0);
        $this->set('category_view_group_id', 0);
        $this->set('category_custom_fields', array());
        $this->set('category_parameters', array());
        $this->set('category_metadata', array());
        $this->set('category_language', '');
        $this->set('category_translation_of_id', 0);

        /** source data */
        $this->set('source_id', 0);
        $this->set('source_title', '');
        $this->set('source_asset_type_id', 0);
        $this->set('source_asset_id', 0);
        $this->set('source_view_group_id', 0);
        $this->set('source_custom_fields', array());
        $this->set('source_parameters', array());
        $this->set('source_metadata', array());
        $this->set('source_language', '');
        $this->set('source_translation_of_id', 0);
        $this->set('source_table', '');
        $this->set('source_last_modified', getDate());

        /** extension */
        $this->set('extension_instance_id', 0);
        $this->set('extension_instance_name', '');
        $this->set('extension_asset_type_id', 0);
        $this->set('extension_asset_id', 0);
        $this->set('extension_view_group_id', 0);
        $this->set('extension_custom_fields', array());
        $this->set('extension_metadata', array());
        $this->set('extension_parameters', array());
        $this->set('extension_path', '');
        $this->set('extension_type', '');
        $this->set('extension_folder', '');
        $this->set('extension_event_type', '');

        /** merged */
        $this->set('parameters', array());

        $this->set('metadata_title', '');
        $this->set('metadata_description', '');
        $this->set('metadata_keywords', '');
        $this->set('metadata_author', '');
        $this->set('metadata_content_rights', '');
        $this->set('metadata_robots', '');
        $this->set('metadata_additional_array', array());

        /** theme */
        $this->set('theme_id', 0);
        $this->set('theme_name', '');
        $this->set('theme_asset_type_id',
            MOLAJO_ASSET_TYPE_EXTENSION_THEME);
        $this->set('theme_asset_id', 0);
        $this->set('theme_view_group_id', 0);
        $this->set('theme_custom_fields', array());
        $this->set('theme_metadata', array());
        $this->set('theme_parameters', array());
        $this->set('theme_path', '');
        $this->set('theme_path_url', '');
        $this->set('theme_include', '');
        $this->set('theme_favicon', '');

        /** page */
        $this->set('page_view_id', 0);
        $this->set('page_view_name', '');
        $this->set('page_view_css_id', '');
        $this->set('page_view_css_class', '');
        $this->set('page_view_asset_type_id',
            MOLAJO_ASSET_TYPE_EXTENSION_PAGE_VIEW);
        $this->set('page_view_asset_id', 0);
        $this->set('page_view_path', '');
        $this->set('page_view_path_url', '');
        $this->set('page_view_include', '');

        /** template */
        $this->set('template_view_id', 0);
        $this->set('template_view_name', '');
        $this->set('template_view_css_id', '');
        $this->set('template_view_css_class', '');
        $this->set('template_view_asset_type_id',
            MOLAJO_ASSET_TYPE_EXTENSION_TEMPLATE_VIEW);
        $this->set('template_view_asset_id', 0);
        $this->set('template_view_path', '');
        $this->set('template_view_path_url', '');

        /** wrap */
        $this->set('wrap_view_id', 0);
        $this->set('wrap_view_name', '');
        $this->set('wrap_view_css_id', '');
        $this->set('wrap_view_css_class', '');
        $this->set('wrap_view_asset_type_id',
            MOLAJO_ASSET_TYPE_EXTENSION_WRAP_VIEW);
        $this->set('wrap_view_asset_id', 0);
        $this->set('wrap_view_path', '');
        $this->set('wrap_view_path_url', '');

        /** mvc parameters */
        $this->set('mvc_controller', '');
        $this->set('mvc_task', '');
        $this->set('mvc_model', '');
        $this->set('mvc_id', 0);
        $this->set('mvc_category_id', 0);
        $this->set('mvc_url_parameters', array());
        $this->set('mvc_suppress_no_results', false);

        /** results */
        $this->set('status_error', false);
        $this->set('status_authorised', false);
        $this->set('status_found', false);
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
        if (Molajo::App()->get('offline', 0) == 1) {
            $this->_error(503);

        } else {
            $this->_getAsset();
            $this->_routeRequest();
            $this->_authoriseTask();
        }

        /** display */
        if ($this->get('mvc_task') == 'add'
            || $this->get('mvc_task') == 'edit'
            || $this->get('mvc_task') == 'display'
        ) {
            $this->_renderDocument();
        }

        return;
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
        $results = AssetHelper::getAssetRequestObject($this->request);

        /** not found: exit */
        if ($results === false) {
            return $this->set('status_found', false);
        }
        $this->request = $results;

        /** menu item */
        if ($this->get('request_asset_type_id')
            == MOLAJO_ASSET_TYPE_MENU_ITEM_COMPONENT
        ) {
            $this->_getMenuItem();
        }

        /** source data */
        if ($this->get('source_id', 0) == 0) {
        } else {
            $this->_getSource();
        }

        /** primary category */
        if ($this->get('category_id', 0) == 0) {
        } else {
            $this->set('mvc_category_id',
                $this->get('category_id'));
            $this->_getPrimaryCategory();
        }

        /** extension */
        if ($this->get('extension_instance_id', 0) == 0) {

            /** 500: Extension not found */
            $this->set('status_found', false);
            Molajo::App()
                ->setMessage(
                $message = TextHelper::_('ERROR_EXTENSION_NOT_FOUND'),
                $type = MOLAJO_MESSAGE_TYPE_ERROR,
                $code = 500,
                $debug_location = 'MolajoControllerRequest::_getAsset',
                $debug_object = $this->request
            );

        } else {
            $this->_getExtension();
        }

        return $this->get('status_found');
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
        $row = ContentHelper::get(
            (int)$this->get('menu_item_id'),
            $this->get('source_table'));

        if (count($row) == 0) {
            /** 500: Extension not found */
            $this->set('status_found', false);
            Molajo::App()
                ->setMessage(
                $message = TextHelper::_('ERROR_MENU_ITEM_NOT_FOUND'),
                $type = MOLAJO_MESSAGE_TYPE_ERROR,
                $code = 500,
                $debug_location = 'MolajoControllerRequest::getMenuItem',
                $debug_object = $this->request
            );
            return $this->set('status_found', false);
        }

        $this->set('menu_item_title', $row->title);
        $this->set('menu_item_asset_type_id', $row->asset_type_id);
        $this->set('menu_item_asset_id', $row->asset_id);
        $this->set('menu_item_view_group_id', $row->view_group_id);

        $parameters = new JRegistry;
        $parameters->loadString($row->parameters);
        $this->set('menu_item_parameters', $parameters);

        $custom_fields = new JRegistry;
        $custom_fields->loadString($row->custom_fields);
        $this->set('menu_item_custom_fields', $custom_fields);

        $metadata = new JRegistry;
        $metadata->loadString($row->metadata);
        $this->set('menu_item_metadata', $metadata);

        $this->set('menu_item_language', $row->language);
        $this->set('menu_item_translation_of_id', $row->translation_of_id);

        $this->_setPageValues($this->get('menu_item_parameters',
            $this->get('menu_item_metadata')));

        /** mvc */
        if ($this->get('mvc_controller', '') == '') {
            $this->set('mvc_controller', $parameters->def('controller', ''));
        }
        if ($this->get('mvc_task', '') == '') {
            $this->set('mvc_task', $parameters->def('task', ''));
        }
        if ($this->get('extension_instance_name', '') == '') {
            $this->set('extension_instance_name', $parameters->def('option', ''));
        }
        if ($this->get('mvc_model', '') == '') {
            $this->set('mvc_model', $parameters->def('model', ''));
        }
        if ((int)$this->get('mvc_id', 0) == 0) {
            $this->set('mvc_id', $parameters->def('id', 0));
        }
        if ((int)$this->get('mvc_category_id', 0) == 0) {
            $this->set('mvc_category_id', $parameters->def('category_id', 0));
        }
        if ((int)$this->get('mvc_suppress_no_results', 0) == 0) {
            $this->set('mvc_suppress_no_results', $parameters->def('suppress_no_results', 0));
        }

        /** extension */
        $this->set('extension_instance_id', $parameters->def('extension_instance_id', 0));

        /** source */
        $this->set('source_id', $parameters->def('id', 0));

        return $this->set('status_found', true);
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
        $row = ContentHelper::get(
            (int)$this->get('source_id'),
            $this->get('source_table'));

        if (count($row) == 0) {
            /** 500: Source Content not found */
            $this->set('status_found', false);
            Molajo::App()
                ->setMessage(
                $message = TextHelper::_('ERROR_SOURCE_ITEM_NOT_FOUND'),
                $type = MOLAJO_MESSAGE_TYPE_ERROR,
                $code = 500,
                $debug_location = 'MolajoControllerRequest::_getSource',
                $debug_object = $this->request
            );
            return $this->set('status_found', false);
        }

        /** match found */
        $this->set('source_title', $row->asset_type_id);
        $this->set('source_asset_type_id', $row->asset_type_id);
        $this->set('source_asset_id', $row->asset_id);
        $this->set('source_view_group_id', $row->view_group_id);
        $this->set('source_language', $row->language);
        $this->set('source_translation_of_id', $row->translation_of_id);
        $this->set('source_last_modified', $row->modified_datetime);

        $this->set('extension_instance_id', $row->extension_instance_id);

        $custom_fields = new JRegistry;
        $custom_fields->loadString($row->custom_fields);
        $this->set('source_custom_fields', $custom_fields);

        $metadata = new JRegistry;
        $metadata->loadString($row->metadata);
        $this->set('source_metadata', $metadata);

        $parameters = new JRegistry;
        $parameters->loadString($row->parameters);
        $this->set('source_parameters', $parameters);

        /** mvc */
        if ($this->get('mvc_controller', '') == '') {
            $this->set('mvc_controller',
                $parameters->def('controller', ''));
        }
        if ($this->get('mvc_task', '') == '') {
            $this->set('mvc_task',
                $parameters->def('task', ''));
        }
        if ($this->get('extension_instance_name', '') == '') {
            $this->set('extension_instance_name',
                $parameters->def('option', ''));
        }
        if ($this->get('mvc_model', '') == '') {
            $this->set('mvc_model',
                $parameters->def('model', ''));
        }
        if ((int)$this->get('mvc_id', 0) == 0) {
            $this->set('mvc_id',
                $parameters->def('id', 0));
        }
        if ((int)$this->get('mvc_category_id', 0) == 0) {
            $this->set('mvc_category_id',
                $parameters->def('category_id', 0));
        }
        if ((int)$this->get('mvc_suppress_no_results', 0) == 0) {
            $this->set('mvc_suppress_no_results',
                $parameters->def('suppress_no_results', 0));
        }

        $this->_setPageValues($this->get('source_parameters',
            $this->get('source_metadata')));

        return $this->set('status_found', true);
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
        $row = ContentHelper::get(
            (int)$this->get(
                'category_id'
            ),
            '__content'
        );

        if (count($row) == 0) {
            /** 500: Category not found */
            $this->set('status_found', false);
            Molajo::App()
                ->setMessage(
                $message = TextHelper::_('ERROR_SOURCE_ITEM_NOT_FOUND'),
                $type = MOLAJO_MESSAGE_TYPE_ERROR,
                $code = 500,
                $debug_location = 'MolajoControllerRequest::_getSource',
                $debug_object = $this->request
            );
            return $this->set('status_found', false);
        }

        $this->set('category_title', $row->title);
        $this->set('category_asset_type_id', $row->asset_type_id);
        $this->set('category_asset_id', $row->asset_id);
        $this->set('category_view_group_id', $row->view_group_id);
        $this->set('category_language', $row->language);
        $this->set('category_translation_of_id', $row->translation_of_id);

        $custom_fields = new JRegistry;
        $custom_fields->loadString($row->custom_fields);
        $this->set('category_custom_fields', $custom_fields);

        $metadata = new JRegistry;
        $metadata->loadString($row->metadata);
        $this->set('category_metadata', $metadata);

        $parameters = new JRegistry;
        $parameters->loadString($row->parameters);
        $this->set('category_parameters', $parameters);

        $this->_setPageValues($this->get('category_parameters',
            $this->get('category_metadata')));

        return $this->set('status_found', true);
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
        $this->set('extension_asset_type_id',
            MOLAJO_ASSET_TYPE_EXTENSION_COMPONENT
        );
        $results = ExtensionHelper::getExtensionRequestObject($this->request);

        if ($results === false) {
            return $this->set('status_found', false);
        }
        $this->request = $results;

        $this->set('extension_path',
            ComponentHelper::getPath(
                strtolower($this->get('extension_instance_name'))
            )
        );

        $this->set('extension_folder',
            ComponentHelper::getPath($this->get('extension_instance_name')
            )
        );
        $this->set('extension_type', 'component');

        $this->_setPageValues($this->get('extension_parameters',
                $this->get('extension_metadata')
            )
        );

        return $this->set('status_found', true);
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

        if ((int)$this->get('theme_id', 0) == 0) {
            $this->set('theme_id',
                $params->def('theme_id', 0)
            );
        }
        if ((int)$this->get('page_view_id', 0) == 0) {
            $this->set('page_view_id',
                $params->def('page_view_id', 0)
            );
        }
        if ((int)$this->get('template_view_id', 0) == 0) {
            $this->set('template_view_id',
                $params->def('template_view_id', 0)
            );
        }
        if ((int)$this->get('wrap_view_id', 0) == 0) {
            $this->set('wrap_view_id',
                $params->def('wrap_view_id', 0)
            );
        }

        /** merge meta data */
        $meta = new JRegistry;
        $meta->loadString($metadata);

        if ($this->get('metadata_title', '') == '') {
            $this->set('metadata_title',
                $meta->def('metadata_title', '')
            );
        }
        if ($this->get('metadata_description', '') == '') {
            $this->set('metadata_description',
                $meta->def('metadata_description', '')
            );
        }
        if ($this->get('metadata_keywords', '') == '') {
            $this->set('metadata_keywords',
                $meta->def('metadata_keywords', '')
            );
        }
        if ($this->get('metadata_author', '') == '') {
            $this->set('metadata_author',
                $meta->def('metadata_author', '')
            );
        }
        if ($this->get('metadata_content_rights', '') == '') {
            $this->set('metadata_content_rights',
                $meta->def('metadata_content_rights', '')
            );
        }
        if ($this->get('metadata_robots', '') == '') {
            $this->set('metadata_robots',
                $meta->def('metadata_robots', '')
            );
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
        if ($this->get('status_found') === false) {
            $this->_error(404);
        }

        /** redirect */
        if ($this->get('request_url_redirect_to_id', 0) == 0) {
        } else {
            Molajo::App()->redirect(
                AssetHelper::getURL(
                    $this->get('request_url_redirect_to_id')), 301
            );
        }

        /** must be logged on */
        if (Molajo::App()->get('logon_requirement', 0) > 0
            && Molajo::User()->get('guest', true) === true
            && $this->get('request_asset_id')
                <> Molajo::App()->get('logon_requirement', 0)
        ) {
            Molajo::App()->redirect(
                Molajo::App()->get('logon_requirement', 0), 303
            );
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
        if ($this->get('status_error', false) === true) {
            $this->set('status_authorised', true);

        } else {
            $this->set('status_authorised',
                MolajoAccess::authoriseTask(
                    $this->get('mvc_task'),
                    $this->get('request_asset_id'))
            );
        }

        if ($this->get('status_authorised') === true) {
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
        $this->_getUser();
        $this->_getApplicationDefaults();
        $this->_getTheme();
        $this->_getPage();
        $this->_getTemplateView();
        $this->_getWrapView();
        $this->_mergeParameters();

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
        $parameters->loadString(Molajo::User()->parameters);

        if ($this->get('theme_id', 0) == 0) {
            $this->set('theme_id', $parameters->def('user_theme_id', 0));
        }
        if ($this->get('page_view_id', 0) == 0) {
            $this->set('page_view_id', $parameters->def('user_page_view_id', 0));
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
        if ($this->get('theme_id', 0) == 0) {
            $this->set('theme_id',
                Molajo::App()->get('default_theme_id', ''));
        }

        if ($this->get('page_view_id', 0) == 0) {
            $this->set('page_view_id',
                Molajo::App()->get('default_page_view_id', ''));
        }

        if ((int)$this->get('template_view_id', 0) == '') {
            $this->set('template_view_id',
                ViewHelper::getViewDefaults('view',
                    $this->get('mvc_task', ''),
                    (int)$this->get('mvc_id', 0))
            );
        }

        if ((int)$this->get('wrap_view_id', 0) == '') {
            $this->set('wrap_view_id',
                ViewHelper::getViewDefaults('wrap',
                    $this->get('mvc_task', ''),
                    (int)$this->get('mvc_id', 0))
            );
        }

        /** metadata  */
        if ($this->get('metadata_title', '') == '') {
            $this->set('metadata_title',
                Molajo::App()->get('metadata_title', '', 'metadata'));
        }
        if ($this->get('metadata_description', '') == '') {
            $this->set('metadata_description',
                Molajo::App()->get('metadata_description', '', 'metadata'));
        }
        if ($this->get('metadata_keywords', '') == '') {
            $this->set('metadata_keywords',
                Molajo::App()->get('metadata_keywords', '', 'metadata'));
        }
        if ($this->get('metadata_author', '') == '') {
            $this->set('metadata_author',
                Molajo::App()->get('metadata_author', '', 'metadata'));
        }
        if ($this->get('metadata_content_rights', '') == '') {
            $this->set('metadata_content_rights',
                Molajo::App()->get('metadata_content_rights', '', 'metadata'));
        }
        if ($this->get('metadata_robots', '') == '') {
            $this->set('metadata_robots',
                Molajo::App()->get('metadata_robots', '', 'metadata'));
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
        $row = ThemeHelper::get($this->get('theme_id'));

        if (count($row) == 0) {
            if ($this->set('theme_name') == 'system') {
                // error
            } else {
                $this->set('theme_name', 'system');
                $row = ThemeHelper::get($this->get('theme_name'));
                if (count($row) > 0) {
                    // error
                }
            }
        }
        $this->set('theme_name', $row->title);
        $this->set('theme_id', $row->extension_instance_id);

        $this->set('theme_asset_type_id', MOLAJO_ASSET_TYPE_EXTENSION_THEME);
        $this->set('theme_asset_id', $row->asset_id);
        $this->set('theme_view_group_id', $row->view_group_id);
        $this->set('theme_language', $row->language);

        $this->set('theme_custom_fields', $row->custom_fields);
        $this->set('theme_metadata', $row->metadata);

        $parameters = new JRegistry;
        $parameters->loadString($row->parameters);
        $this->set('theme_parameters', $parameters);

        if ($this->get('page_view_id', 0) == 0) {
            $this->set('page_view_id', $parameters->get('page_view_id', 0));
        }

        $this->set('theme_path',
            ThemeHelper::getPath($this->get('theme_name')));
        $this->set('theme_path_url',
            ThemeHelper::getPathURL($this->get('theme_name')));
        $this->set('theme_favicon',
            ThemeHelper::getFavicon($this->get('theme_name')));

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
        $this->set('page_view_name',
            ExtensionHelper::getInstanceTitle(
                $this->get('page_view_id'),
                MOLAJO_ASSET_TYPE_EXTENSION_PAGE_VIEW,
                'pages'
            )
        );

        /** Page Path */
        $viewHelper = new MolajoViewHelper(
            $this->get('page_view_name'),
            'pages',
            $this->get('extension_instance_name'),
            $this->get('extension_type'),
            $this->get('extension_subtype'),
            $this->get('theme_name')
        );
        $this->set('page_view_path', $viewHelper->view_path);
        $this->set('page_view_path_url', $viewHelper->view_path_url);
        $this->set('page_view_include', $viewHelper->view_path . '/index.php');

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
        $this->set(
            'template_view_name',
            ExtensionHelper::getInstanceTitle(
                $this->get('template_view_id')
            )
        );

        $viewHelper = new MolajoViewHelper(
            $this->get('template_view_name'),
            $this->get('view_type'),
            $this->get('extension_title'),
            $this->get('extension_instance_name'),
            ' ',
            $this->get('theme_name')
        );
        $this->set('template_view_path', $viewHelper->view_path);
        $this->set('template_view_path_url', $viewHelper->view_path_url);

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
        $this->set(
            'wrap_view_name',
            ExtensionHelper::getInstanceTitle(
                $this->get('wrap_view_id')
            )
        );

        $wrapHelper = new MolajoViewHelper(
            $this->get('wrap_view_name'),
            'wraps',
            $this->get('extension_title'),
            $this->get('extension_instance_name'),
            ' ',
            $this->get('theme_name')
        );
        $this->set('wrap_view_path', $wrapHelper->view_path);
        $this->set('wrap_view_path_url', $wrapHelper->view_path_url);

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
        $db = Molajo::DB();
        $query = $db->getQuery(true);

        if ((int)$asset_id == Molajo::App()->get('home_asset_id', 0)) {
            return '';
        }

        if (Molajo::App()->get('sef', 1) == 0) {
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
        $temp = json_decode($this->get('source_parameters'));
        $parameters = $this->_merge($parameters, $temp);

        /** category parameters */
        $temp = array();
        $temp = json_decode($this->get('category_parameters'));
        $parameters = $this->_merge($parameters, $temp);

        /** extension parameters */
        $temp = array();
        $temp = json_decode($this->get('extension_parameters'));

        $this->parameters = $this->_merge($parameters, $temp);
        /**
        echo '<pre>';
        var_dump($this->parameters);
        echo '</pre>';
         */
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
        $this->set('status_error', true);
        $this->set('mvc_task', 'display');

        /** default error theme and page */
        $this->set(
            'theme_id',
            Molajo::App()->get(
                'error_theme_id',
                'system'
            )
        );
        $this->set(
            'page_view_id',
            Molajo::App()->get(
                'error_page_view_id',
                'error'
            )
        );

        /** set header status, message and override theme/page, if needed */
        if ($code == 503) {
            Molajo::App()->setHeader(
                'Status',
                '503 Service Temporarily Unavailable',
                'true'
            );
            Molajo::App()->setMessage(
                Molajo::App()->get(
                    'offline_message',
                    'This site is not available.<br /> Please check back again soon.'
                ),
                MOLAJO_MESSAGE_TYPE_WARNING,
                503
            );
            $this->set('theme_id',
                Molajo::App()->get(
                    'offline_theme_id',
                    'system'
                )
            );
            $this->set('page_view_id',
                Molajo::App()->get(
                    'offline_page_view_id',
                    'offline'
                )
            );

        } else if ($code == 403) {
            Molajo::App()->setHeader(
                'Status',
                '403 Not Authorised',
                'true'
            );
            Molajo::App()->setMessage(
                Molajo::App()->get(
                    'error_403_message',
                    'Not Authorised.'
                ),
                MOLAJO_MESSAGE_TYPE_ERROR,
                403
            );

        } else if ($code = 404) {
            Molajo::App()->setHeader(
                'Status',
                '404 Not Found',
                'true'
            );
            Molajo::App()->setMessage(
                Molajo::App()->get(
                    'error_404_message',
                    'Page not found.'
                ),
                MOLAJO_MESSAGE_TYPE_ERROR,
                404
            );

        } else {
            Molajo::App()->setHeader(
                'Status',
                '500 Not Found',
                'true'
            );
            Molajo::App()->setMessage(
                Molajo::App()->get(
                    'error_500_message',
                    'Pass the specific error in.'
                ),
                MOLAJO_MESSAGE_TYPE_ERROR,
                500
            );
        }
        return;
    }

    /**
     * get
     *
     * Returns a property of the Application object
     * or the default value if the property is not set.
     *
     * @param   string  $key      The name of the property.
     * @param   mixed   $default  The default value (optional) if none is set.
     *
     * @return  mixed   The value of the configuration.
     *
     * @since   1.0
     */
    public function get($key, $default = null)
    {
        return $this->request->get($key, $default);
    }

    /**
     * set
     *
     * Modifies a property of the Request object, creating it if it does not already exist.
     *
     * @param   string  $key    The name of the property.
     * @param   mixed   $value  The value of the property to set (optional).
     *
     * @return  mixed   Previous value of the property
     *
     * @since   1.0
     */
    public function set($key, $value = null)
    {
        return $this->request->set($key, $value);
    }
}
