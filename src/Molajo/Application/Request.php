<?php
/**
 * @package     Molajo
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application;

use Molajo\Application\Services;
use Molajo\Extension\Helper;

defined('MOLAJO') or die;

/**
 * Request
 *
 * Establishes parameter values given inheritance chain:
 * 1. Menu Item
 *   -or - Detail source content
 * 2. Extension for content
 * 3. Primary category of content
 * 4. Application
 * 5. Hard-coded defaults
 *
 * @package     Molajo
 * @subpackage  Request
 * @since       1.0
 */
Class Request
{
    /**
     * $instance
     *
     * @var        object
     * @since      1.0
     */
    protected static $instance;

    /**
     * getInstance
     *
     * Returns a reference to the global request object,
     *  only creating it if it doesn't already exist.
     *
     * @static
     * @param      string  $override_request_url
     * @param      string  $override_asset_id
     *
     * @return     object
     * @since      1.0
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new Request();
        }
        return self::$instance;
    }

    /**
     * __construct
     *
     * Class constructor
     *
     * @return     mixed
     * @since      1.0
     */
    public function __construct()
    {
        $this->_initialize();

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
     * @since   1.0
     */
    public function get($key, $default = null)
    {
        return Services::Registry()->get('page_request\\' . $key, $default);
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
     * @since   1.0
     */
    public function set($key, $value = null)
    {
        return Services::Registry()->set('page_request\\' . $key, $value);
    }

    /**
     * process
     *
     * Using the MOLAJO_PAGE_REQUEST value,
     *  retrieve the asset record,
     *  set the variables needed to render output,
     *  execute document renders and MVC
     *
     * @param null $override_request_url
     * @param null $override_asset_id
     *
     * @return mixed
     * @since 1.0
     */
    public function process($override_request_url = null, $override_asset_id = null)
    {
        /** Specific asset */
        if ((int)$override_asset_id == 0) {
            Services::Registry()->set('request\\request_asset_id', 0);
        } else {
            Services::Registry()->set('request\\request_asset_id', $override_asset_id);
        }

        /** Check for home duplicate content and redirect */
        $this->_checkHome($override_request_url);
        if (Services::Redirect()->url === null
            && (int)Services::Redirect()->code == 0
        ) {
        } else {
            return false;
        }

        /** Offline Mode */
        if (Services::Configuration()->get('offline', 1) == 0) {
            $this->_error(503);
        }

        /** URL parameters */
        $this->_getRequest();

        /** Asset, Access Control, links to source, menus, extensions, etc. */
        $this->_getAsset();

        /** Authorise */
        if (Services::Registry()->get('request\\status_found')) {
            $this->_authoriseTask();
        }

        /** Route */
        $this->_routeRequest();

        /** Action: Render Page */
        if (Services::Registry()->get('request\\mvc_controller') == 'display') {
            $this->_getUser();
            $this->_getApplicationDefaults();
            $this->_getTheme();
            $this->_getPage();
            $this->_getTemplateView();
            $this->_getWrapView();

            $temp = Services::Registry()->initialise();
            $temp->loadArray($this->parameters);
            $this->parameters = $temp;

        } else {

            /** Action: Database action */
            $temp = Services::Registry()->initialise();
            $temp->loadArray($this->parameters);
            $this->parameters = $temp;

            if (Services::Configuration()->get('sef', 1) == 0) {
                $link = $this->page_request->get('request_url_sef');
            } else {
                $link = $this->page_request->get('request_url');
            }
            Services::Registry()->set('request\\redirect_on_failure', $link);

            Services::Registry()->set('request\\model', ucfirst(trim(Services::Registry()->get('request\\mvc_model'))) . 'Model');
            $cc = 'Molajo' . ucfirst(Services::Registry()->get('request\\mvc_controller')) . 'Controller';
            Services::Registry()->set('request\\controller', $cc);
            $task = Services::Registry()->get('request\\mvc_task');
            Services::Registry()->set('request\\task', $task);
            Services::Registry()->set('request\\id', Services::Registry()->get('request\\mvc_id'));
            $controller = new $cc($this->page_request, $this->parameters);

            /** execute task: non-display, edit, or add tasks */
            return $controller->$task();
        }

        return $this;
    }

    /**
     * _checkHome
     *
     * @param $override_request_url
     *
     * @return mixed
     * @since  1.0
     */
    protected function _checkHome($override_request_url)
    {
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
        Services::Registry()->set('request\\request_url_query', $path);

        /** home: duplicate content - redirect */
        if (Services::Registry()->get('request\\request_url_query', '') == 'index.php'
            || Services::Registry()->get('request\\request_url_query', '') == 'index.php/'
            || Services::Registry()->get('request\\request_url_query', '') == 'index.php?'
            || Services::Registry()->get('request\\request_url_query', '') == '/index.php/'
        ) {
            return Services::Redirect()->set('', 301);
        }

        /** Home */
        if (Services::Registry()->get('request\\request_url_query', '') == ''
            && (int)Services::Registry()->get('request\\request_asset_id', 0) == 0
        ) {
            Services::Registry()->set('request\\request_asset_id',
                Services::Configuration()->get('home_asset_id', 0));
            Services::Registry()->set('request\\request_url_home', true);
        }

        Services::Debug()->set('Molajo::Request()->_checkHome complete');

        return true;
    }

    /**
     * _getRequest
     *
     * Retrieve URL contents
     *
     * @return bool
     * @since 1.0
     */
    protected function _getRequest()
    {
        // echo 'Ajax ' . Services::Request()->request->isXmlHttpRequest().'<br />';
        //$queryString = Services::Request()->get('option');

        $queryString = Services::Request()->request->getQueryString();
        $pair = explode('&', $queryString);
        $pairs = array();
        $extra = array();

        if (count($pairs) > 0) {
            $xml = MOLAJO_APPLICATIONS . '/Configuration/parameters.xml';
            if (is_file($xml)) {
            } else {
                return false;
            }
            $parameters = simplexml_load_file($xml, 'SimpleXMLElement');
            foreach ($parameters->parameter as $item) {
                $extra[(string)$item] = null;
            }

        }

        foreach ($pair as $item) {
            $kv = explode('=', $item);
            $pairs[$kv[0]] = $kv[1];
        }

        /** todo: input is not filtered yet */

        if (count($pairs) > 0
            && isset($pairs['task'])
        ) {
            Services::Registry()->set('request\\mvc_task', $pairs['task']);
        } else {
            Services::Registry()->set('request\\mvc_task', 'display');
        }

        if (Services::Registry()->get('request\\mvc_task', '') == ''
            || Services::Registry()->get('request\\mvc_task', 'display') == 'display'
        ) {
            $pageRequest = Services::Registry()->get('request\\request_url_query');

            if (strripos($pageRequest, '/edit') == (strlen($pageRequest) - 5)) {
                $pageRequest = substr($pageRequest, 0, strripos($pageRequest, '/edit'));
                Services::Registry()->set('request\\request_url_query', $pageRequest);
                Services::Registry()->set('request\\mvc_task', 'edit');

            } else if (strripos($pageRequest, '/add') == (strlen($pageRequest) - 4)) {
                $pageRequest = substr($pageRequest, 0, strripos($pageRequest, '/add'));
                Services::Registry()->set('request\\request_url_query', $pageRequest);
                Services::Registry()->set('request\\mvc_task', 'add');

            } else {
                Services::Debug()->set('Molajo::Request()->_getRequest() complete Display Task');
                Services::Registry()->set('request\\mvc_task', 'display');
            }
            return true;
        }

        /** return */
        if (isset($pairs['return'])) {
            $return = $pairs['return'];
        } else {
            $return = '';
        }
        if (trim($return) == '') {
            Services::Registry()->set('request\\redirect_on_success', '');
        } else if (JUri::isInternal(base64_decode($return))) {
            Services::Registry()->set('request\\redirect_on_success', base64_decode($return));
        } else {
            Services::Registry()->set('request\\redirect_on_success', '');
        }

        /** option */
        Services::Registry()->set('request\\mvc_option', (string)$pairs['option']);

        /** asset information */
        Services::Registry()->set('request\\mvc_id', (int)$pairs['id']);

        Services::Debug()->set('Molajo::Request()->_getRequest()');

        return true;
    }

    /**
     * _getAsset
     *
     * Retrieve Asset and Asset Type data for a specific asset id
     * or query request
     *
     * @return    boolean
     * @since    1.0
     */
    protected function _getAsset()
    {

        $row = Molajo::Helper()
            ->get('Asset',
            (int)Services::Registry()->get('request\\request_asset_id'),
            Services::Registry()->get('request\\request_url_query'),
            Services::Registry()->get('request\\mvc_option'),
            Services::Registry()->get('request\\mvc_id')
        );

        $row = Molajo::Helper()
            ->get('Extension',
            MOLAJO_ASSET_TYPE_EXTENSION_THEME,
            'Bootstrap'
        );
var_dump($row);
        die;
        /** 404: _routeRequest handles redirecting to error page */
        if (count($row) == 0
            || (int)$row->routable == 0
        ) {
            return Services::Registry()->set('request\\status_found', false);
        }
        echo 'before get extension';
        /** Redirect: _routeRequest handles rerouting the request */
        if ((int)$row->redirect_to_id == 0) {
        } else {
            $this->redirect_to_id = (int)$row->redirect_to_id;
            return Services::Registry()->set('request\\status_found', false);
        }

        /** 403: _authoriseTask handles redirecting to error page */
        if (in_array($row->view_group_id, Services::User()->get('view_groups'))) {
            Services::Registry()->set('request\\status_authorised', true);
        } else {
            return Services::Registry()->set('request\\status_authorised', false);
        }

        /** request url */
        Services::Registry()->set('request\\request_asset_id', (int)$row->asset_id);
        Services::Registry()->set('request\\request_asset_type_id', (int)$row->asset_type_id);
        Services::Registry()->set('request\\request_url', $row->request);
        Services::Registry()->set('request\\request_url_sef', $row->sef_request);

        /** home */
        if ((int)Services::Registry()->get('request\\request_asset_id', 0)
            == Services::Configuration()->get('home_asset_id', null)
        ) {
            Services::Registry()->set('request\\request_url_home', true);
        } else {
            Services::Registry()->set('request\\request_url_home', false);
        }

        Services::Registry()->set('request\\source_table', $row->source_table);
        Services::Registry()->set('request\\category_id', (int)$row->primary_category_id);

        /** mvc options and url parameters */
        Services::Registry()->set('request\\extension_instance_name', $row->request_option);
        Services::Registry()->set('request\\mvc_model', $row->request_model);
        Services::Registry()->set('request\\mvc_id', (int)$row->source_id);

        Services::Registry()->set('request\\mvc_controller',
            Services::Access()
                ->getTaskController(Services::Registry()->get('request\\mvc_task'))
        );

        /** Action Tasks need no additional information */
        if (Services::Registry()->get('request\\mvc_controller') == 'display') {
        } else {
            return Services::Registry()->set('request\\status_found', true);
        }

        if (Services::Registry()->get('request\\request_asset_type_id')
            == MOLAJO_ASSET_TYPE_MENU_ITEM_COMPONENT
        ) {
            Services::Registry()->set('request\\menu_item_id', $row->source_id);
            $this->_getMenuItem();
            if (Services::Registry()->get('request\\status_found') === false) {
                return Services::Registry()->get('request\\status_found');
            }
        } else {
            Services::Registry()->set('request\\source_id', $row->source_id);
            $this->_getSource();
        }

        /** primary category */
        if (Services::Registry()->get('request\\category_id', 0) == 0) {
        } else {
            Services::Registry()->set('request\\mvc_category_id', Services::Registry()->get('request\\category_id'));
            $this->_getPrimaryCategory();
        }

        /** Extension */
        $this->_getExtension();

        return Services::Registry()->get('request\\status_found');
    }

    /**
     * _authoriseTask
     *
     * Verify user authorization for task
     *
     * @return   boolean
     * @since    1.0
     */
    protected function _authoriseTask()
    {
        /** display view verified in _getAsset */
        if (Services::Registry()->get('request\\mvc_task') == 'display'
            && Services::Registry()->get('request\\status_authorised') === true
        ) {
            return true;
        }
        if (Services::Registry()->get('request\\mvc_task') == 'display'
            && Services::Registry()->get('request\\status_authorised') === false
        ) {
            $this->_error(403);
            return false;
        }

        /** verify other tasks */
        Services::Registry()->set('request\\status_authorised',
            Services::Access()
                ->authoriseTask(
                Services::Registry()->get('request\\mvc_task'),
                Services::Registry()->get('request\\request_asset_id')
            )
        );

        if (Services::Registry()->get('request\\status_authorised') === true) {
        } else {
            $this->_error(403);
            return false;
        }

        return true;
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
     * @return    void
     * @since    1.0
     */
    protected function _routeRequest()
    {
        /** not found */
        if (Services::Registry()->get('request\\status_found') === false) {
            $this->_error(404);
        }

        /** redirect */
        if ($this->redirect_to_id == 0) {
        } else {
            Services::Response()->redirect(
                Molajo::Helper()->getURL('Asset', $this->redirect_to_id),
                301
            );
        }

        /** must be logged on */
        if (Services::Configuration()->get('logon_requirement', 0) > 0
            && Services::User()->get('guest', true) === true
            && Services::Registry()->get('request\\request_asset_id')
                <> Services::Configuration()->get('logon_requirement', 0)
        ) {
            Services::Response()
                ->redirect(Services::Configuration()
                ->get('logon_requirement', 0), 303);
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
        $row = Molajo::Helper()
            ->get('Menuitem',
            (int)Services::Registry()->get('request\\menu_item_id')
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
            Services::Registry()->set('request\\status_authorised', false);
            return Services::Registry()->set('request\\status_found', false);
        }

        Services::Registry()->set('request\\menu_item_title', $row->menu_item_title);
        Services::Registry()->set('request\\menu_item_asset_type_id', $row->menu_item_asset_type_id);
        Services::Registry()->set('request\\menu_item_asset_id', $row->menu_item_asset_id);
        Services::Registry()->set('request\\menu_item_view_group_id', $row->menu_item_view_group_id);

        Services::Registry()->set('request\\extension_instance_id', $row->menu_id);
        Services::Registry()->set('request\\extension_instance_name', $row->menu_title);
        Services::Registry()->set('request\\extension_instance_asset_type_id', $row->menu_asset_type_id);
        Services::Registry()->set('request\\extension_instance_asset_id', $row->menu_asset_id);
        Services::Registry()->set('request\\extension_instance_view_group_id', $row->menu_view_group_id);

        $parameters = Services::Registry()->initialise();
        $parameters->loadString($row->menu_item_parameters);
        Services::Registry()->set('request\\menu_item_parameters', $parameters);

        $custom_fields = Services::Registry()->initialise();
        $custom_fields->loadString($row->menu_item_custom_fields);
        Services::Registry()->set('request\\menu_item_custom_fields', $custom_fields);

        $metadata = Services::Registry()->initialise();
        $metadata->loadString($row->menu_item_metadata);
        Services::Registry()->set('request\\menu_item_metadata', $metadata);

        $this->_setPageValues($parameters, $metadata);

        Services::Registry()->set('request\\menu_item_language', $row->menu_item_language);
        Services::Registry()->set('request\\menu_item_translation_of_id', $row->menu_item_translation_of_id);

        /** mvc */
        if (Services::Registry()->get('request\\mvc_controller', '') == '') {
            Services::Registry()->set('request\\mvc_controller',
                $parameters->get('controller', '')
            );
        }
        if (Services::Registry()->get('request\\mvc_task', '') == '') {
            Services::Registry()->set('request\\mvc_task',
                $parameters->get('task', '')
            );
        }
        if (Services::Registry()->get('request\\extension_instance_name', '') == '') {
            Services::Registry()->set('request\\extension_instance_name',
                $parameters->get('option', '')
            );
        }
        if (Services::Registry()->get('request\\mvc_model', '') == '') {
            Services::Registry()->set('request\\mvc_model',
                $parameters->get('model', '')
            );
        }
        if ((int)Services::Registry()->get('request\\mvc_id', 0) == 0) {
            Services::Registry()->set('request\\mvc_id', $parameters->get('id', 0));
        }
        if ((int)Services::Registry()->get('request\\mvc_category_id', 0) == 0) {
            Services::Registry()->set('request\\mvc_category_id',
                $parameters->get('category_id', 0)
            );
        }
        if ((int)Services::Registry()->get('request\\mvc_suppress_no_results', 0) == 0) {
            Services::Registry()->set('request\\mvc_suppress_no_results',
                $parameters->get('suppress_no_results', 0)
            );
        }

        return Services::Registry()->set('request\\status_found', true);
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
        $row = Molajo::Helper()
            ->get('Content',
            (int)Services::Registry()->get('request\\source_id'),
            Services::Registry()->get('request\\source_table')
        );

        if (count($row) == 0) {
            return true;
        }
        //        if (count($row) == 0) {
        //            /** 500: Source Content not found */
        //            Services::Registry()->set('request\\status_found', false);
        //            Services::Message()
        //                ->set(
        //                $message = Services::Language()->translate('ERROR_SOURCE_ITEM_NOT_FOUND'),
        //                $type = MOLAJO_MESSAGE_TYPE_ERROR,
        //                $code = 500,
        //                $debug_location = 'MolajoRequest::_getSource',
        //                $debug_object = $this->page_request
        //            );
        //            return Services::Registry()->set('request\\status_found', false);
        //        }

        /** match found */
        Services::Registry()->set('request\\source_title', $row->title);
        Services::Registry()->set('request\\source_asset_type_id', $row->asset_type_id);
        Services::Registry()->set('request\\source_asset_id', $row->asset_id);
        Services::Registry()->set('request\\source_view_group_id', $row->view_group_id);
        Services::Registry()->set('request\\source_language', $row->language);
        Services::Registry()->set('request\\source_translation_of_id', $row->translation_of_id);
        Services::Registry()->set('request\\source_last_modified', $row->modified_datetime);

        Services::Registry()->set('request\\extension_instance_id', $row->extension_instance_id);

        $custom_fields = Services::Registry()->initialise();
        $custom_fields->loadString($row->custom_fields);
        Services::Registry()->set('request\\source_custom_fields', $custom_fields);

        $metadata = Services::Registry()->initialise();
        $metadata->loadString($row->metadata);
        Services::Registry()->set('request\\source_metadata', $metadata);

        $parameters = Services::Registry()->initialise();
        $parameters->loadString($row->parameters);
        $parameters->set('id', $row->id);
        $parameters->set('asset_type_id', $row->asset_type_id);
        Services::Registry()->set('request\\source_parameters', $parameters);

        $this->_setPageValues($parameters, $metadata);

        /** mvc */
        if (Services::Registry()->get('request\\mvc_controller', '') == '') {
            Services::Registry()->set('request\\mvc_controller',
                $parameters->get('controller', ''));
        }
        if (Services::Registry()->get('request\\mvc_task', '') == '') {
            Services::Registry()->set('request\\mvc_task',
                $parameters->get('task', ''));
        }
        if (Services::Registry()->get('request\\extension_instance_name', '') == '') {
            Services::Registry()->set('request\\extension_instance_name',
                $parameters->get('option', ''));
        }
        if (Services::Registry()->get('request\\mvc_model', '') == '') {
            Services::Registry()->set('request\\mvc_model',
                $parameters->get('model', ''));
        }
        if ((int)Services::Registry()->get('request\\mvc_id', 0) == 0) {
            Services::Registry()->set('request\\mvc_id',
                $parameters->get('id', 0));
        }
        if ((int)Services::Registry()->get('request\\mvc_category_id', 0) == 0) {
            Services::Registry()->set('request\\mvc_category_id',
                $parameters->get('category_id', 0));
        }
        if ((int)Services::Registry()->get('request\\mvc_suppress_no_results', 0) == 0) {
            Services::Registry()->set('request\\mvc_suppress_no_results',
                $parameters->get('suppress_no_results', 0));
        }

        return Services::Registry()->set('request\\status_found', true);
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
        $row = Molajo::Helper()
            ->get('Content',
            (int)Services::Registry()->get('request\\category_id'),
            '#__content'
        );

        if (count($row) == 0) {
            /** 500: Category not found */
            Services::Registry()->set('request\\status_found', false);
            Services::Message()
                ->set(
                $message = Services::Language()->translate('ERROR_SOURCE_ITEM_NOT_FOUND'),
                $type = MOLAJO_MESSAGE_TYPE_ERROR,
                $code = 500,
                $debug_location = 'MolajoRequest::_getPrimaryCategory',
                $debug_object = $this->page_request
            );
            return Services::Registry()->set('request\\status_found', false);
        }

        Services::Registry()->set('request\\category_title', $row->title);
        Services::Registry()->set('request\\category_asset_type_id', $row->asset_type_id);
        Services::Registry()->set('request\\category_asset_id', $row->asset_id);
        Services::Registry()->set('request\\category_view_group_id', $row->view_group_id);
        Services::Registry()->set('request\\category_language', $row->language);
        Services::Registry()->set('request\\category_translation_of_id', $row->translation_of_id);

        $custom_fields = Services::Registry()->initialise();
        $custom_fields->loadString($row->custom_fields);
        Services::Registry()->set('request\\category_custom_fields', $custom_fields);

        $metadata = Services::Registry()->initialise();
        $metadata->loadString($row->metadata);
        Services::Registry()->set('request\\category_metadata', $metadata);

        $parameters = Services::Registry()->initialise();
        $parameters->loadString($row->parameters);
        Services::Registry()->set('request\\category_parameters', $parameters);

        $this->_setPageValuesDefaults($parameters, $metadata);

        return Services::Registry()->set('request\\status_found', true);
    }

    /**
     * _getExtension
     *
     * Retrieve extension information for Component Request
     *
     * @return    bool
     * @since    1.0
     */
    protected function _getExtension()
    {
        /** Retrieve Extension Query Results */
        if (Services::Registry()->get('request\\extension_instance_id', 0) == 0) {
        } else {
            $rows = Molajo::Helper()
                ->get('Extension', 0,
                (int)Services::Registry()->get('request\\extension_instance_id')
            );
        }

        /** Fatal error if Extension cannot be found */
        if ((Services::Registry()->get('request\\extension_instance_id', 0) == 0)
            || (count($rows) == 0)
        ) {
            /** 500: Extension not found */
            Services::Message()
                ->set(
                $message = Services::Language()
                    ->translate('ERROR_EXTENSION_NOT_FOUND'),
                $type = MOLAJO_MESSAGE_TYPE_ERROR,
                $code = 500,
                $debug_location = 'MolajoRequest::_getExtension',
                $debug_object = $this->page_request
            );
            return Services::Registry()->set('request\\status_found', false);
        }

        /** Process Results */
        $row = array();
        foreach ($rows as $row) {
        }

        Services::Registry()->set('request\\extension_instance_name', $row->title);
        Services::Registry()->set('request\\extension_asset_id', $row->asset_id);
        Services::Registry()->set('request\\extension_asset_type_id', $row->asset_type_id);
        Services::Registry()->set('request\\extension_view_group_id', $row->view_group_id);
        Services::Registry()->set('request\\extension_type', $row->asset_type_title);

        $custom_fields = Services::Registry()->initialise();
        $custom_fields->loadString($row->custom_fields);
        Services::Registry()->set('request\\extension_custom_fields', $custom_fields);

        $metadata = Services::Registry()->initialise();
        $metadata->loadString($row->metadata);
        Services::Registry()->set('request\\extension_metadata', $metadata);

        $parameters = Services::Registry()->initialise();
        $parameters->loadString($row->parameters);
        Services::Registry()->set('request\\extension_parameters', $parameters);

        $this->_setPageValuesDefaults($parameters, $metadata);

        /** mvc */
        if (Services::Registry()->get('request\\mvc_controller', '') == '') {
            Services::Registry()->set('request\\mvc_controller',
                $parameters->get('controller', '')
            );
        }
        if (Services::Registry()->get('request\\mvc_task', '') == '') {
            Services::Registry()->set('request\\mvc_task',
                $parameters->get('task', 'display')
            );
        }
        if (Services::Registry()->get('request\\mvc_model', '') == '') {
            Services::Registry()->set('request\\mvc_model',
                $parameters->get('model', 'content')
            );
        }
        if ((int)Services::Registry()->get('request\\mvc_id', 0) == 0) {
            Services::Registry()->set('request\\mvc_id',
                $parameters->get('id', 0)
            );
        }
        if ((int)Services::Registry()->get('request\\mvc_category_id', 0) == 0) {
            Services::Registry()->set('request\\mvc_category_id',
                $parameters->get('category_id', 0)
            );
        }
        if ((int)Services::Registry()->get('request\\mvc_suppress_no_results', 0) == 0) {
            Services::Registry()->set('request\\mvc_suppress_no_results',
                $parameters->get('suppress_no_results', 0)
            );
        }

        Services::Registry()->set('request\\extension_event_type',
            $parameters->get('plugin_type', array('content'))
        );

        Services::Registry()->set('request\\extension_path',
            Molajo::Helper()->getPath('Extension',
                Services::Registry()->get('request\\extension_asset_type_id'),
                Services::Registry()->get('request\\extension_instance_name')
            )
        );

        return Services::Registry()->set('request\\status_found', true);
    }

    /**
     * _setPageValues
     *
     * Called by content item and menu item methods
     * Set the values needed to generate the page (theme, page, view, wrap, and various metadata)
     *
     * @param     null    $sourceParameters
     * @param     null    $sourceMetadata
     *
     * @return    bool
     * @since    1.0
     */
    protected function _setPageValues($parameters = null, $metadata = null)
    {
        if ((int)Services::Registry()->get('request\\theme_id', 0) == 0) {
            Services::Registry()->set('request\\theme_id',
                $parameters->get('theme_id', 0)
            );
        }
        if ((int)Services::Registry()->get('request\\page_view_id', 0) == 0) {
            Services::Registry()->set('request\\page_view_id',
                $parameters->get('page_view_id', 0)
            );
        }

        if ((int)Services::Registry()->get('request\\template_view_id', 0) == 0) {
            Services::Registry()->set('request\\template_view_id',
                $parameters->get('template_view_id', 0)
            );
        }

        if ((int)Services::Registry()->get('request\\wrap_view_id', 0) == 0) {
            Services::Registry()->set('request\\wrap_view_id',
                $parameters->get('wrap_view_id', 0)
            );
        }

        $this->parameters = Molajo::Helper()->mergeParameters('Extension',
            $parameters,
            $this->parameters
        );

        /** merge meta data */
        if (Services::Registry()->get('request\\metadata_title', '') == '') {
            Services::Registry()->set('request\\metadata_title',
                $metadata->get('metadata_title', '')
            );
        }
        if (Services::Registry()->get('request\\metadata_description', '') == '') {
            Services::Registry()->set('request\\metadata_description',
                $metadata->get('metadata_description', '')
            );
        }
        if (Services::Registry()->get('request\\metadata_keywords', '') == '') {
            Services::Registry()->set('request\\metadata_keywords',
                $metadata->get('metadata_keywords', '')
            );
        }
        if (Services::Registry()->get('request\\metadata_author', '') == '') {
            Services::Registry()->set('request\\metadata_author',
                $metadata->get('metadata_author', '')
            );
        }
        if (Services::Registry()->get('request\\metadata_content_rights', '') == '') {
            Services::Registry()->set('request\\metadata_content_rights',
                $metadata->get('metadata_content_rights', '')
            );
        }
        if (Services::Registry()->get('request\\metadata_robots', '') == '') {
            Services::Registry()->set('request\\metadata_robots',
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
     * @return    bool
     * @since    1.0
     */
    protected function _setPageValuesDefaults($parameters = null, $metadata = null)
    {
        if (Services::Registry()->get('request\\theme_id', 0) == 0) {
            Services::Registry()->set('request\\theme_id', $parameters->get('default_theme_id', 0));
        }

        if (Services::Registry()->get('request\\page_view_id', 0) == 0) {
            Services::Registry()->set('request\\page_view_id', $parameters->get('default_page_view_id', 0));
        }

        if ((int)Services::Registry()->get('request\\template_view_id', 0) == 0) {
            Services::Registry()->set('request\\template_view_id',
                ViewHelper::getViewDefaultsOther(
                    'template',
                    Services::Registry()->get('request\\mvc_task', ''),
                    (int)Services::Registry()->get('request\\mvc_id', 0),
                    $parameters)
            );
        }

        if ((int)Services::Registry()->get('request\\wrap_view_id', 0) == 0) {
            Services::Registry()->set('request\\wrap_view_id',
                ViewHelper::getViewDefaultsOther(
                    'wrap',
                    Services::Registry()->get('request\\mvc_task', ''),
                    (int)Services::Registry()->get('request\\mvc_id', 0),
                    $parameters)
            );
        }

        /** metadata  */
        if (Services::Registry()->get('request\\metadata_title', '') == '') {
            Services::Registry()->set('request\\metadata_title',
                Services::Configuration()
                    ->get('metadata_title', ''));
        }
        if (Services::Registry()->get('request\\metadata_description', '') == '') {
            Services::Registry()->set('request\\metadata_description',
                Services::Configuration()
                    ->get('metadata_description', ''));
        }
        if (Services::Registry()->get('request\\metadata_keywords', '') == '') {
            Services::Registry()->set('request\\metadata_keywords',
                Services::Configuration()
                    ->get('metadata_keywords', ''));
        }
        if (Services::Registry()->get('request\\metadata_author', '') == '') {
            Services::Registry()->set('request\\metadata_author',
                Services::Configuration()
                    ->get('metadata_author', ''));
        }
        if (Services::Registry()->get('request\\metadata_content_rights', '') == '') {
            Services::Registry()->set('request\\metadata_content_rights',
                Services::Configuration()
                    ->get('metadata_content_rights', ''));
        }
        if (Services::Registry()->get('request\\metadata_robots', '') == '') {
            Services::Registry()->set('request\\metadata_robots',
                Services::Configuration()
                    ->get('metadata_robots', ''));
        }

        $this->parameters = Molajo::Helper()->mergeParameters('Extension',
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
     * @return    bool
     * @since    1.0
     */
    protected function _getUser()
    {
        $parameters = Services::Registry()->initialise();
        $parameters->loadString(
            Services::User()
                ->get('parameters')
        );

        if (Services::Registry()->get('request\\theme_id', 0) == 0) {
            Services::Registry()->set('request\\theme_id',
                $parameters->get('user_theme_id', 0));
        }
        if (Services::Registry()->get('request\\page_view_id', 0) == 0) {
            Services::Registry()->set('request\\page_view_id',
                $parameters->get('user_page_view_id', 0));
        }

        return;
    }

    /**
     *  _getApplicationDefaults
     *
     *  Retrieve Theme and Page from the final level of default values, if needed
     *
     * @return    bool
     * @since    1.0
     */
    protected function _getApplicationDefaults()
    {
        if (Services::Registry()->get('request\\theme_id', 0) == 0) {
            Services::Registry()->set('request\\theme_id',
                Services::Configuration()
                    ->get('default_theme_id', ''));
        }

        if (Services::Registry()->get('request\\page_view_id', 0) == 0) {
            Services::Registry()->set('request\\page_view_id',
                Services::Configuration()
                    ->get('default_page_view_id', ''));
        }

        if ((int)Services::Registry()->get('request\\template_view_id', 0) == 0) {
            Services::Registry()->set('request\\template_view_id',
                Molajo::Helper()
                    ->getViewDefaultsApplication('View', 'template',
                    Services::Registry()->get('request\\mvc_task', ''),
                    (int)Services::Registry()->get('request\\mvc_id', 0))
            );
        }

        if ((int)Services::Registry()->get('request\\wrap_view_id', 0) == 0) {
            Services::Registry()->set('request\\wrap_view_id',
                Molajo::Helper()->getViewDefaultsApplication('View', 'wrap', Services::Registry()->get('request\\mvc_task', ''), (int)Services::Registry()->get('request\\mvc_id', 0))
            );
        }

        /** metadata  */
        if (Services::Registry()->get('request\\metadata_title', '') == '') {
            Services::Registry()->set('request\\metadata_title',
                Services::Configuration()
                    ->get('metadata_title', ''));
        }
        if (Services::Registry()->get('request\\metadata_description', '') == '') {
            Services::Registry()->set('request\\metadata_description',
                Services::Configuration()
                    ->get('metadata_description', ''));
        }
        if (Services::Registry()->get('request\\metadata_keywords', '') == '') {
            Services::Registry()->set('request\\metadata_keywords',
                Services::Configuration()
                    ->get('metadata_keywords', ''));
        }
        if (Services::Registry()->get('request\\metadata_author', '') == '') {
            Services::Registry()->set('request\\metadata_author',
                Services::Configuration()
                    ->get('metadata_author', ''));
        }
        if (Services::Registry()->get('request\\metadata_content_rights', '') == '') {
            Services::Registry()->set('request\\metadata_content_rights',
                Services::Configuration()
                    ->get('metadata_content_rights', ''));
        }
        if (Services::Registry()->get('request\\metadata_robots', '') == '') {
            Services::Registry()->set('request\\metadata_robots',
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
     * @return    bool
     * @since    1.0
     */
    protected function _getTheme()
    {
        $row = Molajo::Helper()
            ->get('Theme',
                Services::Registry()
                    ->get('request\\theme_id')
            );
        var_dump($row);
        die;
        if (count($row) == 0) {
            if (Services::Registry()->set('request\\theme_name') == 'system') {
                // error
            } else {
                Services::Registry()
                    ->set('request\\theme_name', 'system');
                $row = Molajo::Helper()
                    ->get('Theme', Services::Registry()->get('request\\theme_name'));
                if (count($row) > 0) {
                    // error
                }
            }
        }
        Services::Registry()->set('request\\theme_name', $row->title);
        Services::Registry()->set('request\\theme_id', $row->extension_instance_id);

        Services::Registry()->set('request\\theme_asset_type_id', MOLAJO_ASSET_TYPE_EXTENSION_THEME);
        Services::Registry()->set('request\\theme_asset_id', $row->asset_id);
        Services::Registry()->set('request\\theme_view_group_id', $row->view_group_id);
        Services::Registry()->set('request\\theme_language', $row->language);

        Services::Registry()->set('request\\theme_custom_fields', $row->custom_fields);
        Services::Registry()->set('request\\theme_metadata', $row->metadata);

        $parameters = Services::Registry()->initialise();
        $parameters->loadString($row->parameters);
        Services::Registry()->set('request\\theme_parameters', $parameters);

        if (Services::Registry()->get('request\\page_view_id', 0) == 0) {
            Services::Registry()->set('request\\page_view_id', $parameters->get('page_view_id', 0));
        }

        Services::Registry()->set('request\\theme_path',
            Molajo::Helper()
                ->getPath('Theme', Services::Registry()->get('request\\theme_name')));

        Services::Registry()->set('request\\theme_path_url',
            Molajo::Helper()
                ->getPathURL('Theme', Services::Registry()->get('request\\theme_name')));

        Services::Registry()->set('request\\theme_favicon',
            Molajo::Helper()
                ->getFavicon('Theme', Services::Registry()->get('request\\theme_name')));

        return;
    }

    /**
     * _getPage
     *
     * Get Page Name using either the Page ID or the Page Name
     *
     * @return    bool
     * @since    1.0
     */
    protected function _getPage()
    {
        /** Get Name */
        Services::Registry()->set('request\\page_view_name',
            ExtensionHelper::getInstanceTitle(
                Services::Registry()->get('request\\page_view_id'),
                MOLAJO_ASSET_TYPE_EXTENSION_PAGE_VIEW,
                'Page'
            )
        );

        /** Page Path */
        $viewHelper = new ViewHelper(
            Services::Registry()->get('request\\page_view_name'),
            'Page',
            Services::Registry()->get('request\\extension_instance_name'),
            Services::Registry()->get('request\\extension_type'),
            Services::Registry()->get('request\\theme_name')
        );
        Services::Registry()->set('request\\page_view_path', $viewHelper->view_path);
        Services::Registry()->set('request\\page_view_path_url', $viewHelper->view_path_url);
        Services::Registry()->set('request\\page_view_include', $viewHelper->view_path . '/index.php');

        return;
    }

    /**
     * _getTemplateView
     *
     * Get Template View Paths
     *
     * @return    bool
     * @since    1.0
     */
    protected function _getTemplateView()
    {
        $this->set(
            'template_view_name',
            Molajo::Helper()->getInstanceTitle(
                'Extension',
                Services::Registry()->get('request\\template_view_id')
            )
        );

        $viewHelper = Molajo::Helper()->findPath('View',
            Services::Registry()->get('request\\template_view_name'),
            Services::Registry()->get('request\\view_type'),
            Services::Registry()->get('request\\extension_title'),
            Services::Registry()->get('request\\extension_instance_name'),
            Services::Registry()->get('request\\theme_name')
        );
        Services::Registry()->set('request\\template_view_path', $viewHelper->view_path);
        Services::Registry()->set('request\\template_view_path_url', $viewHelper->view_path_url);

        return;
    }

    /**
     * _getWrapView
     *
     * Get View Paths
     *
     * @return    bool
     * @since    1.0
     */
    protected function _getWrapView()
    {
        $this->set(
            'wrap_view_name',
            Molajo::Helper()
                ->getInstanceTitle('Extension',
                Services::Registry()->get('request\\wrap_view_id')
            )
        );

        $wrapHelper = Molajo::Helper()->findPath('View',
            Services::Registry()->get('request\\wrap_view_name'),
            'Wrap',
            Services::Registry()->get('request\\extension_title'),
            Services::Registry()->get('request\\extension_instance_name'),
            Services::Registry()->get('request\\theme_name')
        );
        Services::Registry()
            ->set('request\\wrap_view_path', $wrapHelper->view_path);
        Services::Registry()
            ->set('request\\wrap_view_path_url', $wrapHelper->view_path_url);

        return;
    }

    /**
     * _error
     *
     * Process an error condition
     *
     * @param   $code
     * @param   null $message
     *
     * @return  mixed
     * @since   1.0
     */
    protected function _error($code, $message = 'Internal server error')
    {
        Services::Registry()->set('request\\status_error', true);
        Services::Registry()->set('request\\mvc_controller', 'display');
        Services::Registry()->set('request\\mvc_task', 'display');
        Services::Registry()->set('request\\mvc_model', 'messages');

        /** default error theme and page */
        Services::Registry()->set('request\\theme_id',
            Services::Configuration()
                ->get('error_theme_id', 'system')
        );
        Services::Registry()->set('request\\page_view_id',
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

            Services::Response()
                ->setHeader('Status', '500 Internal server error', 'true');

            Services::Message()
                ->set($message, MOLAJO_MESSAGE_TYPE_ERROR, 500);
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
        Services::Response()
            ->setStatusCode(503);

        Services::Message()
            ->set(Services::Configuration()->get(
                'offline_message',
                'This site is not available.<br /> Please check back again soon.'
            ),
            MOLAJO_MESSAGE_TYPE_WARNING,
            503
        );

        Services::Registry()->set('request\\theme_id',
            Services::Configuration()
                ->get('offline_theme_id', 'system')
        );

        Services::Registry()->set('request\\page_view_id',
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
        Services::Response()
            ->setStatusCode(403);

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
        Services::Response()
            ->setStatusCode(404);

        Services::Message()
            ->set(
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
     * @return    array
     * @since    1.0
     */
    protected function _initialize()
    {
        Services::Registry()->create('parameters');
        Services::Registry()->create('request');

        /** request */
        Services::Registry()->set('request\\request_url_base',
            MOLAJO_BASE_URL);
        Services::Registry()->set('request\\request_asset_id', 0);
        Services::Registry()->set('request\\request_asset_type_id', 0);
        Services::Registry()->set('request\\request_url_query', '');
        Services::Registry()->set('request\\request_url', '');
        Services::Registry()->set('request\\request_url_sef', '');
        Services::Registry()->set('request\\request_url_home', false);

        /** menu item data */
        Services::Registry()->set('request\\menu_item_id', 0);
        Services::Registry()->set('request\\menu_item_title', '');
        Services::Registry()->set('request\\menu_item_asset_type_id',
            MOLAJO_ASSET_TYPE_MENU_ITEM_COMPONENT);
        Services::Registry()->set('request\\menu_item_asset_id', 0);
        Services::Registry()->set('request\\menu_item_view_group_id', 0);
        Services::Registry()->set('request\\menu_item_custom_fields', array());
        Services::Registry()->set('request\\menu_item_parameters', array());
        Services::Registry()->set('request\\menu_item_metadata', array());
        Services::Registry()->set('request\\menu_item_language', '');
        Services::Registry()->set('request\\menu_item_translation_of_id', 0);

        /** source data */
        Services::Registry()->set('request\\source_id', 0);
        Services::Registry()->set('request\\source_title', '');
        Services::Registry()->set('request\\source_asset_type_id', 0);
        Services::Registry()->set('request\\source_asset_id', 0);
        Services::Registry()->set('request\\source_view_group_id', 0);
        Services::Registry()->set('request\\source_custom_fields', array());
        Services::Registry()->set('request\\source_parameters', array());
        Services::Registry()->set('request\\source_metadata', array());
        Services::Registry()->set('request\\source_language', '');
        Services::Registry()->set('request\\source_translation_of_id', 0);
        Services::Registry()->set('request\\source_table', '');
        Services::Registry()->set('request\\source_last_modified', '');

        /** extension */
        Services::Registry()->set('request\\extension_instance_id', 0);
        Services::Registry()->set('request\\extension_instance_name', '');
        Services::Registry()->set('request\\extension_asset_type_id', 0);
        Services::Registry()->set('request\\extension_asset_id', 0);
        Services::Registry()->set('request\\extension_view_group_id', 0);
        Services::Registry()->set('request\\extension_custom_fields', array());
        Services::Registry()->set('request\\extension_metadata', array());
        Services::Registry()->set('request\\extension_parameters', array());
        Services::Registry()->set('request\\extension_path', '');
        Services::Registry()->set('request\\extension_type', '');
        Services::Registry()->set('request\\extension_event_type', '');

        /** primary category */
        Services::Registry()->set('request\\category_id', 0);
        Services::Registry()->set('request\\category_title', '');
        Services::Registry()->set('request\\category_asset_type_id',
            MOLAJO_ASSET_TYPE_CATEGORY_LIST);
        Services::Registry()->set('request\\category_asset_id', 0);
        Services::Registry()->set('request\\category_view_group_id', 0);
        Services::Registry()->set('request\\category_custom_fields', array());
        Services::Registry()->set('request\\category_parameters', array());
        Services::Registry()->set('request\\category_metadata', array());
        Services::Registry()->set('request\\category_language', '');
        Services::Registry()->set('request\\category_translation_of_id', 0);

        /** merged */
        Services::Registry()->set('request\\metadata_title', '');
        Services::Registry()->set('request\\metadata_description', '');
        Services::Registry()->set('request\\metadata_keywords', '');
        Services::Registry()->set('request\\metadata_author', '');
        Services::Registry()->set('request\\metadata_content_rights', '');
        Services::Registry()->set('request\\metadata_robots', '');
        Services::Registry()->set('request\\metadata_additional_array', array());

        /** theme */
        Services::Registry()->set('request\\theme_id', 0);
        Services::Registry()->set('request\\theme_name', '');
        Services::Registry()->set('request\\theme_asset_type_id',
            MOLAJO_ASSET_TYPE_EXTENSION_THEME);
        Services::Registry()->set('request\\theme_asset_id', 0);
        Services::Registry()->set('request\\theme_view_group_id', 0);
        Services::Registry()->set('request\\theme_custom_fields', array());
        Services::Registry()->set('request\\theme_metadata', array());
        Services::Registry()->set('request\\theme_parameters', array());
        Services::Registry()->set('request\\theme_path', '');
        Services::Registry()->set('request\\theme_path_url', '');
        Services::Registry()->set('request\\theme_include', '');
        Services::Registry()->set('request\\theme_favicon', '');

        /** page */
        Services::Registry()->set('request\\page_view_id', 0);
        Services::Registry()->set('request\\page_view_name', '');
        Services::Registry()->set('request\\page_view_css_id', '');
        Services::Registry()->set('request\\page_view_css_class', '');
        Services::Registry()->set('request\\page_view_asset_type_id',
            MOLAJO_ASSET_TYPE_EXTENSION_PAGE_VIEW);
        Services::Registry()->set('request\\page_view_asset_id', 0);
        Services::Registry()->set('request\\page_view_path', '');
        Services::Registry()->set('request\\page_view_path_url', '');
        Services::Registry()->set('request\\page_view_include', '');

        /** template */
        Services::Registry()->set('request\\template_view_id', 0);
        Services::Registry()->set('request\\template_view_name', '');
        Services::Registry()->set('request\\template_view_css_id', '');
        Services::Registry()->set('request\\template_view_css_class', '');
        Services::Registry()->set('request\\template_view_asset_type_id',
            MOLAJO_ASSET_TYPE_EXTENSION_TEMPLATE_VIEW);
        Services::Registry()->set('request\\template_view_asset_id', 0);
        Services::Registry()->set('request\\template_view_path', '');
        Services::Registry()->set('request\\template_view_path_url', '');

        /** wrap */
        Services::Registry()->set('request\\wrap_view_id', 0);
        Services::Registry()->set('request\\wrap_view_name', '');
        Services::Registry()->set('request\\wrap_view_css_id', '');
        Services::Registry()->set('request\\wrap_view_css_class', '');
        Services::Registry()->set('request\\wrap_view_asset_type_id',
            MOLAJO_ASSET_TYPE_EXTENSION_WRAP_VIEW);
        Services::Registry()->set('request\\wrap_view_asset_id', 0);
        Services::Registry()->set('request\\wrap_view_path', '');
        Services::Registry()->set('request\\wrap_view_path_url', '');

        /** mvc parameters */
        Services::Registry()->set('request\\mvc_controller', '');
        Services::Registry()->set('request\\mvc_option', '');
        Services::Registry()->set('request\\mvc_task', '');
        Services::Registry()->set('request\\mvc_model', '');
        Services::Registry()->set('request\\mvc_id', 0);
        Services::Registry()->set('request\\mvc_category_id', 0);
        Services::Registry()->set('request\\mvc_url_parameters', array());
        Services::Registry()->set('request\\mvc_suppress_no_results', false);

        /** results */
        Services::Registry()->set('request\\status_error', false);
        Services::Registry()->set('request\\status_authorised', false);
        Services::Registry()->set('request\\status_found', false);

        /**
         *  Display Controller saves the query results for the primary request
         *      extension for possible reuse by other extensions. MolajoRequestModel
         *      can be used to retrieve the data.
         */
        Services::Registry()->set('request\\query_rowset', array());
        Services::Registry()->set('request\\query_pagination', array());
        Services::Registry()->set('request\\query_state', array());
    }
}
