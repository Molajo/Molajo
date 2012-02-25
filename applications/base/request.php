<?php
/**
 * @package     Molajo
 * @subpackage  Base
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
class MolajoRequest
{
    /**
     * $instance
     *
     * Application static instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

    /**
     * $redirect_to_id
     *
     * Redirect the Tequest URL to Asset ID
     *
     * @var    object
     * @since  1.0
     */
    protected $redirect_to_id;

    /**
     * $page_request
     *
     * Page Request object that will be populated by this class
     * with overall processing requirements for the page
     *
     * Access via Molajo::Request()->get('property')
     *
     * @var    object
     * @since  1.0
     */
    public $page_request;

    /**
     * $parameters
     *
     * Parameters for source, menu item, extension, category,
     * and application are merged into one set where the most
     * detailed value (source or menu item) takes precedence.
     *
     * Access via Molajo::Request()->getParameter('property')
     *
     * @var    object
     * @since  1.0
     */
    public $parameters;

    /**
     * $input
     *
     * Application Request Object
     *
     * @var    object
     * @since  1.0
     */
    public $input;

    /**
     * getInstance
     *
     * Returns a reference to the global request object,
     *  only creating it if it doesn't already exist.
     *
     * @static
     * @param  string  $override_request_url
     * @param  string  $override_asset_id
     *
     * @return object
     * @since  1.0
     */
    public static function getInstance($override_request_url = null,
                                       $override_asset_id = null)
    {
        if (empty(self::$instance)) {
            self::$instance =
                new MolajoRequest(
                    $override_request_url,
                    $override_asset_id
                );
        }
        return self::$instance;
    }

    /**
     * __construct
     *
     * Class constructor
     *
     * @static
     * @param  string $override_request_url
     * @param  string $override_asset_id
     *
     * @return mixed
     * @since  1.0
     */
    public function __construct($override_request_url = null,
                                $override_asset_id = null)
    {
        $this->_initialize();

        $this->redirect_to_id = 0;

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
        if ((int)Services::Configuration()->get('sef_suffix', 1) == 1
            && substr($path, -11) == '/index.html'
        ) {
            $path = substr($path, 0, (strlen($path) - 11));
        }
        if ((int)Services::Configuration()->get('sef_suffix', 1) == 1
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
            Molajo::Responder()->redirect('', 301);
        }

        /** Home */
        if ($this->get('request_url_query', '') == ''
            && (int)$this->get('request_asset_id', 0) == 0
        ) {
            $this->set('request_asset_id',
                Services::Configuration()->get('home_asset_id', 0));
            $this->set('request_url_home', true);
        }

        return $this;
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
        return $this->page_request->get($key, $default);
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
        return $this->page_request->set($key, $value);
    }

    /**
     * getAll
     *
     * Returns an array of all entries in the $page_request Registry
     *
     * @return array
     */
    public function getAll($type = null)
    {
        if ($type == 'array') {
            $r = new Registry($this->page_request);
            $temp = $r->toArray();
            $newArray = array();
            foreach ($temp as $item) {
                foreach ($item as $key => $value) {
                    $newArray[$key] = $value;
                }
            }
            return $newArray;
        } else {
            return $this->page_request;
        }
    }

    /**
     * process
     *
     * Using the MOLAJO_PAGE_REQUEST value,
     *  retrieve the asset record,
     *  set the variables needed to render output,
     *  execute document renders and MVC
     *
     * @return  null
     * @since   1.0
     */
    public function process()
    {
        /** offline */
        if (Services::Configuration()->get('offline', 0) == 1) {
            $this->_error(503);
        }

        /** URL parameters */
        $this->_getInput();

        /** Asset, Access Control, links to source, menus, extensions, etc. */
        $this->_getAsset();

        /** Authorise */
        if ($this->get('status_found')) {
            $this->_authoriseTask();
        }

        /** Route */
        $this->_routeRequest();

        /** Action: Render Page */
        if ($this->get('mvc_controller') == 'display'
        ) {
            $this->_getUser();
            $this->_getApplicationDefaults();
            $this->_getTheme();
            $this->_getPage();
            $this->_getTemplateView();
            $this->_getWrapView();

            $temp = new Registry();
            $temp->loadArray($this->parameters);
            $this->parameters = $temp;

        } else {

            /** Action: Database action */
            $temp = new Registry();
            $temp->loadArray($this->parameters);
            $this->parameters = $temp;

            if (Services::Configuration()->get('sef', 1) == 0) {
                $link = $this->page_request->get('request_url_sef');
            } else {
                $link = $this->page_request->get('request_url');
            }
            $this->set('redirect_on_failure', $link);

            $this->set('model', 'Molajo' . ucfirst(trim($this->get('mvc_model'))) . 'Model');
            $cc = 'Molajo' . ucfirst($this->get('mvc_controller')) . 'Controller';
            $this->set('controller', $cc);
            $task = $this->get('mvc_task');
            $this->set('task', $task);
            $this->set('id', $this->get('mvc_id'));
            $controller = new $cc($this->page_request, $this->parameters);

            /** execute task: non-display, edit, or add tasks */
            return $controller->$task();
        }

        return;
    }

    /**
     * _getInput
     *
     * Retrieve URL contents
     *
     * @return null
     * @since  1.0
     */
    protected function _getInput()
    {
        /** extract URL variables */
        $this->input = array();

        $uri = clone JURI::getInstance();

        $results = $uri->get('_vars');
        foreach ($results as $key => $value) {
            $this->input[$key] = $value;
        }

        if (count($this->input) > 0
            && isset($this->input['task'])
        ) {
            $this->set('mvc_task', $this->input['task']);
        } else {
            $this->set('mvc_task', 'display');
        }

        if ($this->get('mvc_task', '') == ''
            || $this->get('mvc_task', 'display') == 'display'
        ) {
            $pageRequest = $this->get('request_url_query');

            if (strripos($pageRequest, '/edit') == (strlen($pageRequest) - 5)) {
                $pageRequest = substr($pageRequest, 0, strripos($pageRequest, '/edit'));
                $this->set('request_url_query', $pageRequest);
                $this->set('mvc_task', 'edit');

            } else if (strripos($pageRequest, '/add') == (strlen($pageRequest) - 4)) {
                $pageRequest = substr($pageRequest, 0, strripos($pageRequest, '/add'));
                $this->set('request_url_query', $pageRequest);
                $this->set('mvc_task', 'add');

            } else {
                $this->set('mvc_task', 'display');
            }
            return;
        }

        /** return */
        if (isset($this->input['return'])) {
            $return = $this->input['return'];
        } else {
            $return = '';
        }

        if (trim($return) == '') {
            $this->set('redirect_on_success', '');
        } else if (JUri::isInternal(base64_decode($return))) {
            $this->set('redirect_on_success', base64_decode($return));
        } else {
            $this->set('redirect_on_success', '');
        }

        /** option */
        $this->set('mvc_option', (string)$this->input['option']);

        /** asset information */
        $this->set('mvc_id', (int)$this->input['id']);

        return true;
    }

    /**
     * _getAsset
     *
     * Retrieve Asset and Asset Type data for a specific asset id
     * or query request
     *
     * @return   boolean
     * @since    1.0
     */
    protected function _getAsset()
    {
        $row = AssetHelper::get(
            (int)$this->get('request_asset_id'),
            $this->get('request_url_query'),
            $this->get('mvc_option'),
            $this->get('mvc_id')
        );

        /** 404: _routeRequest handles redirecting to error page */
        if (count($row) == 0
            || (int)$row->routable == 0
        ) {
            return $this->set('status_found', false);
        }

        /** Redirect: _routeRequest handles rerouting the request */
        if ((int)$row->redirect_to_id == 0) {
        } else {
            $this->redirect_to_id = (int)$row->redirect_to_id;
            return $this->set('status_found', false);
        }

        /** 403: _authoriseTask handles redirecting to error page */
        if (in_array($row->view_group_id, Services::User()->get('view_groups'))) {
            $this->set('status_authorised', true);
        } else {
            return $this->set('status_authorised', false);
        }

        /** request url */
        $this->set('request_asset_id', (int)$row->asset_id);
        $this->set('request_asset_type_id', (int)$row->asset_type_id);
        $this->set('request_url', $row->request);
        $this->set('request_url_sef', $row->sef_request);

        /** home */
        if ((int)$this->get('request_asset_id', 0)
            == Services::Configuration()->get('home_asset_id', null)
        ) {
            $this->set('request_url_home', true);
        } else {
            $this->set('request_url_home', false);
        }

        $this->set('source_table', $row->source_table);
        $this->set('category_id', (int)$row->primary_category_id);

        /** mvc options and url parameters */
        $this->set('extension_instance_name', $row->request_option);
        $this->set('mvc_model', $row->request_model);
        $this->set('mvc_id', (int)$row->source_id);

        $this->set('mvc_controller',
            Services::Access()
                ->getTaskController($this->get('mvc_task'))
        );

        /** Action Tasks need no additional information */
        if ($this->get('mvc_controller') == 'display') {
        } else {
            return $this->set('status_found', true);
        }

        if ($this->get('request_asset_type_id')
            == MOLAJO_ASSET_TYPE_MENU_ITEM_COMPONENT
        ) {
            $this->set('menu_item_id', $row->source_id);
            $this->_getMenuItem();
            if ($this->get('status_found') === false) {
                return $this->get('status_found');
            }
        } else {
            $this->set('source_id', $row->source_id);
            $this->_getSource();
        }

        /** primary category */
        if ($this->get('category_id', 0) == 0) {
        } else {
            $this->set('mvc_category_id', $this->get('category_id'));
            $this->_getPrimaryCategory();
        }

        /** Extension */
        $this->_getExtension();

        return $this->get('status_found');
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
        /** display view verified in _getAsset */
        if ($this->get('mvc_task') == 'display'
            && $this->get('status_authorised') === true
        ) {
            return true;
        }
        if ($this->get('mvc_task') == 'display'
            && $this->get('status_authorised') === false
        ) {
            $this->_error(403);
        }

        /** verify other tasks */
        $this->set('status_authorised',
            Services::Access()
                ->authoriseTask(
                $this->get('mvc_task'),
                $this->get('request_asset_id')
            )
        );

        if ($this->get('status_authorised') === true) {
        } else {
            $this->_error(403);
        }
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
        if ($this->redirect_to_id == 0) {
        } else {
            Molajo::Responder()->redirect(
                AssetHelper::getURL($this->redirect_to_id),
                301
            );
        }

        /** must be logged on */
        if (Services::Configuration()->get('logon_requirement', 0) > 0
            && Services::User()->get('guest', true) === true
            && $this->get('request_asset_id')
                <> Services::Configuration()->get('logon_requirement', 0)
        ) {
            Molajo::Responder()->redirect(Services::Configuration()->get('logon_requirement', 0), 303);
        }

        return;
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
        $row = MenuItemHelper::get(
            (int)$this->get('menu_item_id')
        );

        /**
         *  403: Unauthorised Access
         *
         *  If the menu item doesn't return, it's likely that the user, application
         *  or site do not have access to the menu (extension_instance).
         *
         *  Since the asset record was found, it is likely not a 404
         *
         *  Will be treating like a 404 for now
         *
         *  _authoriseTask handles redirecting to error page
         */
        if (count($row) == 0) {
            $this->set('status_authorised', false);
            return $this->set('status_found', false);
        }

        $this->set('menu_item_title', $row->menu_item_title);
        $this->set('menu_item_asset_type_id', $row->menu_item_asset_type_id);
        $this->set('menu_item_asset_id', $row->menu_item_asset_id);
        $this->set('menu_item_view_group_id', $row->menu_item_view_group_id);

        $this->set('extension_instance_id', $row->menu_id);
        $this->set('extension_instance_name', $row->menu_title);
        $this->set('extension_instance_asset_type_id', $row->menu_asset_type_id);
        $this->set('extension_instance_asset_id', $row->menu_asset_id);
        $this->set('extension_instance_view_group_id', $row->menu_view_group_id);

        $parameters = new Registry;
        $parameters->loadString($row->menu_item_parameters);
        $this->set('menu_item_parameters', $parameters);

        $custom_fields = new Registry;
        $custom_fields->loadString($row->menu_item_custom_fields);
        $this->set('menu_item_custom_fields', $custom_fields);

        $metadata = new Registry;
        $metadata->loadString($row->menu_item_metadata);
        $this->set('menu_item_metadata', $metadata);

        $this->_setPageValues($parameters, $metadata);

        $this->set('menu_item_language', $row->menu_item_language);
        $this->set('menu_item_translation_of_id', $row->menu_item_translation_of_id);

        /** mvc */
        if ($this->get('mvc_controller', '') == '') {
            $this->set('mvc_controller',
                $parameters->get('controller', '')
            );
        }
        if ($this->get('mvc_task', '') == '') {
            $this->set('mvc_task',
                $parameters->get('task', '')
            );
        }
        if ($this->get('extension_instance_name', '') == '') {
            $this->set('extension_instance_name',
                $parameters->get('option', '')
            );
        }
        if ($this->get('mvc_model', '') == '') {
            $this->set('mvc_model',
                $parameters->get('model', '')
            );
        }
        if ((int)$this->get('mvc_id', 0) == 0) {
            $this->set('mvc_id', $parameters->get('id', 0));
        }
        if ((int)$this->get('mvc_category_id', 0) == 0) {
            $this->set('mvc_category_id',
                $parameters->get('category_id', 0)
            );
        }
        if ((int)$this->get('mvc_suppress_no_results', 0) == 0) {
            $this->set('mvc_suppress_no_results',
                $parameters->get('suppress_no_results', 0)
            );
        }

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
            return true;
        }
        //        if (count($row) == 0) {
        //            /** 500: Source Content not found */
        //            $this->set('status_found', false);
        //            Services::Message()
        //                ->set(
        //                $message = Services::Language()->_('ERROR_SOURCE_ITEM_NOT_FOUND'),
        //                $type = MOLAJO_MESSAGE_TYPE_ERROR,
        //                $code = 500,
        //                $debug_location = 'MolajoRequest::_getSource',
        //                $debug_object = $this->page_request
        //            );
        //            return $this->set('status_found', false);
        //        }

        /** match found */
        $this->set('source_title', $row->title);
        $this->set('source_asset_type_id', $row->asset_type_id);
        $this->set('source_asset_id', $row->asset_id);
        $this->set('source_view_group_id', $row->view_group_id);
        $this->set('source_language', $row->language);
        $this->set('source_translation_of_id', $row->translation_of_id);
        $this->set('source_last_modified', $row->modified_datetime);

        $this->set('extension_instance_id', $row->extension_instance_id);

        $custom_fields = new Registry;
        $custom_fields->loadString($row->custom_fields);
        $this->set('source_custom_fields', $custom_fields);

        $metadata = new Registry;
        $metadata->loadString($row->metadata);
        $this->set('source_metadata', $metadata);

        $parameters = new Registry;
        $parameters->loadString($row->parameters);
        $parameters->set('id', $row->id);
        $parameters->set('asset_type_id', $row->asset_type_id);
        $this->set('source_parameters', $parameters);

        $this->_setPageValues($parameters, $metadata);

        /** mvc */
        if ($this->get('mvc_controller', '') == '') {
            $this->set('mvc_controller',
                $parameters->get('controller', ''));
        }
        if ($this->get('mvc_task', '') == '') {
            $this->set('mvc_task',
                $parameters->get('task', ''));
        }
        if ($this->get('extension_instance_name', '') == '') {
            $this->set('extension_instance_name',
                $parameters->get('option', ''));
        }
        if ($this->get('mvc_model', '') == '') {
            $this->set('mvc_model',
                $parameters->get('model', ''));
        }
        if ((int)$this->get('mvc_id', 0) == 0) {
            $this->set('mvc_id',
                $parameters->get('id', 0));
        }
        if ((int)$this->get('mvc_category_id', 0) == 0) {
            $this->set('mvc_category_id',
                $parameters->get('category_id', 0));
        }
        if ((int)$this->get('mvc_suppress_no_results', 0) == 0) {
            $this->set('mvc_suppress_no_results',
                $parameters->get('suppress_no_results', 0));
        }

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
            (int)$this->get('category_id'),
            '#__content'
        );

        if (count($row) == 0) {
            /** 500: Category not found */
            $this->set('status_found', false);
            Services::Message()
                ->set(
                $message = Services::Language()->_('ERROR_SOURCE_ITEM_NOT_FOUND'),
                $type = MOLAJO_MESSAGE_TYPE_ERROR,
                $code = 500,
                $debug_location = 'MolajoRequest::_getPrimaryCategory',
                $debug_object = $this->page_request
            );
            return $this->set('status_found', false);
        }

        $this->set('category_title', $row->title);
        $this->set('category_asset_type_id', $row->asset_type_id);
        $this->set('category_asset_id', $row->asset_id);
        $this->set('category_view_group_id', $row->view_group_id);
        $this->set('category_language', $row->language);
        $this->set('category_translation_of_id', $row->translation_of_id);

        $custom_fields = new Registry;
        $custom_fields->loadString($row->custom_fields);
        $this->set('category_custom_fields', $custom_fields);

        $metadata = new Registry;
        $metadata->loadString($row->metadata);
        $this->set('category_metadata', $metadata);

        $parameters = new Registry;
        $parameters->loadString($row->parameters);
        $this->set('category_parameters', $parameters);

        $this->_setPageValuesDefaults($parameters, $metadata);

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
        /** Retrieve Extension Query Results */
        if ($this->get('extension_instance_id', 0) == 0) {
        } else {
            $rows = ExtensionHelper::get(
                0,
                (int)$this->get('extension_instance_id')
            );
        }

        /** Fatal error if Extension cannot be found */
        if (($this->get('extension_instance_id', 0) == 0)
            || (count($rows) == 0)
        ) {

            /** 500: Extension not found */
            Services::Message()
                ->set(
                $message = Services::Language()->_('ERROR_EXTENSION_NOT_FOUND'),
                $type = MOLAJO_MESSAGE_TYPE_ERROR,
                $code = 500,
                $debug_location = 'MolajoRequest::_getExtension',
                $debug_object = $this->page_request
            );
            return $this->set('status_found', false);
        }

        /** Process Results */
        $row = array();
        foreach ($rows as $row) {
        }

        $this->set('extension_instance_name', $row->title);
        $this->set('extension_asset_id', $row->asset_id);
        $this->set('extension_asset_type_id', $row->asset_type_id);
        $this->set('extension_view_group_id', $row->view_group_id);
        $this->set('extension_type', $row->asset_type_title);

        $custom_fields = new Registry;
        $custom_fields->loadString($row->custom_fields);
        $this->set('extension_custom_fields', $custom_fields);

        $metadata = new Registry;
        $metadata->loadString($row->metadata);
        $this->set('extension_metadata', $metadata);

        $parameters = new Registry;
        $parameters->loadString($row->parameters);
        $this->set('extension_parameters', $parameters);

        $this->_setPageValuesDefaults($parameters, $metadata);

        /** mvc */
        if ($this->get('mvc_controller', '') == '') {
            $this->set('mvc_controller',
                $parameters->get('controller', '')
            );
        }
        if ($this->get('mvc_task', '') == '') {
            $this->set('mvc_task',
                $parameters->get('task', 'display')
            );
        }
        if ($this->get('mvc_model', '') == '') {
            $this->set('mvc_model',
                $parameters->get('model', 'content')
            );
        }
        if ((int)$this->get('mvc_id', 0) == 0) {
            $this->set('mvc_id',
                $parameters->get('id', 0)
            );
        }
        if ((int)$this->get('mvc_category_id', 0) == 0) {
            $this->set('mvc_category_id',
                $parameters->get('category_id', 0)
            );
        }
        if ((int)$this->get('mvc_suppress_no_results', 0) == 0) {
            $this->set('mvc_suppress_no_results',
                $parameters->get('suppress_no_results', 0)
            );
        }

        $this->set('extension_event_type',
            $parameters->get('plugin_type', array('content'))
        );

        $this->set('extension_path',
            ExtensionHelper::getPath(
                $this->get('extension_asset_type_id'),
                $this->get('extension_instance_name')
            )
        );

        return $this->set('status_found', true);
    }

    /**
     * _setPageValues
     *
     * Called by content item and menu item methods
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
        if ((int)$this->get('theme_id', 0) == 0) {
            $this->set('theme_id',
                $parameters->get('theme_id', 0)
            );
        }
        if ((int)$this->get('page_view_id', 0) == 0) {
            $this->set('page_view_id',
                $parameters->get('page_view_id', 0)
            );
        }

        if ((int)$this->get('template_view_id', 0) == 0) {
            $this->set('template_view_id',
                $parameters->get('template_view_id', 0)
            );
        }

        if ((int)$this->get('wrap_view_id', 0) == 0) {
            $this->set('wrap_view_id',
                $parameters->get('wrap_view_id', 0)
            );
        }

        $this->parameters = ExtensionHelper::mergeParameters(
            $parameters,
            $this->parameters
        );

        /** merge meta data */
        if ($this->get('metadata_title', '') == '') {
            $this->set('metadata_title',
                $metadata->get('metadata_title', '')
            );
        }
        if ($this->get('metadata_description', '') == '') {
            $this->set('metadata_description',
                $metadata->get('metadata_description', '')
            );
        }
        if ($this->get('metadata_keywords', '') == '') {
            $this->set('metadata_keywords',
                $metadata->get('metadata_keywords', '')
            );
        }
        if ($this->get('metadata_author', '') == '') {
            $this->set('metadata_author',
                $metadata->get('metadata_author', '')
            );
        }
        if ($this->get('metadata_content_rights', '') == '') {
            $this->set('metadata_content_rights',
                $metadata->get('metadata_content_rights', '')
            );
        }
        if ($this->get('metadata_robots', '') == '') {
            $this->set('metadata_robots',
                $metadata->get('metadata_robots', '')
            );
        }

        return;
    }

    /**
     *  _setPageValuesDefaults
     *
     *  Called by Category and Extension Methods
     *
     * @return bool
     * @since 1.0
     */
    protected function _setPageValuesDefaults($parameters = null, $metadata = null)
    {
        if ($this->get('theme_id', 0) == 0) {
            $this->set('theme_id', $parameters->get('default_theme_id', 0));
        }

        if ($this->get('page_view_id', 0) == 0) {
            $this->set('page_view_id', $parameters->get('default_page_view_id', 0));
        }

        if ((int)$this->get('template_view_id', 0) == 0) {
            $this->set('template_view_id',
                ViewHelper::getViewDefaultsOther(
                    'template',
                    $this->get('mvc_task', ''),
                    (int)$this->get('mvc_id', 0),
                    $parameters)
            );
        }

        if ((int)$this->get('wrap_view_id', 0) == 0) {
            $this->set('wrap_view_id',
                ViewHelper::getViewDefaultsOther(
                    'wrap',
                    $this->get('mvc_task', ''),
                    (int)$this->get('mvc_id', 0),
                    $parameters)
            );
        }

        /** metadata  */
        if ($this->get('metadata_title', '') == '') {
            $this->set('metadata_title',
                Services::Configuration()
                    ->get('metadata_title', ''));
        }
        if ($this->get('metadata_description', '') == '') {
            $this->set('metadata_description',
                Services::Configuration()
                    ->get('metadata_description', ''));
        }
        if ($this->get('metadata_keywords', '') == '') {
            $this->set('metadata_keywords',
                Services::Configuration()
                    ->get('metadata_keywords', ''));
        }
        if ($this->get('metadata_author', '') == '') {
            $this->set('metadata_author',
                Services::Configuration()
                    ->get('metadata_author', ''));
        }
        if ($this->get('metadata_content_rights', '') == '') {
            $this->set('metadata_content_rights',
                Services::Configuration()
                    ->get('metadata_content_rights', ''));
        }
        if ($this->get('metadata_robots', '') == '') {
            $this->set('metadata_robots',
                Services::Configuration()
                    ->get('metadata_robots', ''));
        }

        $this->parameters = ExtensionHelper::mergeParameters(
            $parameters,
            $this->parameters
        );

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
        $parameters = new Registry;
        $parameters->loadString(
            Services::User()
                ->get('parameters')
        );

        if ($this->get('theme_id', 0) == 0) {
            $this->set('theme_id', $parameters->get('user_theme_id', 0));
        }
        if ($this->get('page_view_id', 0) == 0) {
            $this->set('page_view_id', $parameters->get('user_page_view_id', 0));
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
                Services::Configuration()
                    ->get('default_theme_id', ''));
        }

        if ($this->get('page_view_id', 0) == 0) {
            $this->set('page_view_id',
                Services::Configuration()
                    ->get('default_page_view_id', ''));
        }

        if ((int)$this->get('template_view_id', 0) == 0) {
            $this->set('template_view_id',
                ViewHelper::getViewDefaultsApplication(
                    'template',
                    $this->get('mvc_task', ''),
                    (int)$this->get('mvc_id', 0))
            );
        }

        if ((int)$this->get('wrap_view_id', 0) == 0) {
            $this->set('wrap_view_id',
                ViewHelper::getViewDefaultsApplication('wrap', $this->get('mvc_task', ''), (int)$this->get('mvc_id', 0))
            );
        }

        /** metadata  */
        if ($this->get('metadata_title', '') == '') {
            $this->set('metadata_title',
                Services::Configuration()
                    ->get('metadata_title', ''));
        }
        if ($this->get('metadata_description', '') == '') {
            $this->set('metadata_description',
                Services::Configuration()
                    ->get('metadata_description', ''));
        }
        if ($this->get('metadata_keywords', '') == '') {
            $this->set('metadata_keywords',
                Services::Configuration()
                    ->get('metadata_keywords', ''));
        }
        if ($this->get('metadata_author', '') == '') {
            $this->set('metadata_author',
                Services::Configuration()
                    ->get('metadata_author', ''));
        }
        if ($this->get('metadata_content_rights', '') == '') {
            $this->set('metadata_content_rights',
                Services::Configuration()
                    ->get('metadata_content_rights', ''));
        }
        if ($this->get('metadata_robots', '') == '') {
            $this->set('metadata_robots',
                Services::Configuration()
                    ->get('metadata_robots', ''));
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

        $parameters = new Registry;
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
            $this->get('theme_name')
        );
        $this->set('wrap_view_path', $wrapHelper->view_path);
        $this->set('wrap_view_path_url', $wrapHelper->view_path_url);

        return;
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
    protected function _error($code, $message = 'Internal server error')
    {
        $this->set('status_error', true);
        $this->set('mvc_controller', 'display');
        $this->set('mvc_task', 'display');
        $this->set('mvc_model', 'messages');

        /** default error theme and page */
        $this->set('theme_id',
            Services::Configuration()
                ->get('error_theme_id', 'system')
        );
        $this->set('page_view_id',
            Services::Configuration()
                ->get('error_page_view_id', 'error')
        );

        /** set header status, message and override default theme/page, if needed */
        if ($code == 503) {
            $this->_503error();

        } else if ($code == 403) {
            $this->_403error();

        } else if ($code = 404) {
            $this->_404error();

        } else {

            Molajo::Responder()
                ->setHeader('Status',
                '500 Internal server error',
                'true'
            );

            Services::Message()->set($message,
                MOLAJO_MESSAGE_TYPE_ERROR,
                500
            );
        }
        return;
    }

    /**
     * _503error
     *
     * Offline
     *
     * @return  null
     * @since   1.0
     */
    protected function _503error()
    {
        Molajo::Responder()
            ->setHeader('Status',
            '503 Service Temporarily Unavailable',
            'true'
        );

        Services::Message()->set(
            Services::Configuration()->get('offline_message',
                'This site is not available.<br /> Please check back again soon.'
            ),
            MOLAJO_MESSAGE_TYPE_WARNING,
            503
        );

        $this->set('theme_id',
            Services::Configuration()
                ->get('offline_theme_id', 'system')
        );

        $this->set('page_view_id',
            Services::Configuration()
                ->get('offline_page_view_id', 'offline')
        );

        return;
    }

    /**
     * _403error
     *
     * Not Authorised
     *
     * @return  null
     * @since   1.0
     */
    protected function _403error()
    {
        Molajo::Responder()
            ->setHeader('Status',
            '403 Not Authorised',
            'true'
        );

        Services::Message()->set(
            Services::Configuration()->get('error_403_message', 'Not Authorised.'),
            MOLAJO_MESSAGE_TYPE_ERROR,
            403
        );

        return;
    }

    /**
     * _404error
     *
     * Not Found
     *
     * @return  null
     * @since   1.0
     */
    protected function _404error()
    {
        Molajo::Responder()
            ->setHeader('Status',
            '404 Not Found',
            'true'
        );

        Services::Message()->set(
            Services::Configuration()->get('error_404_message', 'Page not found.'),
            MOLAJO_MESSAGE_TYPE_ERROR,
            404
        );

        return;
    }

    /**
     * _initialize
     *
     * Create and Initialize the request and establish other
     * properties needed by this method and downstream in the
     * application
     *
     * Request Object which can be accessed by other classes
     *
     * @static
     * @return array
     * @since 1.0
     */
    protected function _initialize()
    {
        $this->parameters = array();

        $this->page_request = new JRegistry();

        /** request */
        $this->set('request_url_base', MOLAJO_BASE_URL);
        $this->set('request_asset_id', 0);
        $this->set('request_asset_type_id', 0);
        $this->set('request_url_query', '');
        $this->set('request_url', '');
        $this->set('request_url_sef', '');
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
        $this->set('extension_event_type', '');

        /** merged */
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
        $this->set('mvc_option', '');
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

        /**
         *  Display Controller saves the query results for the primary request
         *      extension for possible reuse by other extensions. MolajoRequestModel
         *      can be used to retrieve the data.
         */
        $this->set('query_rowset', array());
        $this->set('query_pagination', array());
        $this->set('query_state', array());
    }
}
