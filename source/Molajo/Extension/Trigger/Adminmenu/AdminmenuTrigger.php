<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Trigger\Adminmenu;

use Molajo\Extension\Trigger\Content\ContentTrigger;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class AdminmenuTrigger extends ContentTrigger
{
	/**
	 * Static instance
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected static $instance;

	/**
	 * getInstance
	 *
	 * @static
	 * @return bool|object
	 * @since  1.0
	 */
	public static function getInstance()
	{
		if (empty(self::$instance)) {
			self::$instance = new AdminmenuTrigger();
		}

		return self::$instance;
	}

	/**
	 * Before-read processing
	 *
	 * Prepares data for the Administrator Grid  - position AdminmenuTrigger last
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	public function onAfterRoute()
	{
		/** Data Source Connection */
		$model_type = $this->get('model_type');
		$model_name = $this->get('model_name');

		$controllerClass = 'Molajo\\Controller\\ModelController';
		$connect = new $controllerClass();

		$results = $connect->connect($model_type, $model_name);
		if ($results == false) {
			return false;
		}

		$table_name = $connect->get('table_name');

		$primary_prefix = $connect->get('primary_prefix');
		$primary_key = $connect->get('primary_key');
		$name_key = $connect->get('name_key');

		/** URL */
		$url = Services::Registry()->get('Configuration', 'application_base_url');

		if (Services::Registry()->get('Configuration', 'url_sef') == 1) {
			$url .= '/' . $this->get('catalog_url_sef_request');
			$connector = '?';
		} else {
			$url .= '/' . $this->get('catalog_url_request');
			$connector = '&';
		}

		Services::Registry()->set('Trigger', 'PageURL', $url);
		Services::Registry()->set('Trigger', 'PageURLConnector', $connector);

		/** Create Admin Menus, verifying ACL */
		$this->setMenu();
		$this->setPageTitle();

		return true;
	}

	/**
	 * Retrieve an array of values that represent the active menuitem ids for a specific menu
	 *
	 * @return void
	 * @since  1.0
	 */
	protected function setMenu()
	{
		$extension_instance_id = Services::Registry()->get('Parameters', 'menu_extension_instance_id');
		if ((int) $extension_instance_id == 0) {
			$catalog_type_id = Services::Registry()->get('Parameters', 'catalog_type_id');
			if ((int) $catalog_type_id == 10000) {
				$extension_instance_id = 100;
			}
		}

		$bread_crumbs = Services::Menu()->getMenuBreadcrumbIds($extension_instance_id);

		$activeCatalogID = array();
		foreach ($bread_crumbs as $item) {
			$activeCatalogID[] = $item->catalog_id;
		}

		Services::Registry()->get('Trigger', 'AdminBreadcrumbs', $bread_crumbs);

		$menuArray = array();
		$menuArray[] = 'Adminnavigationbar';
		$menuArray[] = 'Adminsectionmenu';
		$menuArray[] = 'Adminsubmenu';

		$i = 0;
		foreach ($bread_crumbs as $item) {

			$extension_instance_id = $item->extension_instance_id;
			$lvl = $item->lvl + 1;
			$parent_id = $item->id;

			$query_results = Services::Menu()->runMenuQuery(
				$extension_instance_id, $lvl, $lvl, $parent_id, 0, $activeCatalogID
			);

			Services::Registry()->set('Trigger', $menuArray[$i++], $query_results);

			if ($i > 2) {
				break;
			}
		}

		return;
	}

	/**
	 * Set the Title, given the Breadcrumb values
	 *
	 * @param   int $extension_instance_id - menu
	 *
	 * @return  object
	 * @since   1.0
	 */
	public function setPageTitle()
	{
		$bread_crumbs = Services::Registry()->get('Trigger', 'AdminBreadcrumbs');

		$title = '';
		foreach ($bread_crumbs as $item) {
			$title = $item->title;
		}

		Services::Registry()->set('Trigger', 'AdminTitle', $title);

		return $this;
	}
}
