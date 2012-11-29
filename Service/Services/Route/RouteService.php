<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Service\Services\Route;

use Molajo\Application;
use Molajo\Service\Services;
use Molajo\Helpers;

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
        if ((int) Services::Registry()->get(OVERRIDE_LITERAL, 'catalog_id') > 0) {
            Services::Registry()->set(PARAMETERS_LITERAL, 'request_catalog_id',
                (int) Services::Registry()->get(OVERRIDE_LITERAL, 'catalog_id'));
        }

        if (Services::Registry()->get(OVERRIDE_LITERAL, 'url_request', '') == '') {
        } else {
            Services::Registry()->set(PARAMETERS_LITERAL, 'request_url',
                Services::Registry()->get(OVERRIDE_LITERAL, 'url_request'));
        }

        $continue = $this->checkHome();
        if ($continue === false) {
            Services::Profiler()->set('Route checkHome() Redirect to Real Home', 'Route');
            return false;
        }
//todo: define groups who can logon in offline mode
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
        $continue = Helpers::Catalog()->getRouteCatalog();

        /** 404 */
        if (Services::Registry()->get(PARAMETERS_LITERAL, 'status_found') === false) {
            Services::Error()->set(404);
            Services::Profiler()->set('Application::Route() 404', 'Route');
            return false;
        }

        /** URL Change Redirect from Catalog */
        if ((int) Services::Registry()->get(PARAMETERS_LITERAL, 'redirect_to_id', 0) == 0) {
        } else {

            Services::Response()->redirect(
                Helpers::Catalog()->getURL(
                    Services::Registry()->get(PARAMETERS_LITERAL, 'redirect_to_id', 0)
                ), 301
            );

            Services::Profiler()->set('Application::Route() Redirect', 'Route');

            return false;
        }

        /** Redirect to Logon */
        if (Services::Registry()->get(CONFIGURATION_LITERAL, 'application_logon_requirement', 0) > 0
            && Services::Registry()->get(USER_LITERAL, 'guest', true) === true
            && Services::Registry()->get(PARAMETERS_LITERAL, 'request_catalog_id')
                <> Services::Registry()->get(CONFIGURATION_LITERAL, 'application_logon_requirement', 0)
        ) {
            Services::Response()->redirect(
                Services::Registry()->get(CONFIGURATION_LITERAL, 'application_logon_requirement', 0)
                , 303
            );
            Services::Profiler()->set('Application::Route() Redirect to Logon', 'Route');
            return false;
        }

        return $this->getRouteParameters();
    }

    /**
     * Determine if URL is duplicate content for home (and issue redirect, if necessary)
     *
     * @param   string  $path Stripped of Host, Folder, and Application
     *                         ex. index.php?option=login or access/groups
     *
     * @return  boolean
     * @since   1.0
     */
    protected function checkHome()
    {
        $path = Services::Registry()->get(PARAMETERS_LITERAL, 'request_url');
        Services::Registry()->set(PARAMETERS_LITERAL, 'home', 0);

        if (strlen($path) == 0 || trim($path) == '') {

            Services::Registry()->set(PARAMETERS_LITERAL, 'request_url', '');
            Services::Registry()->set(PARAMETERS_LITERAL, 'request_catalog_id',
                Services::Registry()->get(CONFIGURATION_LITERAL, 'application_home_catalog_id', 0));
            Services::Registry()->set(PARAMETERS_LITERAL, 'catalog_home', true);
            Services::Registry()->set(PARAMETERS_LITERAL, 'home', 1);

            return true;

        } else {

            if ((int) Services::Registry()->get(CONFIGURATION_LITERAL, 'url_sef_suffix', 1) == 1
                && substr($path, -11) == '/index.html'
            ) {
                $path = substr($path, 0, (strlen($path) - 11));
            }

            if ((int) Services::Registry()->get(CONFIGURATION_LITERAL, 'url_sef_suffix', 1) == 1
                && substr($path, -5) == '.html'
            ) {
                $path = substr($path, 0, (strlen($path) - 5));
            }
        }

        Services::Registry()->set(PARAMETERS_LITERAL, 'request_url', $path);

        if (Services::Registry()->get(PARAMETERS_LITERAL, 'request_url', '') == 'index.php'
            || Services::Registry()->get(PARAMETERS_LITERAL, 'request_url', '') == 'index.php/'
            || Services::Registry()->get(PARAMETERS_LITERAL, 'request_url', '') == 'index.php?'
            || Services::Registry()->get(PARAMETERS_LITERAL, 'request_url', '') == '/index.php/'
        ) {
            Services::Redirect()->set('', 301);

            return false;
        }

        if (Services::Registry()->get(PARAMETERS_LITERAL, 'request_url', '') == ''
            && (int) Services::Registry()->get(PARAMETERS_LITERAL, 'request_catalog_id', 0) == 0
        ) {

            Services::Registry()->set(PARAMETERS_LITERAL, 'request_catalog_id',
                Services::Registry()->get(CONFIGURATION_LITERAL, 'application_home_catalog_id', 0));
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
                || $post_variables === false) {
            } else {
                $i = 0;
                foreach ($post_variables as $key=>$value) {
                    echo $key. ' ' . $value . '<br />';
                    //Services::Request()->set($key, $value);
                }
            }
        }

        Services::Registry()->set(PARAMETERS_LITERAL, 'request_action', $action);
        Services::Registry()->set(PARAMETERS_LITERAL, 'request_action_authorisation', $controller); //for now
        Services::Registry()->set(PARAMETERS_LITERAL, 'request_controller', $controller);

        /** Retrieve ID, unless already set for Home or Override  */
        if (Services::Registry()->get(PARAMETERS_LITERAL, 'request_catalog_id') > 0) {
        } else {
            $value = (int) Services::Request()->get('id');
            if ($value == 0) {
            } else {
                Services::Registry()->set(PARAMETERS_LITERAL, 'request_catalog_id', $value);
            }
        }

        /** URL Type */
        $sef = Services::Registry()->get(PARAMETERS_LITERAL, 'sef_url', 1);
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
        $path = Services::Registry()->get(PARAMETERS_LITERAL, 'request_url');

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
		Services::Registry()->set(PARAMETERS_LITERAL, 'request_task',
            $task);

        Services::Registry()->set(PARAMETERS_LITERAL, 'request_action_target',
            $action_target);

        Services::Registry()->set(PARAMETERS_LITERAL, 'request_controller',
            Services::Permissions()->getTaskController($task));

        Services::Registry()->set(PARAMETERS_LITERAL, 'request_action',
            Services::Permissions()->getTaskAction($task));

		if ($path == Services::Registry()->get(PARAMETERS_LITERAL, 'request_url')) {
		} else {
			Services::Registry()->set(PARAMETERS_LITERAL, 'request_url', $path);
			return true;
		}

	    /** Extract Non-routing Parameters from Route-able Request */
		$urlParts = explode('/', $path);
		if (count($urlParts) == 0) {
			return true;
		}

		$filters = array('page','category','author', 'tag');

		$path = '';
		$filterArray =  '';
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

		if ($path == Services::Registry()->get(PARAMETERS_LITERAL, 'request_url')) {
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
     * For Item, List, or Menu Item, retrieve Parameter data needed to generate page
     *
     * @return  boolean
     * @since   1.0
     * @throws  /Exception
     */
    protected function getRouteParameters()
    {
        if (defined('ROUTE')) {
        } else {
            define('ROUTE', true);
        }

        $catalog_type_id = Services::Registry()->get(PARAMETERS_LITERAL, 'catalog_type_id');
        $id = Services::Registry()->get(PARAMETERS_LITERAL, 'catalog_source_id');
        $catalog_extension_instance_id = Services::Registry()->get(PARAMETERS_LITERAL, 'catalog_extension_instance_id');
        $catalog_page_type = Services::Registry()->get(PARAMETERS_LITERAL, 'catalog_page_type');
        $model_type = ucfirst(strtolower(Services::Registry()->get(PARAMETERS_LITERAL, 'catalog_model_type')));
        $model_name = ucfirst(strtolower(Services::Registry()->get(PARAMETERS_LITERAL, 'catalog_model_name')));

        if (strtolower(trim($catalog_page_type)) == QUERY_OBJECT_LIST
        ) {
            $response = Helpers::Content()->getRouteList($id, $model_type, $model_name);
            if ($response === false) {
                return false;
            }

        } elseif (strtolower(trim($catalog_page_type)) == QUERY_OBJECT_ITEM) {
            $response = Helpers::Content()->getRouteItem($id, $model_type, $model_name);
            if ($response === false) {
                return false;
            }

        } else {
            $response = Helpers::Content()->getRouteMenuitem();
            if ($response === false) {
                return false;
            }
        }

        return true;
    }
}
