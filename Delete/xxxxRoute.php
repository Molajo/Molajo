<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application;

use Molajo\Services;
use Molajo\Extension\Helper;

defined('MOLAJO') or die;

/**
 * Route
 *
 * Establishes parameter values given inheritance chain:
 * 1. Menu Item
 *   -or - Detail source content
 * 2. Extension for content
 * 3. Primary category of content
 * 4. Application
 * 5. Hard-coded defaults
 *
 * @package    Molajo
 * @subpackage Route
 * @since      1.0
 */
Class Route
{
	/**
	 * $instance
	 *
	 * @var        object
	 * @since      1.0
	 */
	protected static $instance = null;

	/**
	 * getInstance
	 *
	 * Returns the global site object, creating if not existing
	 *
	 * @return  Application  object
	 * @since   1.0
	 */
	public static function getInstance()
	{
		if (self::$instance) {
		} else {
			self::$instance = new Route();
		}
		return self::$instance;
	}

    /**
     * Using the PAGE_REQUEST value:
     *  - retrieve the catalog record
     *  - set registry values needed to render output
     *
     * @return mixed
     * @since 1.0
     */
    public function process()
    {
		$this->initialise();

        /** Retrieve overrides */
		$override_request_url = Service::Registry()->get('DependencyInjection', 'request_url', '');
		$override_catalog_id = (int) Service::Registry()->get('DependencyInjection', 'catalog_id', 0);

		/** Specific catalog */
        if ((int)$override_catalog_id == 0) {
            Service::Registry()->set('Request', 'request_catalog_id', 0);
        } else {
            Service::Registry()->set('Request', 'request_catalog_id', $override_catalog_id);
        }

        /** Check for home duplicate content and redirect */
        $this->checkHome($override_request_url);

        if (Service::Redirect()->url === null
            && (int)Service::Redirect()->code == 0
        ) {
        } else {
            return false;
        }

        /** Offline Mode */
        if (Service::Registry()->get('Configuration', 'offline', 1) == 0) {
            $this->error(503);
        }

        /** URL parameters */
        $this->getRequest();

        /** Catalog, Access Control, links to source, menus, extensions, etc. */
        $this->getCatalog();

        /** Authorise */
        if (Service::Registry()->get('Request', 'status_found')) {
            $this->authoriseTask();
        }

        /** Route */
		if (Service::Registry()->get('Request', 'status_found') === false) {
			$this->error(404);
		}

		/** redirect */
		if ($this->redirect_to_id == 0) {
		} else {
			Service::Response()->redirect(
				Application::Helper()->getURL('Catalog', $this->redirect_to_id),
				301
			);
		}

		/** must be logged on */
		if (Service::Registry()->get('Configuration', 'logon_requirement', 0) > 0
			&& Service::Registry()->get('User', 'guest', true) === true
			&& Service::Registry()->get('Request', 'request_catalog_id')
				<> Service::Registry()->get('Configuration', 'logon_requirement', 0)
		) {
			Service::Response()->redirect(
				Service::Registry()->get('Configuration', 'logon_requirement', 0), 303
			);
		}

        /** Action: Render Page */
        if (Service::Registry()->get('Request', 'mvc_controller') == 'display') {
            $this->getUser();
            $this->getApplicationDefaults();
            $this->getTheme();
            $this->getPageView();
            $this->getTemplateView();
            $this->getWrapView();

            $temp = Service::Registry()->initialise();

            $temp->loadArray($this->parameters);
            $this->parameters = $temp;

        } else {

            /** Action: Database action */
            $temp = Service::Registry()->initialise();
            $temp->loadArray($this->parameters);
            $this->parameters = $temp;

            if (Service::Registry()->get('Configuration', 'sef', 1) == 0) {
                $link = $this->page_request->get('request_url_sef');
            } else {
                $link = $this->page_request->get('request_url');
            }
            Service::Registry()->set('Request', 'redirect_on_failure', $link);

            Service::Registry()->set('Request', 'model',
                ucfirst(trim(Service::Registry()->get('Request', 'mvc_model'))) . 'Model');
            $cc = 'Molajo' . ucfirst(Service::Registry()->get('Request', 'mvc_controller')) . 'Controller';
            Service::Registry()->set('Request', 'controller', $cc);
            $task = Service::Registry()->get('Request', 'mvc_task');
            Service::Registry()->set('Request', 'task', $task);
            Service::Registry()->set('Request', 'id', Service::Registry()->get('Request', 'mvc_id'));
            $controller = new $cc($this->page_request, $this->parameters);

            /** execute task: non-display, edit, or add tasks */
            return $controller->$task();
        }

        return $this;
    }

    /**
     * Determine if URL is duplicate content for home (and issue redirect, if necessary)
     *
     * @param $override_request_url
     *
     * @return mixed
     * @since  1.0
     */
    protected function checkHome($override_request_url)
    {
        /**
         * Specific URL path
         *  Request is stripped of Host, Folder, and Application
         *  Path ex. index.php?option=login or access/groups
         */
        if ($override_request_url == null) {
            $path = PAGE_REQUEST;
        } else {
            $path = $override_request_url;
        }

        /** duplicate content: URLs without the .html */
        if ((int)Service::Registry()->get('Configuration', 'sef_suffix', 1) == 1
            && substr($path, -11) == '/index.html'
        ) {
            $path = substr($path, 0, (strlen($path) - 11));
        }
        if ((int)Service::Registry()->get('Configuration', 'sef_suffix', 1) == 1
            && substr($path, -5) == '.html'
        ) {
            $path = substr($path, 0, (strlen($path) - 5));
        }

        /** populate value used in query  */
        Service::Registry()->set('Request', 'request_url_query', $path);

        /** home: duplicate content - redirect */
        if (Service::Registry()->get('Request', 'request_url_query', '') == 'index.php'
            || Service::Registry()->get('Request', 'request_url_query', '') == 'index.php/'
            || Service::Registry()->get('Request', 'request_url_query', '') == 'index.php?'
            || Service::Registry()->get('Request', 'request_url_query', '') == '/index.php/'
        ) {
            return Service::Redirect()->set('', 301);
        }

        /** Home */
        if (Service::Registry()->get('Request', 'request_url_query', '') == ''
            && (int)Service::Registry()->get('Request', 'request_catalog_id', 0) == 0
        ) {
            Service::Registry()->set('Request', 'request_catalog_id',
                Service::Registry()->get('Configuration', 'home_catalog_id', 0));
            Service::Registry()->set('Request', 'request_url_home', true);
        }

        Service::Debug()->set('Application::Request()->checkHome complete');

        return true;
    }

    /**
     * Retrieve URL contents
     *
     * @return bool
     * @since 1.0
     */
    protected function getRequest()
    {
        // echo 'Ajax ' . Service::Request()->request->isXmlHttpRequest().'<br />';
        //$queryString = Service::Request()->get('option');

        $queryString = Service::Request()->request->getQueryString();
        $pair = explode('&', $queryString);
        $pairs = array();
        $extra = array();

        if (count($pairs) > 0) {
            $xml = CONFIGURATION_FOLDER . '/nonroutable.xml';
            if (is_file($xml)) {
            } else {
                return false;
            }
            $parameters = simplexml_load_file($xml);
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
            Service::Registry()->set('Request', 'mvc_task', $pairs['task']);
        } else {
            Service::Registry()->set('Request', 'mvc_task', 'display');
        }

        if (Service::Registry()->get('Request', 'mvc_task', '') == ''
            || Service::Registry()->get('Request', 'mvc_task', 'display') == 'display'
        ) {
            $pageRequest = Service::Registry()->get('Request', 'request_url_query');

            if (strripos($pageRequest, '/edit') == (strlen($pageRequest) - 5)) {
                $pageRequest = substr($pageRequest, 0, strripos($pageRequest, '/edit'));
                Service::Registry()->set('Request', 'request_url_query', $pageRequest);
                Service::Registry()->set('Request', 'mvc_task', 'edit');

            } else if (strripos($pageRequest, '/add') == (strlen($pageRequest) - 4)) {
                $pageRequest = substr($pageRequest, 0, strripos($pageRequest, '/add'));
                Service::Registry()->set('Request', 'request_url_query', $pageRequest);
                Service::Registry()->set('Request', 'mvc_task', 'add');

            } else {
                Service::Debug()->set('Application::Request()->getRequest() complete Display Task');
                Service::Registry()->set('Request', 'mvc_task', 'display');
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
            Service::Registry()->set('Request', 'redirect_on_success', '');
        } else if (JUri::isInternal(base64_decode($return))) {
            Service::Registry()->set('Request', 'redirect_on_success', base64_decode($return));
        } else {
            Service::Registry()->set('Request', 'redirect_on_success', '');
        }

        /** option */
        Service::Registry()->set('Request', 'mvc_option', (string)$pairs['option']);

        /** catalog information */
        Service::Registry()->set('Request', 'mvc_id', (int)$pairs['id']);

        Service::Debug()->set('Application::Request()->getRequest()');

        return true;
    }

    /**
     * Retrieve Catalog and Catalog Type data for a specific catalog id
     * or query request
     *
     * @return    boolean
     * @since    1.0
     */
    protected function getCatalog()
    {

        $row = Application::Helper()
            ->get('Catalog',
            (int)Service::Registry()->get('Request', 'request_catalog_id'),
            Service::Registry()->get('Request', 'request_url_query'),
            Service::Registry()->get('Request', 'mvc_option'),
            Service::Registry()->get('Request', 'mvc_id')
        );

        /** 404: routeRequest handles redirecting to error page */
        if (count($row) == 0
            || (int)$row->routable == 0
        ) {
            return Service::Registry()->set('Request', 'status_found', false);
        }

        /** Redirect: routeRequest handles rerouting the request */
        if ((int)$row->redirect_to_id == 0) {
        } else {
            $this->redirect_to_id = (int)$row->redirect_to_id;
            return Service::Registry()->set('Request', 'status_found', false);
        }

        /** 403: authoriseTask handles redirecting to error page */
        if (in_array($row->view_group_id, Service::Registry()->get('User', 'ViewGroups'))) {
            Service::Registry()->set('Request', 'status_authorised', true);
        } else {
            return Service::Registry()->set('Request', 'status_authorised', false);
        }

        /** request url */
        Service::Registry()->set('Request', 'request_catalog_id', (int)$row->catalog_id);
        Service::Registry()->set('Request', 'request_catalog_type_id', (int)$row->catalog_type_id);
        Service::Registry()->set('Request', 'request_url', $row->request);
        Service::Registry()->set('Request', 'request_url_sef', $row->sef_request);

        /** home */
        if ((int)Service::Registry()->get('Request', 'request_catalog_id', 0)
            == Service::Registry()->get('Configuration', 'home_catalog_id', null)
        ) {
            Service::Registry()->set('Request', 'request_url_home', true);
        } else {
            Service::Registry()->set('Request', 'request_url_home', false);
        }

        Service::Registry()->set('Request', 'source_table', $row->source_table);
        Service::Registry()->set('Request', 'category_id', (int)$row->primary_category_id);

        /** mvc options and url parameters */
        Service::Registry()->set('Request', 'extension_instance_name', $row->request_option);
        Service::Registry()->set('Request', 'mvc_model', $row->request_model);
        Service::Registry()->set('Request', 'mvc_id', (int)$row->source_id);

        Service::Registry()->set('Request', 'mvc_controller',
            Service::Access()
                ->getTaskController(Service::Registry()->get('Request', 'mvc_task'))
        );

        /** Action Tasks need no additional information */
        if (Service::Registry()->get('Request', 'mvc_controller') == 'display') {
        } else {
            return Service::Registry()->set('Request', 'status_found', true);
        }

        if (Service::Registry()->get('Request', 'request_catalog_type_id')
            == CATALOG_TYPE_MENU_ITEM_COMPONENT
        ) {
            Service::Registry()->set('Request', 'menu_item_id', $row->source_id);
            $this->getMenuItem();
            if (Service::Registry()->get('Request', 'status_found') === false) {
                return Service::Registry()->get('Request', 'status_found');
            }
        } else {
            Service::Registry()->set('Request', 'source_id', $row->source_id);
            $this->getSource();
        }

        /** primary category */
        if (Service::Registry()->get('Request', 'category_id', 0) == 0) {
        } else {
            Service::Registry()->set('Request', 'mvc_category_id',
                Service::Registry()->get('Request', 'category_id'));
            $this->getPrimaryCategory();
        }

        /** Extension */
        $this->getExtension();

        return Service::Registry()->get('Request', 'status_found');
    }

    /**
     * Verify user authorization for task
     *
     * @return   boolean
     * @since    1.0
     */
    protected function authoriseTask()
    {
        /** display view verified in getCatalog */
        if (Service::Registry()->get('Request', 'mvc_task') == 'display'
            && Service::Registry()->get('Request', 'status_authorised') === true
        ) {
            return true;
        }
        if (Service::Registry()->get('Request', 'mvc_task') == 'display'
            && Service::Registry()->get('Request', 'status_authorised') === false
        ) {
            $this->error(403);
            return false;
        }

        /** verify other tasks */
        Service::Registry()->set('Request', 'status_authorised',
            Service::Access()->authoriseTask(
                Service::Registry()->get('Request', 'mvc_task'),
                Service::Registry()->get('Request', 'request_catalog_id')
            )
        );

        if (Service::Registry()->get('Request', 'status_authorised') === true) {
        } else {
            $this->error(403);
            return false;
        }

        return true;
    }

    /**
     * Retrieve the Menu Item Data
     *
     * @return  boolean
     * @since   1.0
     */
    protected function getMenuItem()
    {
        $row = Application::Helper()
            ->get('MenuItem',
            (int)Service::Registry()->get('Request', 'menu_item_id')
        );

        /**
         *  403: Unauthorised Access
         *
         *  If the menu item doesn't return, it's likely that the user, application
         *  or site do not have access to the menu (extension_instance).
         *
         *  Since the catalog record was found, it is likely not a 404
         *
         *  Will be treating like a 404 for now
         *
         *  authoriseTask handles redirecting to error page
         */
        if (count($row) == 0) {
            Service::Registry()->set('Request', 'status_authorised', false);
            return Service::Registry()->set('Request', 'status_found', false);
        }

        Service::Registry()->set('Request', 'menu_item_title', $row->menu_item_title);
        Service::Registry()->set('Request', 'menu_item_catalog_type_id', $row->menu_item_catalog_type_id);
        Service::Registry()->set('Request', 'menu_item_catalog_id', $row->menu_item_catalog_id);
        Service::Registry()->set('Request', 'menu_item_view_group_id', $row->menu_item_view_group_id);

        Service::Registry()->set('Request', 'extension_instance_id', $row->menu_id);
        Service::Registry()->set('Request', 'extension_instance_name', $row->menu_title);
        Service::Registry()->set('Request', 'extension_instance_catalog_type_id', $row->menu_catalog_type_id);
        Service::Registry()->set('Request', 'extension_instance_catalog_id', $row->menu_catalog_id);
        Service::Registry()->set('Request', 'extension_instance_view_group_id', $row->menu_view_group_id);

        $parameters = Service::Registry()->initialise();
        $parameters->loadString($row->menu_item_parameters);
        Service::Registry()->set('Request', 'menu_item_parameters', $parameters);

        $custom_fields = Service::Registry()->initialise();
        $custom_fields->loadString($row->menu_item_custom_fields);
        Service::Registry()->set('Request', 'menu_item_custom_fields', $custom_fields);

        $metadata = Service::Registry()->initialise();
        $metadata->loadString($row->menu_item_metadata);
        Service::Registry()->set('Request', 'menu_item_metadata', $metadata);

        $this->setPageValues($parameters, $metadata);

        Service::Registry()->set('Request', 'menu_item_language', $row->menu_item_language);
        Service::Registry()->set('Request', 'menu_item_translation_of_id', $row->menu_item_translation_of_id);

        /** mvc */
        if (Service::Registry()->get('Request', 'mvc_controller', '') == '') {
            Service::Registry()->set('Request', 'mvc_controller',
                $parameters->get('controller', '')
            );
        }
        if (Service::Registry()->get('Request', 'mvc_task', '') == '') {
            Service::Registry()->set('Request', 'mvc_task',
                $parameters->get('task', '')
            );
        }
        if (Service::Registry()->get('Request', 'extension_instance_name', '') == '') {
            Service::Registry()->set('Request', 'extension_instance_name',
                $parameters->get('option', '')
            );
        }
        if (Service::Registry()->get('Request', 'mvc_model', '') == '') {
            Service::Registry()->set('Request', 'mvc_model',
                $parameters->get('model', '')
            );
        }
        if ((int)Service::Registry()->get('Request', 'mvc_id', 0) == 0) {
            Service::Registry()->set('Request', 'mvc_id', $parameters->get('id', 0));
        }
        if ((int)Service::Registry()->get('Request', 'mvc_category_id', 0) == 0) {
            Service::Registry()->set('Request', 'mvc_category_id',
                $parameters->get('category_id', 0)
            );
        }
        if ((int)Service::Registry()->get('Request', 'mvc_suppress_no_results', 0) == 0) {
            Service::Registry()->set('Request', 'mvc_suppress_no_results',
                $parameters->get('suppress_no_results', 0)
            );
        }

        return Service::Registry()->set('Request', 'status_found', true);
    }

    /**
     * getSource
     *
     * Retrieve Parameters and Metadata for Source Detail
     *
     * @return  bool
     * @since   1.0
     */
    protected function getSource()
    {
        $row = Application::Helper()
            ->get('Content',
            (int)Service::Registry()->get('Request', 'source_id'),
            Service::Registry()->get('Request', 'source_table')
        );

        if (count($row) == 0) {
            return true;
        }
        //        if (count($row) == 0) {
        //            /** 500: Source Content not found */
        //            Service::Registry()->set('Request', 'status_found', false);
        //            Service::Message()
        //                ->set(
        //                $message = Service::Language()->translate('ERROR_SOURCE_ITEM_NOT_FOUND'),
        //                $type = MESSAGE_TYPE_ERROR,
        //                $code = 500,
        //                $debug_location = 'MolajoRequest::getSource',
        //                $debug_object = $this->page_request
        //            );
        //            return Service::Registry()->set('Request', 'status_found', false);
        //        }

        /** match found */
        Service::Registry()->set('Request', 'source_title', $row->title);
        Service::Registry()->set('Request', 'source_catalog_type_id', $row->catalog_type_id);
        Service::Registry()->set('Request', 'source_catalog_id', $row->catalog_id);
        Service::Registry()->set('Request', 'source_view_group_id', $row->view_group_id);
        Service::Registry()->set('Request', 'source_language', $row->language);
        Service::Registry()->set('Request', 'source_translation_of_id', $row->translation_of_id);
        Service::Registry()->set('Request', 'source_last_modified', $row->modified_datetime);

        Service::Registry()->set('Request', 'extension_instance_id', $row->extension_instance_id);

        $custom_fields = Service::Registry()->initialise();
        $custom_fields->loadString($row->custom_fields);
        Service::Registry()->set('Request', 'source_custom_fields', $custom_fields);

        $metadata = Service::Registry()->initialise();
        $metadata->loadString($row->metadata);
        Service::Registry()->set('Request', 'source_metadata', $metadata);

        $parameters = Service::Registry()->initialise();
        $parameters->loadString($row->parameters);
        $parameters->set('id', $row->id);
        $parameters->set('catalog_type_id', $row->catalog_type_id);
        Service::Registry()->set('Request', 'source_parameters', $parameters);

        $this->setPageValues($parameters, $metadata);

        /** mvc */
        if (Service::Registry()->get('Request', 'mvc_controller', '') == '') {
            Service::Registry()->set('Request', 'mvc_controller',
                $parameters->get('controller', ''));
        }
        if (Service::Registry()->get('Request', 'mvc_task', '') == '') {
            Service::Registry()->set('Request', 'mvc_task',
                $parameters->get('task', ''));
        }
        if (Service::Registry()->get('Request', 'extension_instance_name', '') == '') {
            Service::Registry()->set('Request', 'extension_instance_name',
                $parameters->get('option', ''));
        }
        if (Service::Registry()->get('Request', 'mvc_model', '') == '') {
            Service::Registry()->set('Request', 'mvc_model',
                $parameters->get('model', ''));
        }
        if ((int)Service::Registry()->get('Request', 'mvc_id', 0) == 0) {
            Service::Registry()->set('Request', 'mvc_id',
                $parameters->get('id', 0));
        }
        if ((int)Service::Registry()->get('Request', 'mvc_category_id', 0) == 0) {
            Service::Registry()->set('Request', 'mvc_category_id',
                $parameters->get('category_id', 0));
        }
        if ((int)Service::Registry()->get('Request', 'mvc_suppress_no_results', 0) == 0) {
            Service::Registry()->set('Request', 'mvc_suppress_no_results',
                $parameters->get('suppress_no_results', 0));
        }

        return Service::Registry()->set('Request', 'status_found', true);
    }

    /**
     * getPrimaryCategory
     *
     * Retrieve the Menu Item Parameters and Meta Data
     *
     * @return  boolean
     * @since   1.0
     */
    protected function getPrimaryCategory()
    {
        $row = Application::Helper()
            ->get('Content',
            (int)Service::Registry()->get('Request', 'category_id'),
            '#__content'
        );

        if (count($row) == 0) {
            /** 500: Category not found */
            Service::Registry()->set('Request', 'status_found', false);
            Service::Message()
                ->set(
                $message = Service::Language()->translate('ERROR_SOURCE_ITEM_NOT_FOUND'),
                $type = MESSAGE_TYPE_ERROR,
                $code = 500,
                $debug_location = 'MolajoRequest::getPrimaryCategory',
                $debug_object = $this->page_request
            );
            return Service::Registry()->set('Request', 'status_found', false);
        }

        Service::Registry()->set('Request', 'category_title', $row->title);
        Service::Registry()->set('Request', 'category_catalog_type_id', $row->catalog_type_id);
        Service::Registry()->set('Request', 'category_catalog_id', $row->catalog_id);
        Service::Registry()->set('Request', 'category_view_group_id', $row->view_group_id);
        Service::Registry()->set('Request', 'category_language', $row->language);
        Service::Registry()->set('Request', 'category_translation_of_id', $row->translation_of_id);

        $custom_fields = Service::Registry()->initialise();
        $custom_fields->loadString($row->custom_fields);
        Service::Registry()->set('Request', 'category_custom_fields', $custom_fields);

        $metadata = Service::Registry()->initialise();
        $metadata->loadString($row->metadata);
        Service::Registry()->set('Request', 'category_metadata', $metadata);

        $parameters = Service::Registry()->initialise();
        $parameters->loadString($row->parameters);
        Service::Registry()->set('Request', 'category_parameters', $parameters);

        $this->setPageValuesDefaults($parameters, $metadata);

        return Service::Registry()->set('Request', 'status_found', true);
    }

    /**
     * Retrieve extension information for Component Request
     *
     * @return    bool
     * @since    1.0
     */
    protected function getExtension()
    {
        /** Retrieve Extension Query Results */
        if (Service::Registry()->get('Request', 'extension_instance_id', 0) == 0) {
        } else {
            $rows = Application::Helper()
                ->get('Extension', 0,
                (int)Service::Registry()->get('Request', 'extension_instance_id')
            );
        }

        /** Fatal error if Extension cannot be found */
        if ((Service::Registry()->get('Request', 'extension_instance_id', 0) == 0)
            || (count($rows) == 0)
        ) {
            /** 500: Extension not found */
            Service::Message()
                ->set(
                $message = Service::Language()
                    ->translate('ERROR_EXTENSION_NOT_FOUND'),
                $type = MESSAGE_TYPE_ERROR,
                $code = 500,
                $debug_location = 'MolajoRequest::getExtension',
                $debug_object = $this->page_request
            );
            return Service::Registry()->set('Request', 'status_found', false);
        }

        /** Process Results */
        $row = array();
        foreach ($rows as $row) {
        }

        Service::Registry()->set('Request', 'extension_instance_name', $row->title);
        Service::Registry()->set('Request', 'extension_catalog_id', $row->catalog_id);
        Service::Registry()->set('Request', 'extension_catalog_type_id', $row->catalog_type_id);
        Service::Registry()->set('Request', 'extension_view_group_id', $row->view_group_id);
        Service::Registry()->set('Request', 'extension_type', $row->catalog_type_title);

        $custom_fields = Service::Registry()->initialise();
        $custom_fields->loadString($row->custom_fields);
        Service::Registry()->set('Request', 'extension_custom_fields', $custom_fields);

        $metadata = Service::Registry()->initialise();
        $metadata->loadString($row->metadata);
        Service::Registry()->set('Request', 'extension_metadata', $metadata);

        $parameters = Service::Registry()->initialise();
        $parameters->loadString($row->parameters);
        Service::Registry()->set('Request', 'extension_parameters', $parameters);

        $this->setPageValuesDefaults($parameters, $metadata);

        /** mvc */
        if (Service::Registry()->get('Request', 'mvc_controller', '') == '') {
            Service::Registry()->set('Request', 'mvc_controller',
                $parameters->get('controller', '')
            );
        }
        if (Service::Registry()->get('Request', 'mvc_task', '') == '') {
            Service::Registry()->set('Request', 'mvc_task',
                $parameters->get('task', 'display')
            );
        }
        if (Service::Registry()->get('Request', 'mvc_model', '') == '') {
            Service::Registry()->set('Request', 'mvc_model',
                $parameters->get('model', 'content')
            );
        }
        if ((int)Service::Registry()->get('Request', 'mvc_id', 0) == 0) {
            Service::Registry()->set('Request', 'mvc_id',
                $parameters->get('id', 0)
            );
        }
        if ((int)Service::Registry()->get('Request', 'mvc_category_id', 0) == 0) {
            Service::Registry()->set('Request', 'mvc_category_id',
                $parameters->get('category_id', 0)
            );
        }
        if ((int)Service::Registry()->get('Request', 'mvc_suppress_no_results', 0) == 0) {
            Service::Registry()->set('Request', 'mvc_suppress_no_results',
                $parameters->get('suppress_no_results', 0)
            );
        }

        Service::Registry()->set('Request', 'extension_event_type',
            $parameters->get('event_type', array('content'))
        );

        Service::Registry()->set('Request', 'extension_path',
            Application::Helper()->getPath('Extension',
                Service::Registry()->get('Request', 'extension_catalog_type_id'),
                Service::Registry()->get('Request', 'extension_instance_name')
            )
        );

        return Service::Registry()->set('Request', 'status_found', true);
    }

    /**
     * Called by content item and menu item methods
     * Set the values needed to generate the page
     * (theme, page, view, wrap, and various metadata)
     *
     * @param null $parameters
     * @param null $metadata
     * @internal param null $sourceParameters
     * @internal param null $sourceMetadata
     *
     * @return    bool
     * @since    1.0
     */
    protected function setPageValues($parameters = null, $metadata = null)
    {
        if ((int)Service::Registry()->get('Request', 'theme_id', 0) == 0) {
            Service::Registry()->set('Request', 'theme_id',
                $parameters->get('theme_id', 0)
            );
        }
        if ((int)Service::Registry()->get('Request', 'page_view_id', 0) == 0) {
            Service::Registry()->set('Request', 'page_view_id',
                $parameters->get('page_view_id', 0)
            );
        }

        if ((int)Service::Registry()->get('Request', 'template_view_id', 0) == 0) {
            Service::Registry()->set('Request', 'template_view_id',
                $parameters->get('template_view_id', 0)
            );
        }

        if ((int)Service::Registry()->get('Request', 'wrap_view_id', 0) == 0) {
            Service::Registry()->set('Request', 'wrap_view_id',
                $parameters->get('wrap_view_id', 0)
            );
        }

        $this->parameters =
            Application::Helper()
                ->mergeParameters(
                'Extension',
                $parameters,
                $this->parameters
            );

        /** merge meta data */
        if (Service::Registry()->get('Request', 'metadata_title', '') == '') {
            Service::Registry()->set('Request', 'metadata_title',
                $metadata->get('metadata_title', '')
            );
        }
        if (Service::Registry()->get('Request', 'metadata_description', '') == '') {
            Service::Registry()->set('Request', 'metadata_description',
                $metadata->get('metadata_description', '')
            );
        }
        if (Service::Registry()->get('Request', 'metadata_keywords', '') == '') {
            Service::Registry()->set('Request', 'metadata_keywords',
                $metadata->get('metadata_keywords', '')
            );
        }
        if (Service::Registry()->get('Request', 'metadata_author', '') == '') {
            Service::Registry()->set('Request', 'metadata_author',
                $metadata->get('metadata_author', '')
            );
        }
        if (Service::Registry()->get('Request', 'metadata_content_rights', '') == '') {
            Service::Registry()->set('Request', 'metadata_content_rights',
                $metadata->get('metadata_content_rights', '')
            );
        }
        if (Service::Registry()->get('Request', 'metadata_robots', '') == '') {
            Service::Registry()->set('Request', 'metadata_robots',
                $metadata->get('metadata_robots', '')
            );
        }

        return;
    }

    /**
     *  Called by Category and Extension Methods
     *
     * @param null $parameters
     * @param null $metadata
     * @return    bool
     * @since    1.0
     */
    protected function setPageValuesDefaults($parameters = null, $metadata = null)
    {
        if (Service::Registry()->get('Request', 'theme_id', 0) == 0) {
            Service::Registry()->set('Request', 'theme_id', $parameters->get('default_theme_id', 0));
        }

        if (Service::Registry()->get('Request', 'page_view_id', 0) == 0) {
            Service::Registry()->set('Request', 'page_view_id', $parameters->get('default_page_view_id', 0));
        }

        if ((int)Service::Registry()->get('Request', 'template_view_id', 0) == 0) {
            Service::Registry()->set('Request', 'template_view_id',
                ViewHelper::getViewDefaultsOther(
                    'template',
                    Service::Registry()->get('Request', 'mvc_task', ''),
                    (int)Service::Registry()->get('Request', 'mvc_id', 0),
                    $parameters)
            );
        }

        if ((int)Service::Registry()->get('Request', 'wrap_view_id', 0) == 0) {
            Service::Registry()->set('Request', 'wrap_view_id',
                ViewHelper::getViewDefaultsOther(
                    'wrap',
                    Service::Registry()->get('Request', 'mvc_task', ''),
                    (int)Service::Registry()->get('Request', 'mvc_id', 0),
                    $parameters)
            );
        }

        /** metadata  */
        if (Service::Registry()->get('Request', 'metadata_title', '') == '') {
            Service::Registry()->set('Request', 'metadata_title',
                Service::Registry()->get('Configuration', 'metadata_title', ''));
        }
        if (Service::Registry()->get('Request', 'metadata_description', '') == '') {
            Service::Registry()->set('Request', 'metadata_description',
                Service::Registry()->get('Configuration', 'metadata_description', ''));
        }
        if (Service::Registry()->get('Request', 'metadata_keywords', '') == '') {
            Service::Registry()->set('Request', 'metadata_keywords',
                Service::Registry()->get('Configuration', 'metadata_keywords', ''));
        }
        if (Service::Registry()->get('Request', 'metadata_author', '') == '') {
            Service::Registry()->set('Request', 'metadata_author',
                Service::Registry()->get('Configuration', 'metadata_author', ''));
        }
        if (Service::Registry()->get('Request', 'metadata_content_rights', '') == '') {
            Service::Registry()->set('Request', 'metadata_content_rights',
                Service::Registry()->get('Configuration', 'metadata_content_rights', ''));
        }
        if (Service::Registry()->get('Request', 'metadata_robots', '') == '') {
            Service::Registry()->set('Request', 'metadata_robots',
                Service::Registry()->get('Configuration', 'metadata_robots', ''));
        }

        $this->parameters = Application::Helper()->mergeParameters('Extension',
            $parameters,
            $this->parameters
        );

        return;
    }

    /**
     * Retrieve theme for user (if theme and/or page view not available)
     *
     * @return    bool
     * @since    1.0
     */
    protected function getUser()
    {

        if (Service::Registry()->get('Request', 'theme_id', 0) == 0) {
            Service::Registry()->set('Request', 'theme_id',
                Service::Registry()->get('UserParameters\\user_theme_id', 0));
        }
        if (Service::Registry()->get('Request', 'page_view_id', 0) == 0) {
            Service::Registry()->set('Request', 'page_view_id',
                Service::Registry()->get('UserParameters\\user_page_view_id', 0));
        }

        return;
    }

    /**
     * Retrieve Theme and Page from the final level of default values, if needed
     *
     * @return    bool
     * @since    1.0
     */
    protected function getApplicationDefaults()
    {
        if (Service::Registry()->get('Request', 'theme_id', 0) == 0) {
            Service::Registry()->set('Request', 'theme_id',
                Service::Registry()->get('Configuration', 'default_theme_id', ''));
        }

        if (Service::Registry()->get('Request', 'page_view_id', 0) == 0) {
            Service::Registry()->set('Request', 'page_view_id',
                Service::Registry()->get('Configuration', 'default_page_view_id', ''));
        }

        if ((int)Service::Registry()->get('Request', 'template_view_id', 0) == 0) {
            Service::Registry()->set('Request', 'template_view_id',
                Application::Helper()
                    ->getViewDefaultsApplication('View', 'template',
                    Service::Registry()->get('Request', 'mvc_task', ''),
                    (int)Service::Registry()->get('Request', 'mvc_id', 0))
            );
        }

        if ((int)Service::Registry()->get('Request', 'wrap_view_id', 0) == 0) {
            Service::Registry()->set('Request', 'wrap_view_id',
                Application::Helper()->getViewDefaultsApplication('View', 'wrap', Service::Registry()->get('Request', 'mvc_task', ''), (int)Service::Registry()->get('Request', 'mvc_id', 0))
            );
        }

        /** metadata  */
        if (Service::Registry()->get('Request', 'metadata_title', '') == '') {
            Service::Registry()->set('Request', 'metadata_title',
                Service::Registry()->get('Configuration', 'metadata_title', ''));
        }
        if (Service::Registry()->get('Request', 'metadata_description', '') == '') {
            Service::Registry()->set('Request', 'metadata_description',
                Service::Registry()->get('Configuration', 'metadata_description', ''));
        }
        if (Service::Registry()->get('Request', 'metadata_keywords', '') == '') {
            Service::Registry()->set('Request', 'metadata_keywords',
                Service::Registry()->get('Configuration', 'metadata_keywords', ''));
        }
        if (Service::Registry()->get('Request', 'metadata_author', '') == '') {
            Service::Registry()->set('Request', 'metadata_author',
                Service::Registry()->get('Configuration', 'metadata_author', ''));
        }
        if (Service::Registry()->get('Request', 'metadata_content_rights', '') == '') {
            Service::Registry()->set('Request', 'metadata_content_rights',
                Service::Registry()->get('Configuration', 'metadata_content_rights', ''));
        }
        if (Service::Registry()->get('Request', 'metadata_robots', '') == '') {
            Service::Registry()->set('Request', 'metadata_robots',
                Service::Registry()->get('Configuration', 'metadata_robots', ''));
        }
        return;
    }

    /**
     * Get Theme Name using either the Theme ID or the Theme Name
     *
     * @return    bool
     * @since    1.0
     */
    protected function getTheme()
    {
        $row = Application::Helper()
            ->get('Extension',
            CATALOG_TYPE_EXTENSION_THEME,
            Service::Registry()->get('Request', 'theme_id')
        );

        if (count($row) == 0) {
            if (Service::Registry()->set('Request', 'theme_name') == 'system') {
                // error
            } else {
                Service::Registry()
                    ->set('Request', 'theme_name', 'system');
                $row = Application::Helper()
                    ->get('Theme',
                    Service::Registry()->get('Request', 'theme_name')
                );
                if (count($row) > 0) {
                    // error
                }
            }
        }
        Service::Registry()->set('Request', 'theme_name', $row->title);
        Service::Registry()->set('Request', 'theme_id', $row->extension_instance_id);

        Service::Registry()->set('Request', 'theme_catalog_type_id', CATALOG_TYPE_EXTENSION_THEME);
        Service::Registry()->set('Request', 'theme_catalog_id', $row->catalog_id);
        Service::Registry()->set('Request', 'theme_view_group_id', $row->view_group_id);
        Service::Registry()->set('Request', 'theme_language', $row->language);

        Service::Registry()->set('Request', 'theme_custom_fields', $row->custom_fields);
        Service::Registry()->set('Request', 'theme_metadata', $row->metadata);

        $parameters = Service::Registry()->initialise();
        $parameters->loadString($row->parameters);
        Service::Registry()->set('Request', 'theme_parameters', $parameters);

        if (Service::Registry()->get('Request', 'page_view_id', 0) == 0) {
            Service::Registry()->set('Request', 'page_view_id', $parameters->get('page_view_id', 0));
        }

        Service::Registry()->set('Request', 'theme_path',
            Application::Helper()->getPath('Theme', Service::Registry()->get('Request', 'theme_name'))
        );

        Service::Registry()->set('Request', 'theme_path_url',
            Application::Helper()->getPathURL('Theme', Service::Registry()->get('Request', 'theme_name'))
        );

        Service::Registry()->set('Request', 'theme_favicon',
            Application::Helper()->getFavicon('Theme', Service::Registry()->get('Request', 'theme_name'))
        );

        return;
    }

    /**
     * Get Page Name using either the Page ID or the Page Name
     *
     * @return    bool
     * @since    1.0
     */
    protected function getPageView()
    {
        /** Get Name */
        Service::Registry()->set('Request', 'page_view_name',
            Application::Helper()->getInstanceTitle('Extension',
                Service::Registry()->get('Request', 'template_view_id')
            )
        );

        /** Page Path */
        $paths = Application::Helper()
            ->get(
            'View',
            Service::Registry()->get('Request', 'page_view_name'),
            'Page',
            Service::Registry()->get('Request', 'extension_instance_name'),
            Service::Registry()->get('Request', 'extension_type'),
            Service::Registry()->get('Request', 'theme_name')
        );

        if ($paths === false) {
            return false;
        }

        Service::Registry()->set('Request', 'page_view_path', $paths[0]);
        Service::Registry()->set('Request', 'page_view_path_url', $paths[1]);
        Service::Registry()->set('Request', 'page_view_include', $paths[0] . '/index.php');

        return true;
    }

    /**
     * Get Template View Paths
     *
     * @return    bool
     * @since    1.0
     */
    protected function getTemplateView()
    {
        /** Get Name */
        Service::Registry()->set('Request', 'template_view_name',
            Application::Helper()->getInstanceTitle('Extension',
                Service::Registry()->get('Request', 'template_view_id')
            )
        );
        echo Service::Registry()->get('Request', 'template_view_name');

        /** Page Path */
        $paths = Application::Helper()->get(
            'View',
            Service::Registry()->get('Request', 'template_view_name'),
            'Template',
            Service::Registry()->get('Request', 'extension_instance_name'),
            Service::Registry()->get('Request', 'extension_type'),
            Service::Registry()->get('Request', 'theme_name')
        );

        if ($paths === false) {
            return false;
        }

        Service::Registry()->set('Request', 'template_view_path', $paths[0]);
        Service::Registry()->set('Request', 'template_view_path_url', $paths[1]);

        return true;
    }

    /**
     * Get Wrap View Paths
     *
     * @return    bool
     * @since    1.0
     */
    protected function getWrapView()
    {
        $this->set('wrap_view_name',
            Application::Helper()->getInstanceTitle(
                'Extension',
                Service::Registry()->get('Request', 'wrap_view_id')
            )
        );

        $wrapHelper = Application::Helper()
            ->findPath(
            'View',
            Service::Registry()->get('Request', 'wrap_view_name'),
            'Wrap',
            Service::Registry()->get('Request', 'extension_title'),
            Service::Registry()->get('Request', 'extension_instance_name'),
            Service::Registry()->get('Request', 'theme_name')
        );

        Service::Registry()
            ->set('Request', 'wrap_view_path', $wrapHelper->view_path);
        Service::Registry()
            ->set('Request', 'wrap_view_path_url', $wrapHelper->view_path_url);

        return;
    }

    /**
     * Process an error condition
     *
     * @param   $code
     * @param null|string $message
     *
     * @return  mixed
     * @since   1.0
     */
    protected function error($code, $message = 'Internal server error')
    {
        Service::Registry()->set('Request', 'error_status', true);
        Service::Registry()->set('Request', 'mvc_controller', 'display');
        Service::Registry()->set('Request', 'mvc_task', 'display');
        Service::Registry()->set('Request', 'mvc_model', 'messages');

        /** default error theme and page */
        Service::Registry()->set('Request', 'theme_id',
            Service::Registry()->get('Configuration', 'error_theme_id', 'system')
        );
        Service::Registry()->set('Request', 'page_view_id',
            Service::Registry()->get('Configuration', 'error_page_view_id', 'error')
        );

        /** set header status, message and override default theme/page, if needed */
        if ($code == 503) {
            $this->error503();

        } else if ($code == 403) {
            $this->error403();

        } else if ($code = 404) {
            $this->error404();

        } else {

            Service::Response()
                ->setHeader('Status', '500 Internal server error', 'true');

            Service::Message()
                ->set($message, MESSAGE_TYPE_ERROR, 500);
        }
        return;
    }

    /**
     * Offline
     *
     * @return  null
     * @since   1.0
     */
    protected function error503()
    {
        Service::Response()
            ->setStatusCode(503);

        Service::Message()
            ->set(Service::Registry()->get('Configuration', 'offline_message',
                'This site is not available.<br /> Please check back again soon.'
            ),
            MESSAGE_TYPE_WARNING,
            503
        );

        Service::Registry()->set('Request', 'theme_id',
            Service::Registry()->get('Configuration', 'offline_theme_id', 'system')
        );

        Service::Registry()->set('Request', 'page_view_id',
            Service::Registry()->get('Configuration', 'offline_page_view_id', 'offline')
        );

        return;
    }

    /**
     * Not Authorised
     *
     * @return  null
     * @since   1.0
     */
    protected function error403()
    {
        Service::Response()
            ->setStatusCode(403);

        Service::Message()->set(
            Service::Registry()->get('Configuration', 'error_403_message', 'Not Authorised.'),
            MESSAGE_TYPE_ERROR,
            403
        );

        return;
    }

    /**
     * Page Not Found
     *
     * @return  null
     * @since   1.0
     */
    protected function error404()
    {
        Service::Response()
            ->setStatusCode(404);

        Service::Message()->set
        (Service::Registry()->get('Configuration', 'error_404_message', 'Page not found.'),
            MESSAGE_TYPE_ERROR,
            404);

        return;
    }

    /**
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
    protected function initialise()
    {
		Service::Registry()->create('parameters');
		Service::Registry()->create('request');

		/** request */
		Service::Registry()->set('Request', 'request_url_base',
			BASE_URL);
		Service::Registry()->set('Request', 'request_catalog_id', 0);
		Service::Registry()->set('Request', 'request_catalog_type_id', 0);
		Service::Registry()->set('Request', 'request_url_query', '');
		Service::Registry()->set('Request', 'request_url', '');
		Service::Registry()->set('Request', 'request_url_sef', '');
		Service::Registry()->set('Request', 'request_url_home', false);

		/** menu item data */
		Service::Registry()->set('Request', 'menu_item_id', 0);
		Service::Registry()->set('Request', 'menu_item_title', '');
		Service::Registry()->set('Request', 'menu_item_catalog_type_id',
			CATALOG_TYPE_MENU_ITEM_COMPONENT);
		Service::Registry()->set('Request', 'menu_item_catalog_id', 0);
		Service::Registry()->set('Request', 'menu_item_view_group_id', 0);
		Service::Registry()->set('Request', 'menu_item_custom_fields', array());
		Service::Registry()->set('Request', 'menu_item_parameters', array());
		Service::Registry()->set('Request', 'menu_item_metadata', array());
		Service::Registry()->set('Request', 'menu_item_language', '');
		Service::Registry()->set('Request', 'menu_item_translation_of_id', 0);

		/** source data */
		Service::Registry()->set('Request', 'source_id', 0);
		Service::Registry()->set('Request', 'source_title', '');
		Service::Registry()->set('Request', 'source_catalog_type_id', 0);
		Service::Registry()->set('Request', 'source_catalog_id', 0);
		Service::Registry()->set('Request', 'source_view_group_id', 0);
		Service::Registry()->set('Request', 'source_custom_fields', array());
		Service::Registry()->set('Request', 'source_parameters', array());
		Service::Registry()->set('Request', 'source_metadata', array());
		Service::Registry()->set('Request', 'source_language', '');
		Service::Registry()->set('Request', 'source_translation_of_id', 0);
		Service::Registry()->set('Request', 'source_table', '');
		Service::Registry()->set('Request', 'source_last_modified', '');

		/** extension */
		Service::Registry()->set('Request', 'extension_instance_id', 0);
		Service::Registry()->set('Request', 'extension_instance_name', '');
		Service::Registry()->set('Request', 'extension_catalog_type_id', 0);
		Service::Registry()->set('Request', 'extension_catalog_id', 0);
		Service::Registry()->set('Request', 'extension_view_group_id', 0);
		Service::Registry()->set('Request', 'extension_custom_fields', array());
		Service::Registry()->set('Request', 'extension_metadata', array());
		Service::Registry()->set('Request', 'extension_parameters', array());
		Service::Registry()->set('Request', 'extension_path', '');
		Service::Registry()->set('Request', 'extension_type', '');
		Service::Registry()->set('Request', 'extension_event_type', '');

		/** primary category */
		Service::Registry()->set('Request', 'category_id', 0);
		Service::Registry()->set('Request', 'category_title', '');
		Service::Registry()->set('Request', 'category_catalog_type_id',
			CATALOG_TYPE_CATEGORY_LIST);
		Service::Registry()->set('Request', 'category_catalog_id', 0);
		Service::Registry()->set('Request', 'category_view_group_id', 0);
		Service::Registry()->set('Request', 'category_custom_fields', array());
		Service::Registry()->set('Request', 'category_parameters', array());
		Service::Registry()->set('Request', 'category_metadata', array());
		Service::Registry()->set('Request', 'category_language', '');
		Service::Registry()->set('Request', 'category_translation_of_id', 0);

		/** merged */
		Service::Registry()->set('Request', 'metadata_title', '');
		Service::Registry()->set('Request', 'metadata_description', '');
		Service::Registry()->set('Request', 'metadata_keywords', '');
		Service::Registry()->set('Request', 'metadata_author', '');
		Service::Registry()->set('Request', 'metadata_content_rights', '');
		Service::Registry()->set('Request', 'metadata_robots', '');
		Service::Registry()->set('Request', 'metadata_additional_array', array());

		/** theme */
		Service::Registry()->set('Request', 'theme_id', 0);
		Service::Registry()->set('Request', 'theme_name', '');
		Service::Registry()->set('Request', 'theme_catalog_type_id',
			CATALOG_TYPE_EXTENSION_THEME);
		Service::Registry()->set('Request', 'theme_catalog_id', 0);
		Service::Registry()->set('Request', 'theme_view_group_id', 0);
		Service::Registry()->set('Request', 'theme_custom_fields', array());
		Service::Registry()->set('Request', 'theme_metadata', array());
		Service::Registry()->set('Request', 'theme_parameters', array());
		Service::Registry()->set('Request', 'theme_path', '');
		Service::Registry()->set('Request', 'theme_path_url', '');
		Service::Registry()->set('Request', 'theme_include', '');
		Service::Registry()->set('Request', 'theme_favicon', '');

		/** page */
		Service::Registry()->set('Request', 'page_view_id', 0);
		Service::Registry()->set('Request', 'page_view_name', '');
		Service::Registry()->set('Request', 'page_view_css_id', '');
		Service::Registry()->set('Request', 'page_view_css_class', '');
		Service::Registry()->set('Request', 'page_view_catalog_type_id',
			CATALOG_TYPE_EXTENSION_PAGE_VIEW);
		Service::Registry()->set('Request', 'page_view_catalog_id', 0);
		Service::Registry()->set('Request', 'page_view_path', '');
		Service::Registry()->set('Request', 'page_view_path_url', '');
		Service::Registry()->set('Request', 'page_view_include', '');

		/** template */
		Service::Registry()->set('Request', 'template_view_id', 0);
		Service::Registry()->set('Request', 'template_view_name', '');
		Service::Registry()->set('Request', 'template_view_css_id', '');
		Service::Registry()->set('Request', 'template_view_css_class', '');
		Service::Registry()->set('Request', 'template_view_catalog_type_id',
			CATALOG_TYPE_EXTENSION_TEMPLATE_VIEW);
		Service::Registry()->set('Request', 'template_view_catalog_id', 0);
		Service::Registry()->set('Request', 'template_view_path', '');
		Service::Registry()->set('Request', 'template_view_path_url', '');

		/** wrap */
		Service::Registry()->set('Request', 'wrap_view_id', 0);
		Service::Registry()->set('Request', 'wrap_view_name', '');
		Service::Registry()->set('Request', 'wrap_view_css_id', '');
		Service::Registry()->set('Request', 'wrap_view_css_class', '');
		Service::Registry()->set('Request', 'wrap_view_catalog_type_id',
			CATALOG_TYPE_EXTENSION_WRAP_VIEW);
		Service::Registry()->set('Request', 'wrap_view_catalog_id', 0);
		Service::Registry()->set('Request', 'wrap_view_path', '');
		Service::Registry()->set('Request', 'wrap_view_path_url', '');

		/** mvc parameters */
		Service::Registry()->set('Request', 'mvc_controller', '');
		Service::Registry()->set('Request', 'mvc_option', '');
		Service::Registry()->set('Request', 'mvc_task', '');
		Service::Registry()->set('Request', 'mvc_model', '');
		Service::Registry()->set('Request', 'mvc_id', 0);
		Service::Registry()->set('Request', 'mvc_category_id', 0);
		Service::Registry()->set('Request', 'mvc_url_parameters', array());
		Service::Registry()->set('Request', 'mvc_suppress_no_results', false);

		/** results */
		Service::Registry()->set('Request', 'error_status', false);
		Service::Registry()->set('Request', 'status_authorised', false);
		Service::Registry()->set('Request', 'status_found', false);

		/**
		 *  Display Controller saves the query results for the primary request
		 *      extension for possible reuse by other extensions. MolajoRequestModel
		 *      can be used to retrieve the data.
		 */
		Service::Registry()->set('Request', 'query_resultset', array());
		Service::Registry()->set('Request', 'query_pagination', array());
		Service::Registry()->set('Request', 'query_state', array());
    }
}
