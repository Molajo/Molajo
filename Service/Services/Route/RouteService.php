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
 * @package    Molajo
 * @subpackage Route
 * @since      1.0
 */
Class RouteService
{
	/**
	 * $instance
	 *
	 * @var     object
	 * @since   1.0
	 */
	protected static $instance = null;

	/**
	 * @return Object
	 *
	 * @since   1.0
	 */
	public static function getInstance()
	{
		if (self::$instance) {
		} else {
			self::$instance = new RouteService();
		}

		return self::$instance;
	}

	/**
	 * Using the Application::Request()->get('requested_resource_for_route') constant:
	 *
	 *  - retrieve the catalog record
	 *  - set registry values needed to fulfill the page request
	 *
	 * @return mixed
	 *
	 * @since 1.0
	 */
	public function process()
	{
		/** Route Registry */
		Services::Registry()->createRegistry('Parameters');
		Services::Registry()->createRegistry('Metadata');
		Services::Registry()->deleteRegistry('Plugin');

		Services::Registry()->set('Parameters', 'request_catalog_id', 0);
		Services::Registry()->set('Parameters', 'status_found', '');
		Services::Registry()->set('Parameters', 'status_authorised', '');
		Services::Registry()->set('Parameters', 'redirect_to_id', 0);

		$url_request = Application::Request()->get('requested_resource_for_route');
		if (substr($url_request, 0, 1) == '/') {
			$url_request = substr($url_request, 1);
		}
		Services::Registry()->set('Parameters', 'request_url_request', $url_request);
		Services::Registry()->set('Parameters', 'request_catalog_id', 0);

		/** Overrides */
		if ((int) Services::Registry()->get('Override', 'catalog_id') > 0) {
			Services::Registry()->set('Parameters', 'request_catalog_id',
				(int)Services::Registry()->get('Override', 'catalog_id'));
		}

		if (Services::Registry()->get('Override', 'url_request', '') == '') {
		} else {
			Services::Registry()->set('Parameters', 'request_url_request',
				Services::Registry()->get('Override', 'url_request'));
		}

		/** Check for duplicate content URL for Home (and redirect, if found) */
		$continue = $this->checkHome();
		if ($continue === false) {
			Services::Profiler()->set('Route checkHome() Redirect to Real Home', 'Route');

			return false;
		}

		/** See if Application is in Offline Mode */
		if (Services::Registry()->get('Configuration', 'offline_switch', 0) == 1) {
			Services::Error()->set(503);
			Services::Profiler()->set('Application::Route() Direct to Offline Mode', 'Route');

			return false;
		}

		/** Identify Resource and sub-resource values */
		$continue = $this->getResource();
		if ($continue === false) {
			Services::Profiler()->set('Route getResource() Failed', 'Route');

			return false;
		}

		/**  Get Route Information: Catalog  */
		$continue = Helpers::Catalog()->getRouteCatalog();

		/** 404 */
		if (Services::Registry()->get('Parameters', 'status_found') === false) {
			echo 404;
			Services::Error()->set(404);
			Services::Profiler()->set('Application::Route() 404', 'Route');

			return false;
		}

		/** URL Change Redirect from Catalog */
		if ((int)Services::Registry()->get('Parameters', 'redirect_to_id', 0) == 0) {
		} else {

			Services::Response()->redirect(
				Helpers::Catalog()->getURL(
					Services::Registry()->get('Parameters', 'redirect_to_id', 0)
				), 301
			);

			Services::Profiler()->set('Application::Route() Redirect', 'Route');

			return false;
		}

		/** Redirect to Logon */
		if (Services::Registry()->get('Configuration', 'application_logon_requirement', 0) > 0
			&& Services::Registry()->get('User', 'guest', true) === true
			&& Services::Registry()->get('Parameters', 'request_catalog_id')
				<> Services::Registry()->get('Configuration', 'application_logon_requirement', 0)
		) {
			Services::Response()->redirect(
				Services::Registry()->get('Configuration', 'application_logon_requirement', 0)
				, 303
			);
			Services::Profiler()->set('Application::Route() Redirect to Logon', 'Route');

			return false;
		}

		$this->getRouteParameters();

		return true;
	}

	/**
	 * Determine if URL is duplicate content for home (and issue redirect, if necessary)
	 *
	 * @param string $path Stripped of Host, Folder, and Application
	 *                         ex. index.php?option=login or access/groups
	 *
	 * @return boolean
	 * @since  1.0
	 */
	protected function checkHome()
	{

		$path = Services::Registry()->get('Parameters', 'request_url_request');

		if (strlen($path) == 0 || trim($path) == '') {
			Services::Registry()->set('Parameters', 'request_url_query', '');
			Services::Registry()->set('Parameters', 'request_catalog_id',
				Services::Registry()->get('Configuration', 'application_home_catalog_id', 0));
			Services::Registry()->set('Parameters', 'catalog_home', true);
			return true;

		} else {

			/** duplicate content: URLs without the .html */
			if ((int)Services::Registry()->get('Configuration', 'url_sef_suffix', 1) == 1
				&& substr($path, -11) == '/index.html'
			) {
				$path = substr($path, 0, (strlen($path) - 11));
			}

			if ((int)Services::Registry()->get('Configuration', 'url_sef_suffix', 1) == 1
				&& substr($path, -5) == '.html'
			) {
				$path = substr($path, 0, (strlen($path) - 5));
			}
		}

		/** populate value used in query  */
		Services::Registry()->set('Parameters', 'request_url_query', $path);

		/** home: duplicate content - redirect */
		if (Services::Registry()->get('Parameters', 'request_url_query', '') == 'index.php'
			|| Services::Registry()->get('Parameters', 'request_url_query', '') == 'index.php/'
			|| Services::Registry()->get('Parameters', 'request_url_query', '') == 'index.php?'
			|| Services::Registry()->get('Parameters', 'request_url_query', '') == '/index.php/'
		) {
			Services::Redirect()->set('', 301);

			return false;
		}

		/** Home */
		if (Services::Registry()->get('Parameters', 'request_url_query', '') == ''
			&& (int)Services::Registry()->get('Parameters', 'request_catalog_id', 0) == 0
		) {

			Services::Registry()->set('Parameters', 'request_catalog_id',
				Services::Registry()->get('Configuration', 'application_home_catalog_id', 0));
			Services::Registry()->set('Parameters', 'catalog_home', true);
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
	 * @return boolean
	 * @since   1.0
	 */
	protected function getResource()
	{
		/**echo '<pre>';
		var_dump(Application::Request()->request);
		echo '</pre>'; */

		/** Defaults */
		Services::Registry()->set('Parameters', 'request_non_route_parameters', '');

		$method = Application::Request()->get('method');
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
			$action = 'display';
			$controller = 'read';
		}

		Services::Registry()->set('Parameters', 'request_action', $action);
		Services::Registry()->set('Parameters', 'request_action_authorisation', $controller); //for now
		Services::Registry()->set('Parameters', 'request_controller', $controller);

		/** Retrieve ID, unless already set for Home or Override  */
		if (Services::Registry()->get('Parameters', 'request_catalog_id') > 0) {
		} else {
			$value = (int)Application::Request()->get('id');
			if ($value == 0) {
			} else {
				Services::Registry()->set('Parameters', 'request_catalog_id', $value);
			}
		}

		/** URL Type */
		$sef = Services::Registry()->get('Parameters', 'sef_url', 1);
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
	 * @return bool
	 * @since   1.0
	 */
	protected function getResourceParameters()
	{
		return true;
	}

	/**
	 * Retrieve non-route values for SEF URLs
	 *
	 * @return boolean
	 * @since   1.0
	 */
	protected function getResourceSEF()
	{
		return true;

		//todo - extract non route  parameter and values (tag/1,2,3 and date/2012-12 and author/amystephen
		$path = Services::Registry()->get('Parameters', 'request_url_query');

		$testKey = '';
		$testPath = '';
		if (strrpos($path, '/') > 0) {
			$testKey = substr($path, strrpos($path, '/') + 1, strlen($path) - strrpos($path, '/'));
			$testPath = substr($path, 0, strrpos($path, '/'));
		} else {
			$testPath = $path;
		}

		$action = '';
		$path = '';
		$authorisation = '';
		$controller = '';

		$list = Services::Configuration()->getFile('Application', 'Actions');

		if (count($list) > 0) {
			foreach ($list->action as $item) {

				$key = (string)$item['name'];

				if ($key == $testKey) {
					$action = $key;
					$path = $testPath;
					$authorisation = (string)$item['authorisation'];
					$controller = (string)$item['controller'];
					break;
				}
			}
		} else {
			$path = $testPath . '/' . $testKey;
		}

		/** Update Path and store Non-routable parameters or defaults for Extension Use */
		Services::Registry()->set('Parameters', 'request_url_query', $path);
		Services::Registry()->set('Parameters', 'request_non_route_parameters', $action);

		Services::Registry()->set('Parameters', 'request_action', $action);
		Services::Registry()->set('Parameters', 'request_action_authorisation', $authorisation);
		Services::Registry()->set('Parameters', 'request_controller', $controller);

		return true;
	}

	/**
	 * filterInput
	 *
	 * @param string $name        Name of input field
	 * @param string $field_value Value of input field
	 * @param string $dataType    Datatype of input field
	 * @param int    $null        0 or 1 - is null allowed
	 * @param string $default     Default value, optional
	 *
	 * @return mixed
	 * @since   1.0
	 *
	 * @throws /Exception
	 */
	protected function filterInput($name, $value, $dataType, $null, $default)
	{
		try {
			$value = Services::Filter()->filter($value, $dataType, $null, $default);

		} catch (\Exception $e) {
			//echo $e->getMessage() . ' ' . $name;
			return false;
		}

		return $value;
	}

	/**
	 * Retrieve the Menu Item, Content, Extension and Primary Category Parameters for Route
	 *
	 * Determine the Theme and Page Values
	 *
	 * @return null
	 * @since   1.0
	 *
	 * @throws /Exception
	 */
	protected function getRouteParameters()
	{
		if (defined('ROUTE')) {
		} else {
			define('ROUTE', true);
		}
		Services::Registry()->get('Parameters', '*');
			$catalog_type_id = Services::Registry()->get('Parameters', 'catalog_type_id');
		$id = Services::Registry()->get('Parameters', 'catalog_source_id');
		$table = Services::Registry()->get('Parameters', 'catalog_source_table');
		$catalog_menuitem_type = Services::Registry()->get('Parameters', 'catalog_menuitem_type');
		$model_type = ucfirst(strtolower(Services::Registry()->get('Parameters', 'catalog_model_type')));
		$model_name = ucfirst(strtolower(Services::Registry()->get('Parameters', 'catalog_model_name')));
echo $id;
		die;
		/** List */
		if ((int)$id > 0
			&& (int)$catalog_type_id == CATALOG_TYPE_EXTENSION_RESOURCE
			&& strtolower(trim($catalog_menuitem_type)) == 'list'
		) {
			$response = Helpers::Content()->getRouteList($id, $model_type, $model_name);
			if ($response === false) {
				Services::Error()->set(500, 'Extension not found');
			}

		/** Item */
		} elseif ((int)$id > 0
			&& (strtolower(trim($catalog_menuitem_type)) == 'item'
				|| strtolower(trim($catalog_menuitem_type)) == 'form')
		) {

			$response = Helpers::Content()->getRouteItem($id, $model_type, $model_name);
			if ($response === false) {
				Services::Error()->set(500, 'Content not found');
				return false;
			}

		/** Template Menu item */
		} elseif ($catalog_menuitem_type > '') {
			$response = Helpers::Content()->getRouteMenuitem();
			if ($response === false) {
				Services::Error()->set(500, 'Menu Item not found');
			}

		} else {
			Services::Error()->set(500, 'Content not found');
			return false;
		}

		return true;
	}
}
