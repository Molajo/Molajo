<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Service\Services\Route;

use Molajo\Application;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Route Service
 *
 * @package     Molajo
 * @subpackage  Route
 * @since       1.0
 */
Class RouteService
{
    /**
     * Using the Services::Request()->get('requested_resource_for_route') constant:
     *
     *  - retrieve the catalog record
     *  - set registry values needed to fulfill the page request
     *
     * @param   $requested_resource_for_route
     * @param   $base_url_path_for_application
     *
     * @return  bool
     * @since   1.0
     */
    public function process($requested_resource_for_route, $base_url_path_for_application)
    {
        Services::Registry()->createRegistry(PARAMETERS_LITERAL);
        Services::Registry()->createRegistry(METADATA_LITERAL);
        Services::Registry()->deleteRegistry(PLUGIN_LITERAL);

        Services::Registry()->set(PARAMETERS_LITERAL, 'request_catalog_id', 0);
        Services::Registry()->set(PARAMETERS_LITERAL, 'status_found', '');
        Services::Registry()->set(PARAMETERS_LITERAL, 'status_authorised', '');
        Services::Registry()->set(PARAMETERS_LITERAL, 'redirect_to_id', 0);

        $url_request = $requested_resource_for_route;
        if (substr($url_request, 0, 1) == '/') {
            $url_request = substr($url_request, 1);
        }
        Services::Registry()->set(PARAMETERS_LITERAL, 'request_url', $url_request);
        Services::Registry()->set(PARAMETERS_LITERAL, 'request_base_url_path', $base_url_path_for_application);
        Services::Registry()->set(PARAMETERS_LITERAL, 'request_catalog_id', 0);

        /** Overrides */
        if ((int)Services::Registry()->get(OVERRIDE_LITERAL, 'catalog_id') > 0) {
            Services::Registry()->set(
                PARAMETERS_LITERAL,
                'request_catalog_id',
                (int)Services::Registry()->get(OVERRIDE_LITERAL, 'catalog_id')
            );
        }

        if (Services::Registry()->get(OVERRIDE_LITERAL, 'url_request', '') == '') {
        } else {
            Services::Registry()->set(
                PARAMETERS_LITERAL,
                'request_url',
                Services::Registry()->get(OVERRIDE_LITERAL, 'url_request')
            );
        }

        $continue = $this->checkHome();
        if ($continue === false) {
            Services::Profiler()->set('Route checkHome() Redirect to Real Home', 'Route');
            return false;
        }
//todo: define groups who can signin in offline mode
        if (Services::Registry()->get(CONFIGURATION_LITERAL, 'offline_switch', 0) == 1) {
            Services::Error()->set(503);
            Services::Profiler()->set('Application::Route() Direct to Offline Mode', 'Route');
            return false;
        }

        $continue = $this->getResource();

        if ($continue === false) {
            Services::Profiler()->set('Route getResource() Failed', 'Route');
            return false;
        }

        /**  Get Route Information: Catalog  */
        $continue = $this->getRouteCatalog();

        /** 404 */
        if (Services::Registry()->get('parameters', 'status_found') === false) {
            Services::Error()->set(404);
            Services::Profiler()->set('Application::Route() 404', 'Route');
            return false;
        }

        /** URL Change Redirect from Catalog */
        if ((int)Services::Registry()->get('parameters', 'redirect_to_id', 0) == 0) {
        } else {

            Services::Response()->redirect(
                Services::Url()->get(
                    0,
                    0,
                    Services::Registry()->get('parameters', 'redirect_to_id', 0)
                ),
                301
            );

            Services::Profiler()->set('Application::Route() Redirect', 'Route');

            return false;
        }

        /** Redirect to signin */
        if (Services::Registry()->get(CONFIGURATION_LITERAL, 'application_signin_requirement', 0) > 0
            && Services::Registry()->get(USER_LITERAL, 'guest', true) === true
            && Services::Registry()->get('parameters', 'request_catalog_id')
                <> Services::Registry()->get(CONFIGURATION_LITERAL, 'application_signin_requirement', 0)
        ) {
            Services::Response()->redirect(
                Services::Registry()->get(CONFIGURATION_LITERAL, 'application_signin_requirement', 0)
                ,
                303
            );
            Services::Profiler()->set('Route::Redirect to signin', 'Route');
            return false;
        }

        return $this;
    }

    /**
     * Determine if URL is duplicate content for home (and issue redirect, if necessary)
     *
     * @param   string  $path Stripped of Host, Folder, and Application
     *                         ex. index.php?option=signin or access/groups
     *
     * @return  boolean
     * @since   1.0
     */
    protected function checkHome()
    {
        $path = Services::Registry()->get('parameters', 'request_url');
        Services::Registry()->set(PARAMETERS_LITERAL, 'home', 0);

        if (strlen($path) == 0 || trim($path) == '') {

            Services::Registry()->set(PARAMETERS_LITERAL, 'request_url', '');
            Services::Registry()->set(
                PARAMETERS_LITERAL,
                'request_catalog_id',
                Services::Registry()->get(CONFIGURATION_LITERAL, 'application_home_catalog_id', 0)
            );
            Services::Registry()->set(PARAMETERS_LITERAL, 'catalog_home', true);
            Services::Registry()->set(PARAMETERS_LITERAL, 'home', 1);

            return true;

        } else {

            if ((int)Services::Registry()->get(CONFIGURATION_LITERAL, 'url_sef_suffix', 1) == 1
                && substr($path, -11) == '/index.html'
            ) {
                $path = substr($path, 0, (strlen($path) - 11));
            }

            if ((int)Services::Registry()->get(CONFIGURATION_LITERAL, 'url_sef_suffix', 1) == 1
                && substr($path, -5) == '.html'
            ) {
                $path = substr($path, 0, (strlen($path) - 5));
            }
        }

        Services::Registry()->set(PARAMETERS_LITERAL, 'request_url', $path);

        if (Services::Registry()->get('parameters', 'request_url', '') == 'index.php'
            || Services::Registry()->get('parameters', 'request_url', '') == 'index.php/'
            || Services::Registry()->get('parameters', 'request_url', '') == 'index.php?'
            || Services::Registry()->get('parameters', 'request_url', '') == '/index.php/'
        ) {
            Services::Redirect()->set('', 301);

            return false;
        }

        if (Services::Registry()->get('parameters', 'request_url', '') == ''
            && (int)Services::Registry()->get('parameters', 'request_catalog_id', 0) == 0
        ) {

            Services::Registry()->set(
                PARAMETERS_LITERAL,
                'request_catalog_id',
                Services::Registry()->get(CONFIGURATION_LITERAL, 'application_home_catalog_id', 0)
            );
            Services::Registry()->set(PARAMETERS_LITERAL, 'catalog_home', true);
        }

        return true;
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
     * GET /people/1
     *
     * GET /articles - list
     * GET /articles/new - form for new article
     * GET /articles/1/edit - form for edit article 1
     *
     * POST /articles - create new article
     * PUT /articles/1,3,4 - update articles 1,3,4
     * PUT /articles/1 - update article record 1
     * DELETE /articles/1 - delete the record with 1 for a primary key
     *
     * To compensate for browser limitations
     *
     * POST /articles/1?_method=DELETE
     * POST /articles/1?_method=PUT
     *
     * Follow a relationship:
     * GET /articles/1/comments
     * GET /articles/1/comments/new
     * POST /articles/1/comments (new comment save)
     *
     * Response http://en.wikipedia.org/wiki/List_of_HTTP_status_codes#2xx_Success
     *
     * @return  boolean
     * @since   1.0
     */
    protected function getResource()
    {
        Services::Registry()->set(PARAMETERS_LITERAL, 'request_non_route_parameters', '');

        $method = Services::Request()->get('method');

        if ($method == 'POST') {
            $action = 'create';
            $controller = 'create';

        } elseif ($method == 'PUT') {
            $action = 'update';
            $controller = 'update';

        } elseif ($method == 'DELETE') {
            $action = 'delete';
            $controller = 'delete';

        } else {
            $action = ACTION_READ;
            $controller = 'read';
        }

        if ($action == ACTION_READ) {
            $post_variables = array();

        } else {
            //todo retrieve post variables
            $post_variables = Services::Request()->get('post_variables');

            if (count($post_variables) == 0
                || $post_variables === false
            ) {
            } else {
                $i = 0;
                foreach ($post_variables as $key => $value) {
                    echo $key . ' ' . $value . '<br />';
                    //Services::Request()->set($key, $value);
                }
            }
        }

        Services::Registry()->set(PARAMETERS_LITERAL, 'request_action', $action);
        Services::Registry()->set(PARAMETERS_LITERAL, 'request_action_authorisation', $controller); //for now
        Services::Registry()->set(PARAMETERS_LITERAL, 'request_controller', $controller);

        /** Retrieve ID, unless already set for Home or Override  */
        if (Services::Registry()->get('parameters', 'request_catalog_id') > 0) {
        } else {
            $value = (int)Services::Request()->get('id');
            if ($value == 0) {
            } else {
                Services::Registry()->set(PARAMETERS_LITERAL, 'request_catalog_id', $value);
            }
        }

        /** URL Type */
        $sef = Services::Registry()->get('parameters', 'sef_url', 1);
        if ($sef == 1) {
            $this->getResourceSEF();
        } else {
            $this->getResourceExtensionParameters();
        }

        return true;
    }

    /**
     * Retrieve non-route values from parameter URL
     *
     * @return  bool
     * @since   1.0
     */
    protected function getResourceExtensionParameters()
    {
        return true;
    }

    /**
     * Retrieve non-route values for SEF URLs:
     *
     *  1.  Tasks (Tag, Favoriate, Order Up) to Permission Actions (Insert, View, Delete, etc)
     *  2.  Filters (Tag, Category, Page, Author, etc.) not used for Route
     *
     * @return  boolean
     * @since   1.0
     */
    protected function getResourceSEF()
    {
        $path = Services::Registry()->get('parameters', 'request_url');

        /** Tasks (Tag, Favorite, Order Up) to Permission Actions (Insert, View, Delete, etc) */
        $urlParts = explode('/', $path);
        if (count($urlParts) == 0) {
            return true;
        }

        $urlActions = Services::Registry()->get(PERMISSIONS_LITERAL, 'urlActions');

        $path = '';
        $task = '';
        $action_target = '';

        foreach ($urlParts as $slug) {
            if ($task == '') {
                if (in_array($slug, $urlActions)) {
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

        if ($task == '') {
            $task = ACTION_READ;
        }

        /** Map Action Verb (Tag, Favorite, etc.) to Permission Action (Update, Delete, etc.) */
        Services::Registry()->set(
            PARAMETERS_LITERAL,
            'request_task',
            $task
        );

        Services::Registry()->set(
            PARAMETERS_LITERAL,
            'request_action_target',
            $action_target
        );

        Services::Registry()->set(
            PARAMETERS_LITERAL,
            'request_controller',
            Services::Permissions()->getTaskController($task)
        );

        Services::Registry()->set(
            PARAMETERS_LITERAL,
            'request_action',
            Services::Permissions()->getTaskAction($task)
        );

        if ($path == Services::Registry()->get('parameters', 'request_url')) {
        } else {
            Services::Registry()->set(PARAMETERS_LITERAL, 'request_url', $path);
            return true;
        }

        /** Extract Non-routing Parameters from Route-able Request */
        $urlParts = explode('/', $path);
        if (count($urlParts) == 0) {
            return true;
        }

        $filters = array('page', 'category', 'author', 'tag');

        $path = '';
        $filterArray = '';
        $filter = '';
        $i = 0;

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
                $filterArray .= $filter . ':' . $slug . ',';
                $filter = '';
            }
        }

        Services::Registry()->set(PARAMETERS_LITERAL, 'request_filters', $filterArray);

        if ($path == Services::Registry()->get('parameters', 'request_url')) {
        } else {
            Services::Registry()->set(PARAMETERS_LITERAL, 'request_url', $path);
        }

        Services::Registry()->sort(PARAMETERS_LITERAL);

        return true;
    }

    /**
     * filterInput
     *
     * @param   string  $name         Name of input field
     * @param   string  $field_value  Value of input field
     * @param   string  $dataType     Datatype of input field
     * @param   int     $null         0 or 1 - is null allowed
     * @param   string  $default      Default value, optional
     *
     * @return  mixed
     * @since   1.0
     *
     * @throws  /Exception
     */
    protected function filterInput($name, $value, $dataType, $null, $default)
    {
        try {
            $value = Services::Filter()->filter($value, $dataType, $null, $default);

        } catch (\Exception $e) {
            throw new \Exception('Route: Error in Filtering of Input Field: ' . $name . ' ' . $e->getMessage());
        }

        return $value;
    }

    /**
     * getRouteCatalog
     *
     * @return  array|bool
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

        $catalog_id = Services::Registry()->get('parameters', 'request_catalog_id');
        $url_sef_request = Services::Registry()->get('parameters', 'request_url');
        $catalog_type_id = 0;
        $source_id = 0;

        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();
        $controller->getModelRegistry(DATA_SOURCE_LITERAL, 'Catalog', 1);

        $controller->set('use_special_joins', 1, 'model_registry');
        $controller->set('process_plugins', 0, 'model_registry');

        $prefix = $controller->get('primary_prefix', 'a', 'model_registry');
        $key = $controller->get('primary_key', 'id', 'model_registry');

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
            return array();
        }

        $item->catalog_url_request = 'index.php?id=' . (int)$item->id;

        if ($catalog_id == Services::Registry()->get(CONFIGURATION_LITERAL, 'application_home_catalog_id', 0)) {
            $item->sef_request = '';
        }

        if (count($item) == 0 || (int)$item->id == 0 || (int)$item->enabled == 0) {
            Services::Registry()->set(PARAMETERS_LITERAL, 'status_found', false);
            Services::Profiler()->set(
                'Route: getRouteCatalog 404 - Not Found '
                    . ' Requested Catalog ID: ' . Services::Registry()->get(
                    PARAMETERS_LITERAL,
                    'request_catalog_id'
                )
                    . ' Requested URL Query: ' . Services::Registry()->get('parameters', 'request_url'),
                PROFILER_ROUTING,
                0
            );

            return false;
        }

        if ((int)$item->redirect_to_id == 0) {
        } else {
            Services::Profiler()->set(
                'Route: getRouteCatalog Redirect to ID '
                    . (int)$item->redirect_to_id,
                PROFILER_ROUTING,
                0
            );

            Services::Registry()->set(PARAMETERS_LITERAL, 'redirect_to_id', (int)$item->redirect_to_id);

            return false;
        }

        Services::Registry()->set(PARAMETERS_LITERAL, 'catalog_id', (int)$item->id);
        Services::Registry()->set(PARAMETERS_LITERAL, 'catalog_type_id', (int)$item->catalog_type_id);
        Services::Registry()->set(PARAMETERS_LITERAL, 'catalog_type', $item->b_title);
        Services::Registry()->set(PARAMETERS_LITERAL, 'catalog_url_sef_request', $item->sef_request);
        Services::Registry()->set(PARAMETERS_LITERAL, 'catalog_url_request', $item->catalog_url_request);
        Services::Registry()->set(PARAMETERS_LITERAL, 'catalog_page_type', $item->page_type);
        Services::Registry()->set(PARAMETERS_LITERAL, 'catalog_view_group_id', (int)$item->view_group_id);
        Services::Registry()->set(PARAMETERS_LITERAL, 'catalog_category_id', (int)$item->primary_category_id);
        Services::Registry()->set(
            PARAMETERS_LITERAL,
            'catalog_extension_instance_id',
            $item->extension_instance_id
        );
        Services::Registry()->set(PARAMETERS_LITERAL, 'catalog_model_type', $item->b_model_type);
        Services::Registry()->set(PARAMETERS_LITERAL, 'catalog_model_name', $item->b_model_name);
        Services::Registry()->set(PARAMETERS_LITERAL, 'catalog_alias', $item->b_alias);
        Services::Registry()->set(PARAMETERS_LITERAL, 'catalog_source_id', (int)$item->source_id);

        if ((int)Services::Registry()->get('parameters', 'catalog_id')
            == (int)Services::Registry()->get(CONFIGURATION_LITERAL, 'application_home_catalog_id')
        ) {
            Services::Registry()->set(PARAMETERS_LITERAL, 'catalog_home', 1);
        } else {
            Services::Registry()->set(PARAMETERS_LITERAL, 'catalog_home', 0);
        }

        return true;
    }
}
