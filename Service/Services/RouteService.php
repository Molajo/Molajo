<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Service\Services;

use Molajo\Application;

use Molajo\Service\Services;

use Molajo\Extension\Helpers;

defined('MOLAJO') or die;

/**
 * Route
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
	 * @return  Object
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
	 * Using the PAGE_REQUEST constant:
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
		/** Overrides */
		if ((int)Services::Registry()->get('Override', 'catalog_id', 0) == 0) {
			Services::Registry()->set('Route', 'id', 0);

		} else {
			Services::Registry()->set('Route', 'id',
				(int)Services::Registry()->get('Override', 'catalog_id', 0));
		}

		if (Services::Registry()->get('Override', 'request_url', '') == '') {
			$path = PAGE_REQUEST;

		} else {
			$path = Services::Registry()->get('Override', 'request_url', '');
		}

		/** @var $continue Check for duplicate content URL for Home (and redirect, if found) */
		$continue = $this->checkHome($path);

		if ($continue == false) {
			Services::Debug()->set('Application::Route()->checkHome() Redirect to Real Home');
			return false;

		} else {
			Services::Debug()->set('Application::Route()->checkHome() No Redirect needed');
		}

		/** See if Application is in Offline Mode */
		if (Services::Registry()->get('Configuration', 'offline', 0) == 1) {
			Services::Error()->set(503);
			Services::Debug()->set('Application::Route() Direct to Offline Mode');
			return true;

		} else {
			Services::Debug()->set('Application::Route() Not in Offline Mode');
		}

		/** Remove Parameters from path and save for later use */
		$continue = $this->getNonRoutableParameters();

		if ($continue == false) {
			Services::Debug()->set('Application::Route()->getNonRoutableParameters() Failed');
			return false;

		} else {
			Services::Debug()->set('Application::Route()->getNonRoutableParameters() Successful');
		}

		/**  Get Route Information: Catalog  */
		$continue = Helpers::Catalog()->getRoute();

		/** 404 */
		if (Services::Registry()->get('Route', 'status_found') === false) {
			Services::Error()->set(404);
			Services::Debug()->set('Application::Route() 404');
			return false;

		} else {
			Services::Registry()->set('Route', 'status_found', true);
			Services::Debug()->set('Application::Route()->getCatalog() Successful');
		}

		/** URL Change Redirect from Catalog */
		if ((int)Services::Registry()->get('Route', 'redirect_to_id', 0) == 0) {
		} else {
			Services::Response()->redirect(
				Helper::Catalog()->getURL(
					Services::Registry()->get('Route', 'redirect_to_id', 0)
				), 301
			);
			Services::Debug()->set('Application::Route() Redirect');
			return false;
		}

		/** Redirect to Logon */
		if (Services::Registry()->get('Configuration', 'logon_requirement', 0) > 0
			&& Services::Registry()->get('User', 'guest', true) === true
			&& Services::Registry()->get('Route', 'id')
				<> Services::Registry()->get('Configuration', 'logon_requirement', 0)
		) {
			Services::Response()->redirect(
				Services::Registry()->get('Configuration', 'logon_requirement', 0)
				, 303
			);
			Services::Debug()->set('Application::Route() Redirect to Logon');
			return false;
		}

		$this->getRouteParameters();

		/**   Return to Application Object */
		return $this;
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
	protected function checkHome($path = '')
	{
		if (strlen($path) == 0) {
			return true;

		} else {

			/** duplicate content: URLs without the .html */
			if ((int)Services::Registry()->get('Configuration', 'sef_suffix', 1) == 1
				&& substr($path, -11) == '/index.html'
			) {
				$path = substr($path, 0, (strlen($path) - 11));
			}

			if ((int)Services::Registry()->get('Configuration', 'sef_suffix', 1) == 1
				&& substr($path, -5) == '.html'
			) {
				$path = substr($path, 0, (strlen($path) - 5));
			}
		}

		/** populate value used in query  */
		Services::Registry()->set('Route', 'request_url_query', $path);

		/** home: duplicate content - redirect */
		if (Services::Registry()->get('Route', 'request_url_query', '') == 'index.php'
			|| Services::Registry()->get('Route', 'request_url_query', '') == 'index.php/'
			|| Services::Registry()->get('Route', 'request_url_query', '') == 'index.php?'
			|| Services::Registry()->get('Route', 'request_url_query', '') == '/index.php/'
		) {
			Services::Redirect()->set('', 301);
			return false;
		}

		/** Home */
		if (Services::Registry()->get('Route', 'request_url_query', '') == ''
			&& (int)Services::Registry()->get('Route', 'id', 0) == 0
		) {
			Services::Registry()->set('Route', 'id',
				Services::Registry()->get('Configuration', 'home_catalog_id', 0));
			Services::Registry()->set('Route', 'home', true);
		}

		return true;
	}

	/**
	 * Retrieve non routable parameter values and remove from path
	 *
	 * Note: $path has already been stripped of Host, Folder, and Application
	 *
	 *   ex. index.php?option=article&tag=XYZ&prev=6
	 *      ex. access/groups/tag/XYZ/prev/6
	 *
	 * todo: remove tag/value if SEF URL
	 *
	 * @since 1.0
	 */
	protected function getNonRoutableParameters()
	{
		$action = 'display';

		$path = Services::Registry()->get('Route', 'request_url_query');

		if ($path == '') {
			Services::Registry()->set('Route', 'non_routable_parameters', array());
			Services::Registry()->set('Route', 'action', 'display');
			Services::Registry()->set('Route', 'id',
				Services::Registry()->get('Configuration', 'home_catalog_id', 0));
			return true;
		}

		/** Retrieve ID */
		$value = (int)Services::Request()->get('request')->get('id');
		Services::Registry()->set('Route', 'id', $value);

		/** save non-routable parameter pairs in array */
		$use = array();

		/** XML with system defined nonroutable pairs */
		$list = Services::Configuration()->loadFile('nonroutable');

		foreach ($list->parameter as $item) {

			$key = (string)$item['name'];

			$filter = (string)$item['filter'];
			if ($filter === null) {
				$filter = 'char';
			}

			$value = Services::Request()->get('request')->get($key);

			if ($value === null) {
			} else {

				/** Action */
				if ($key == 'action') {
					$action = $value;
				}

				/** remove non-routable parameter - as it is - from the routeable path */
				$remove = $key . '=' . $value;

				$path = substr($path, 0, strpos($path, $remove))
					. substr($path, strpos($path, $remove) + 1 + strlen($remove), 999);

				/** filter input */
				$value = $this->filterInput($key, $value, $filter, 1, null);

				if ($value === false) {
				} else {
					$use[$key] = $value;
				}
			}
		}

		/** Remove trailing ? or & */
		if (trim($path) == '') {
		} else {
			if (strrpos($path, '&') == (strlen($path) - 1)
				|| strrpos($path, '?') == (strlen($path) - 1)
			) {
				$path = substr($path, 0, strlen($path) - 1);
			}
		}

		/** Update Path and store Non-routable parameters for Extension Use */
		Services::Registry()->set('Route', 'request_url_query', $path);
		Services::Registry()->set('Route', 'non_routable_parameters', $use);
		Services::Registry()->set('Route', 'action', $action);

		/** add Edit and Add later

		2. add /add and /edit
		3. deal with nonroutable sef
		 *
		if (strripos($pageRequest, '/edit') == (strlen($pageRequest) - 5)) {
		} else if (strripos($pageRequest, '/add') == (strlen($pageRequest) - 4)) {
		Services::Registry()->set('Route', 'action', 'add');
		 */

		/**
		look up the URL in the catalog first to determine if it's internal
		if (trim($return) == '') {
		Services::Registry()->set('Route', 'redirect_on_success', '');

		} else if (JUri::isInternal(base64_decode($return))) {
		Services::Registry()->set('Route', 'redirect_on_success', base64_decode($return));

		} else {
		Services::Registry()->set('Route', 'redirect_on_success', '');
		}
		 */
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
			//echo $e->getMessage() . ' ' . $name;
			return false;
		}

		return $value;
	}

	/**
	 * getRouteParameters
	 *
	 * Retrieve the Menu Item, Content, Extension and Primary Category Parameters for Route
	 *
	 * Determine the Theme and Page Values
	 *
	 * @return  null
	 * @since   1.0
	 *
	 * @throws  /Exception
	 */
	protected function getRouteParameters()
	{
		echo '<br /><br /><pre>';
		echo 'Route<br />';
		var_dump(Services::Registry()->get('Route'));
		echo '</pre>';

		/**  Menu Item  */
		if (Services::Registry()->get('Route', 'catalog_type_id') == CATALOG_TYPE_MENU_ITEM_COMPONENT) {
			$response = Helpers::Menuitem()->getRoute();
			if ($response === false) {
				Services::Error()->set(500, 'Menu Item not found');
			}
			echo '<br /><br /><pre>';
			echo 'Menu Item Parameters<br />';
			var_dump(Services::Registry()->get('MenuitemParameters'));
			echo 'Metadata<br />';
			var_dump(Services::Registry()->get('MenuitemMetadata'));
			echo '</pre>';
		}

		/**  Content */
		$response = Helpers::Content()->getRoute();
		if ($response === false) {
			Services::Error()->set(500, 'Content Item not found');
		}
		echo '<br /><br /><pre>';
		echo 'Content<br />';
		echo 'table '.Services::Registry()->get('Route', 'source_table').'<br />';
		echo 'id '.Services::Registry()->get('Route', 'source_id').'<br />';
		var_dump(Services::Registry()->get('Content'));
		echo 'Content Parameters<br />';
		var_dump(Services::Registry()->get('ContentParameters'));
		echo 'Metadata<br />';
		var_dump(Services::Registry()->get('ContentMetadata'));
		echo '</pre>';

		/**  Extension */
		$response = Helpers::Extension()->getRoute(
			Services::Registry()->get('Content', 'extension_instance_id')
		);
		if ($response === false) {
			Services::Error()->set(500, 'Extension not found');
		}
		echo '<br /><br /><pre>';
		echo 'Extension Parameters<br />';
		echo 'id '.Services::Registry()->get('Route', 'extension_instances_id').'<br />';
		var_dump(Services::Registry()->get('ExtensionParameters'));
		echo 'Metadata<br />';
		var_dump(Services::Registry()->get('ExtensionMetadata'));
		echo '</pre>';

		/**  Primary Category  */
		if ((int)Services::Registry()->get('Route', 'primary_category_id') == 0) {
		} else {
			echo 'id '.Services::Registry()->get('Route', 'primary_category_id').'<br />';
			Helpers::Content()->getRouteCategory();
			echo '<br /><br /><pre>';
			echo 'Categories Parameters<br />';
			var_dump(Services::Registry()->get('CategoryParameters'));
			echo 'Metadata<br />';
			var_dump(Services::Registry()->get('CategoryMetadata'));
			echo '</pre>';
		}

		echo '<pre>';
		var_dump(Services::Registry()->get('Route'));
		die;
	}
}
