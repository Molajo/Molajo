<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Service\Services\Route;

use Molajo\Application;
use Molajo\Service\Services;
use Molajo\Extension\Helpers;

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
			echo 404;
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
		/**  Menu Item  */
		if (Services::Registry()->get('Route', 'catalog_type_id') == CATALOG_TYPE_MENU_ITEM_COMPONENT) {
			$response = Helpers::Menuitem()->getRoute();
			if ($response === false) {
				Services::Error()->set(500, 'Menu Item not found');
			}
		}

		/**  Content */
		$response = Helpers::Content()->getRoute();
		if ($response === false) {
			Services::Error()->set(500, 'Content Item not found');
		}

		/**  Extension */
		$response = Helpers::Extension()->getRoute(Services::Registry()->get('Content', 'extension_instance_id'));
		if ($response === false) {
			Services::Error()->set(500, 'Extension not found');
		}

		/**  Primary Category  */
		if ((int)Services::Registry()->get('Route', 'primary_category_id') == 0) {
		} else {
			Helpers::Content()->getRouteCategory();
		}

		/** Theme  */
		Helpers::Theme()->get();

		/** Page  */
		Helpers::PageView()->get();

		/** Metadata */
		$this->loadMetadata();

		return;


		echo '<br /><br />Route<br /><pre>';
		var_dump(Services::Registry()->get('Route'));
		echo '</pre>';

		echo '<br /><br />Menu Item<br /><pre>';
		var_dump(Services::Registry()->get('Menuitem'));
		echo '<br />Menu Item Parameters<br />';
		var_dump(Services::Registry()->get('MenuitemParameters'));
		echo '<br />Metadata<br />';
		var_dump(Services::Registry()->get('MenuitemMetadata'));
		echo '</pre>';

		echo '<br /><br />Content<br /><pre>';
		echo 'table ' . Services::Registry()->get('Route', 'source_table') . '<br />';
		echo 'id ' . Services::Registry()->get('Route', 'source_id') . '<br /><pre>';
		var_dump(Services::Registry()->get('Content'));
		echo 'Parameters<br />';
		var_dump(Services::Registry()->get('Parameters'));
		echo 'Metadata<br />';
		var_dump(Services::Registry()->get('Metadata'));
		echo '</pre>';

		echo '<br /><br />Extension<br /><pre>';
		echo 'id ' . Services::Registry()->get('Route', 'extension_instances_id') . '<br /><pre>';
		var_dump(Services::Registry()->get('Extension'));
		echo 'Parameters<br />';
		var_dump(Services::Registry()->get('ExtensionParameters'));
		echo 'Custom Fields<br />';
		var_dump(Services::Registry()->get('ExtensionCustomfields'));
		echo 'Metadata<br />';
		var_dump(Services::Registry()->get('ExtensionMetadata'));
		echo '</pre>';

		echo '<br /><br />Primary Category<br /><pre>';
		echo 'id ' . Services::Registry()->get('Route', 'primary_category_id') . '<br /><pre>';
		var_dump(Services::Registry()->get('Category'));
		echo 'Parameters<br /><pre>';
		var_dump(Services::Registry()->get('CategoryParameters'));
		echo 'Metadata<br />';
		var_dump(Services::Registry()->get('CategoryMetadata'));
		echo '</pre>';


		echo '<br /><br />Theme<br /><pre>';
		var_dump(Services::Registry()->get('Theme'));
		echo '<br /><br />Parameters<br /><pre>';
		var_dump(Services::Registry()->get('ThemeParameters'));
		echo '<br /><br />Custom Fields<br /><pre>';
		var_dump(Services::Registry()->get('ThemeCustomfields'));
		echo '<br /><br />Metadata<br /><pre>';
		var_dump(Services::Registry()->get('ThemeMetadata'));
		echo '</pre>';

		echo '<br /><br />PageView<br /><pre>';
		var_dump(Services::Registry()->get('PageView'));
		echo '<br /><br />Parameters<br /><pre>';
		var_dump(Services::Registry()->get('PageViewParameters'));
		echo '<br /><br />Custom Fields<br /><pre>';
		var_dump(Services::Registry()->get('PageViewCustomfields'));
		echo '<br /><br />Metadata<br /><pre>';
		var_dump(Services::Registry()->get('PageViewMetadata'));
		echo '</pre>';


		echo '<pre>';
		var_dump(Services::Registry()->get('Metadata'));

	}


	/**
	 * loadMetadata
	 *
	 * Loads Metadata values into Services::Document Metadata array
	 *
	 * @return  null
	 * @since   1.0
	 */
	protected function loadMetadata()
	{
		// todo: add event for metadata

		Services::Registry()->set('Metadata', 'title', '');
		Services::Registry()->set('Metadata', 'description', '');
		Services::Registry()->set('Metadata', 'keywords', '');
		Services::Registry()->set('Metadata', 'robots', '');
		Services::Registry()->set('Metadata', 'author', '');
		Services::Registry()->set('Metadata', 'content_rights', '');

		if (Services::Registry()->get('Request', 'status_error') == true) {
			Services::Registry()->set('Metadata', 'title',
				Services::Language()->translate('ERROR_FOUND'));
			return;
		}

		/** Last Modified */
		$date = Services::Date()->getDate();

		if (Services::Registry()->get('Menuitem', 'modified_datetime') == NULL) {
		} else {
			$date = Services::Registry()->get('Menuitem', 'modified_datetime');
		}
		if (Services::Registry()->get('Content', 'modified_datetime') == NULL) {
		} else {
			$date = Services::Registry()->get('Content', 'modified_datetime');
		}
		Services::Registry()->set('Metadata', 'modified_datetime', $date);

		/** Metadata */
		$this->setMetadata('SiteMetadata');
		$this->setMetadata('ApplicationMetadata');
		$this->setMetadata('ExtensionMetadata');
		$this->setMetadata('CategoryMetadata');
		$this->setMetadata('MenuMetadata');
		$this->setMetadata('Metadata');

	}

	/**
	 * setMetadata for specific Namespace
	 *
	 * @return  null
	 * @since   1.0
	 */
	protected function setMetadata($namespace)
	{
		$metadata = Services::Registry()->get($namespace);

		if (count($metadata) > 0) {
			foreach ($metadata as $key => $value) {
				if (substr($key, 0, 9) == 'metadata_')  {
					$key = substr($key, 9, strlen($key) - 9);
				}
				if ($value == '' || $value === null) {
				} else {
					Services::Registry()->set('Metadata', $key, $value);
				}
			}
		}
	}
}
