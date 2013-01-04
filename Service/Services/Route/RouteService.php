<?php
/**
 * Route Service
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Services\Route;

use Molajo\Frontcontroller;
use Molajo\Service\Services;

defined('NIAMBIE') or die;

/**
 * The Route Service determines application actions to be taken as a result of the Request.
 * First, it determines what action is requested: Create, Read, Update, Delete, or Login.
 *
 * For read requests, filter values are removed from the URL. These values are defined in the
 * Application Filters file and can be changed, as needed.
 *
 * For non-read requests, the task and related values, are removed from the URL. These values
 * are defined in the Application Actions file.
 *
 * For the remaining portion of the Request URL, a query against the Catalog Table is made
 * to retrieve Route information to determine if an item, list, or menuitem was requested and
 * what the basic parameters were for that request.
 *
 * @author       Amy Stephen
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 * @since        1.0
 */
Class RouteService
{
    /**
     * Stores an array of key/value Parameters settings
     *
     * @var    array
     * @since  1.0
     */
    protected $parameters = array();

    /**
     * List of Known, Valid Parameter Properties
     *
     * @var    object
     * @since  1.0
     */
    protected $property_array = array();

    /**
     * Get the current value (or default) of the specified key
     *
     * @param   string  $key
     * @param   mixed   $default
     *
     * @return  mixed
     * @since   1.0
     */
    public function get($key = null, $default = null)
    {
        $key = strtolower($key);

        if (in_array($key, $this->property_array)) {
        } else {
            throw new \OutOfRangeException('Route: is attempting to get value for unknown key: ' . $key);
        }

        if (isset($this->parameters[$key])) {
            return $this->parameters[$key];
        }

        $this->parameters[$key] = $default;

        return $this->parameters[$key];
    }

    /**
     * Set the value of a specified key
     *
     * @param   string  $key
     * @param   mixed   $value
     *
     * @return  mixed
     * @since   1.0
     */
    public function set($key, $value = null)
    {
        $key = strtolower($key);

        if (in_array($key, $this->property_array)) {
        } else {
            throw new \OutOfRangeException('Route: is attempting to set value for unknown key: ' . $key);
        }

        $this->parameters[$key] = $value;

        return $this->parameters[$key];
    }

    /**
     * Retrieve catalog entry and values needed to route the request
     *
     * @param   string  $property_array     Valid parameter keys
     * @param   string  $requested_resource_for_route   Routable portion of Request (ex. articles/article-1)
     * @param   string  $base_url_path_for_application  Base for URL (ex. http://example.com/administrator)
     * @param   string  $override_catalog_id            Use instead of $requested_resource_for_route
     *
     * @return  array   return array($this->parameters, $this->property_array);
     * @since   1.0
     */
    public function process(
        $parameters,
        $property_array,
        $requested_resource_for_route,
        $base_url_path_for_application,
        $override_catalog_id = null
    ) {
        $this->parameters                 = $parameters;
        $this->property_array = $property_array;

        $this->set('request_catalog_id', 0);
        $this->set('status_found', 0);
        $this->set('status_authorised', '');
        $this->set('redirect_to_id', 0);
        $this->set('error_code', 0);

        if (substr($requested_resource_for_route, 0, 1) == '/') {
            $requested_resource_for_route = substr($requested_resource_for_route, 1);
        }

        $this->set('request_url', $requested_resource_for_route);
        $this->set('request_base_url_path', $base_url_path_for_application);
        $this->set('request_catalog_id', 0);

        if ((int)$override_catalog_id > 0) {
            $this->set('request_catalog_id', (int)$override_catalog_id);
        }

        /** Check if home, redirect for duplicate content home */
        $this->checkHome();

        if ($this->get('redirect_to_id') === 0) {
        } else {
            return array($this->parameters, $this->property_array);
        }

        /** Offline Check */
        if ($this->get('configuration_offline_switch') == 1) {
            if ($this->get('user_authorised_for_offline_access') == 1) {
            } else {
                $this->set('error_code', 503);

                return array($this->parameters, $this->property_array);
            }
        }

        /** Extract Action, Task, Tags, Post Array from Request */
        $this->getRequest();

        /**  Get Route Information: Catalog  */
        $this->getRouteCatalog();

        /** 404 */
        if ($this->get('status_found') === 0) {
            $this->set('error_code', 404);

            return array($this->parameters, $this->property_array);
        }

        /** URL Change Redirect from Catalog */
        if ((int)$this->get('redirect_to_id') === 0) {
        } else {
            return array($this->parameters, $this->property_array);
        }

        /** Redirect to login */
        if ($this->get('configuration_application_login_requirement', 0) > 0
            && $this->get('user_guest') == 1
            && $this->get('request_catalog_id') <> $this->get('configuration_application_login_requirement', 0)
        ) {

            $this->set('redirect_id', 'configuration_application_login_requirement');
            $this->set('error_code', 303);

            return array($this->parameters, $this->property_array);
        }

        $sort = $this->parameters;
        ksort($sort);

        return array($sort, $this->property_array);
    }

    /**
     * Determine if URL is duplicate content for home (and issue redirect, if necessary)
     *
     * @return  void
     * @since   1.0
     */
    protected function checkHome()
    {
        $path = $this->get('request_url');
        $this->set('catalog_home', 0);

        if (strlen($path) == 0 || trim($path) == '') {
            $this->set('request_url', '');
            $this->set(
                'request_catalog_id',
                $this->get('configuration_application_home_catalog_id', 0)
            );
            $this->set('catalog_home', 1);

            return;
        }

        $this->set('request_url', $path);

        if ($this->get('request_url', '') == 'index.php'
            || $this->get('request_url', '') == 'index.php/'
            || $this->get('request_url', '') == 'index.php?'
            || $this->get('request_url', '') == '/index.php/'
        ) {
            $this->set('error_code', 301);
            $this->set('redirect_to_id', $this->get('configuration_application_home_catalog_id'));
        }

        if ($this->get('request_url', '') == ''
            && (int)$this->get('request_catalog_id', 0) == 0
        ) {
            $this->set(
                'request_catalog_id',
                $this->get('configuration_application_home_catalog_id', 0)
            );
            $this->set('catalog_home', 1);
        }

        return;
    }

    /**
     * rest/urls
     *
     * http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html
     *
     * http://microformats.org/wiki/rest/urls
     *
     * POST - create a resource within a given collection
     * GET - retrieve
     * PUT - update
     * DELETE - Delete
     *
     * Most browsers do not support PUT and DELETE.
     * However, adding method="PUT" or method="DELETE" within the form works
     *
     * Routing - operate on the collection
     *
     * GET /resource/1
     *
     * GET /resource - list
     * GET /resource/new - form for new resource
     * GET /resource/1/edit - form for edit resource 1
     *
     * POST /resource - create new resource
     * PUT /resource/1,3,4 - update resource 1,3,4
     * PUT /resource/1 - update resource record 1
     * DELETE /resource/1 - delete the record with 1 for a primary key
     *
     * To compensate for browser limitations
     *
     * POST /resource/1?_method=DELETE
     * POST /resource/1?_method=PUT
     *
     * Follow a relationship:
     * GET /resource/1/comments
     * GET /resource/1/comments/new
     * POST /resource/1/comments (new comment save)
     *
     * Response http://en.wikipedia.org/wiki/List_of_HTTP_status_codes#2xx_Success
     *
     * @return  void
     * @since   1.0
     */
    protected function getRequest()
    {
        $this->set('request_non_route_parameters', '');

        $method = $this->get('request_method');
        $method = strtolower($method);

        if ($method == 'post') {
            $action = 'create';

        } elseif ($method == 'put') {
            $action = 'update';

        } elseif ($method == 'delete') {
            $action = 'delete';

        } else {
            $action = 'read';
        }
        $this->set('request_action', $action);

        if ($action == 'read') {
            $post_variables = array();
            $this->getReadVariables();

        } else {
            $post_variables = $this->get('request_post_variables');
            $this->getTaskVariables();
        }
        $this->set('request_post_variables', $post_variables);

        if ($this->get('request_catalog_id') > 0) {
        } else {
            $value = (int)$this->get('request_id');
            if ($value == 0) {
            } else {
                $this->set('request_catalog_id', $value);
            }
        }

        /**
        @todo test with non-sef URLs
        $sef = $this->get('configuration_sef_url', 1);
        if ($sef == 1) {
        $this->getResourceSEF();
        } else {
        $this->getResourceExtensionParameters();
        }
         */

        return;
    }

    /**
     * Read Action: Retrieve non-route values from parameter URL
     *
     * @return  void
     * @since   1.0
     */
    protected function getResourceExtensionParameters()
    {
        return;
    }

    /**
     * Retrieve non-route values for SEF URLs:
     *
     * @return  void
     * @since   1.0
     */
    protected function getReadVariables()
    {
        $path = $this->get('request_url');

        $urlParts = explode('/', $path);
        if (count($urlParts) == 0) {
            return;
        }

        $filters = $this->get('permission_filters');

        $path        = '';
        $filterArray = '';
        $filter      = '';
        $i           = 0;

        foreach ($urlParts as $slug) {

            if ($filter == '') {
                if (in_array($slug, $filters)) {
                    $filter = $slug;
                } else {
                    if (trim($path) == '') {
                    } else {
                        $path .= '/';
                    }
                    $path .= $slug;
                }
            } else {
                if ($filterArray == '') {
                } else {
                    $filterArray .= ';';
                }
                $filterArray .= $filter . ':' . $slug;
                $filter = '';
            }
        }

        $this->set('request_filters', $filterArray);

        /** Map Action Verb (Tag, Favorite, etc.) to Permission Action (Update, Delete, etc.) */
        $this->set('request_task', 'read');
        $this->set('request_task_values', '');

        $authorisationArray = $this->get('permission_action_to_authorisation');
        $authorisation      = $authorisationArray['read'];
        $this->set('request_task_permission', $authorisation);

        $controllerArray = $this->get('permission_action_to_controller');
        $x               = $controllerArray['read'];
        $this->set('request_task_controller', $x);

        if ($path == $this->get('request_url')) {
        } else {
            $this->set('request_url', $path);
        }

        return;
    }

    /**
     * For non-read actions, retrieve task and values
     *
     * @return  void
     * @since   1.0
     */
    protected function getTaskVariables()
    {
        $path = $this->get('request_url');

        $urlParts = explode('/', $path);
        if (count($urlParts) == 0) {
            return;
        }

        $tasks = $this->get('permission_tasks');

        $path          = '';
        $task          = '';
        $action_target = '';

        foreach ($urlParts as $slug) {
            if ($task == '') {
                if (in_array($slug, $tasks)) {
                    $task = $slug;
                } else {
                    if (trim($path) == '') {
                    } else {
                        $path .= '/';
                    }
                    $path .= $slug;
                }
            } else {
                $action_target = $slug;
                break;
            }
        }

        /** Map Action Verb (Tag, Favorite, etc.) to Permission Action (Update, Delete, etc.) */
        $this->set('request_task', $task);
        $this->set('request_task_values', $action_target);

        $authorisationArray = $this->get('permission_action_to_authorisation');
        $authorisation      = $authorisationArray[$task];
        $this->set('request_task_permission', $authorisation);

        $controllerArray = $this->get('permission_action_to_controller');
        $x               = $controllerArray[$task];
        $this->set('request_task_controller', $x);

        if ($path == $this->get('request_url')) {
        } else {
            $this->set('request_url', $path);
        }

        return;
    }

    /**
     * For Rout, retrieve Catalog Row for SEF URL or Catalog ID
     *
     * @return  void
     * @since   1.0
     */
    public function getRouteCatalog()
    {
        /* test 1: Application 2, Site 1 - Retrieve Catalog ID: 831 using Source ID: 1 and Catalog Type ID: 1000
                     $catalog_id = 0;
                     $url_sef_request = '';
                     $source_id = 1;
                     $catalog_type_id = 1000;
             */

        /* test 2: Application 2, Site 1- Retrieve Catalog ID: 1075 using $url_sef_request = 'articles'
                $catalog_id = 0;
                $url_sef_request = 'articles';
                $source_id = 0;
                $catalog_type_id = 0;
        */

        /* test 3: Application 2, Site 1- Retrieve Item: for Catalog ID 1075
                $catalog_id = 1075;
                $url_sef_request = '';
                $source_id = 0;
                $catalog_type_id = 0;
         */

        $catalog_id      = $this->get('request_catalog_id');
        $url_sef_request = $this->get('request_url');
        $catalog_type_id = 0;
        $source_id       = 0;

        $controllerClass = CONTROLLER_CLASS_NAMESPACE;
        $controller      = new $controllerClass();
        $controller->getModelRegistry('datasource', 'Catalog', 1);

        $controller->set('use_special_joins', 1, 'model_registry');
        $controller->set('process_plugins', 0, 'model_registry');

        $prefix = $controller->get('primary_prefix', 'a', 'model_registry');
        $key    = $controller->get('primary_key', 'id', 'model_registry');

        if ((int)$catalog_id > 0) {
            $controller->model->query->where(
                $controller->model->db->qn($prefix)
                    . '.'
                    . $controller->model->db->qn($key)
                    . ' = '
                    . (int)$catalog_id
            );

        } elseif ((int)$source_id > 0 && (int)$catalog_type_id > 0) {
            $controller->model->query->where(
                $controller->model->db->qn($prefix)
                    . '.'
                    . $controller->model->db->qn('catalog_type_id')
                    . ' = '
                    . (int)$catalog_type_id
            );

            $controller->model->query->where(
                $controller->model->db->qn($prefix)
                    . '.'
                    . $controller->model->db->qn('source_id')
                    . ' = '
                    . (int)$source_id
            );

        } else {
            $controller->model->query->where(
                $controller->model->db->qn($prefix)
                    . '.'
                    . $controller->model->db->qn('sef_request')
                    . ' = '
                    . $controller->model->db->q($url_sef_request)
            );
        }

        $controller->model->query->where(
            $controller->model->db->qn($prefix)
                . '.'
                . $controller->model->db->qn('page_type')
                . ' <> '
                . $controller->model->db->q(PAGE_TYPE_LINK)
        );

        $item = $controller->getData(QUERY_OBJECT_ITEM);

        if (count($item) == 0 || $item === false) {
            $this->set('status_found', 0);

            return;
        }

        $item->catalog_url_request = 'index.php?id=' . (int)$item->id;

        if ($catalog_id == $this->get('configuration_application_home_catalog_id', 0)) {
            $item->sef_request = '';
        }

        if (count($item) == 0 || (int)$item->id == 0 || (int)$item->enabled == 0) {
            $this->set('status_found', 0);

            return;
        }

        $this->set('status_found', 1);

        if ((int)$item->redirect_to_id == 0) {
        } else {
            $this->set('redirect_to_id', (int)$item->redirect_to_id);

            return;
        }

        $this->set('catalog_id', (int)$item->id);
        $this->set('catalog_type_id', (int)$item->catalog_type_id);
        $this->set('catalog_type', $item->b_title);
        $this->set('catalog_url_sef_request', $item->sef_request);
        $this->set('catalog_url_request', $item->catalog_url_request);
        $this->set('catalog_page_type', $item->page_type);
        $this->set('catalog_view_group_id', (int)$item->view_group_id);
        $this->set('catalog_category_id', (int)$item->primary_category_id);
        $this->set('catalog_extension_instance_id', $item->extension_instance_id);
        $this->set('catalog_model_type', $item->b_model_type);
        $this->set('catalog_model_name', $item->b_model_name);
        $this->set(
            'catalog_model_registry_name',
            ucfirst(strtolower($item->b_model_name)) . ucfirst(strtolower($item->b_model_type))
        );
        $this->set('catalog_alias', $item->b_alias);
        $this->set('catalog_source_id', (int)$item->source_id);

        if ((int)$this->get('catalog_id') == (int)$this->get('configuration_application_home_catalog_id')
        ) {
            $this->set('catalog_home', 1);
        } else {
            $this->set('catalog_home', 0);
        }

        return;
    }
}
