<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Trigger\Adminmenus;

use Molajo\Extension\Trigger\Content\ContentTrigger;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class AdminmenusTrigger extends ContentTrigger
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
			self::$instance = new AdminmenusTrigger();
		}

		return self::$instance;
	}

	/**
	 * Before-read processing
	 *
	 * Prepares data for the Administrator Grid  - position AdminmenusTrigger before Admingrid
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onAfterRoute()
	{

		echo 'in AdminmenusTrigger';
		die;

		/** Is this an Administrative Request?  */
		//todo: find a better way
		if (APPLICATION_ID == 2) {
		} else {
			return true;
		}

		Services::Registry()->get('Parameters', '*');
		die;
		/** 1. Navigation Bar  */
		$controllerClass = 'Molajo\\MVC\\Controller\\ModelController';
		$m = new $controllerClass();
		$results = $m->connect('Table', 'Content');
		if ($results == false) {
			return false;
		}

		$table_name = $m->get('table_name');

		$primary_prefix = $m->get('primary_prefix');
		$primary_key = $m->get('primary_key');
		$name_key = $m->get('name_key');
//menu id = 100
		$query_results = array();

		$row = new \stdClass();
		$row->link = $url.$connector.'&start='.$current + 15;
		$row->class = ' page-next';
		$row->link_text = ' 3';
		$query_results[] = $row;

		/** Home */
		if (Services::Registry()->get('Configuration', 'url_sef', 1) == 1) {
			$row->link = Services::Url()->getApplicationURL('');
		}  else {
			$row->link = Services::Url()->getApplicationURL(Services::Url()->getCatalogID(''));
		}

		/** Content */
		if (Services::Registry()->get('Configuration', 'url_sef', 1) == 1) {
			$row->link = Services::Url()->getApplicationURL('');
		}  else {
			$row->link = Services::Url()->getApplicationURL(Services::Url()->getCatalogID(''));
		}

		Services::Redirect()->redirect(Services::Url()->getApplicationURL($url), '301')->send();
		           Services::Registry()->get('Configuration', 'application_home_catalog_id');
		/** 1. Navigation Bar  */
		//admin">Home</a></li>
	//admin/content">Content</a></li>
	//admin/access">Access</a></li>
	//admin/build">Build</a></li>
	//admin/configure">Configure</a></li>
	//admin/install">Install</a></li>

		/** 2. Submen */

		/** 1. Prepare Submenu Data */
		$grid_submenu_items = explode(',', $this->get('grid_submenu_items', 'items,categories,drafts'));

		$query_results = array();

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

		if (count($grid_submenu_items) == 0 || $grid_submenu_items == null) {
		} else {

			foreach ($grid_submenu_items as $submenu) {
				$row = new \stdClass();
				$row->link = $url . $connector. 'submenu=' . $submenu;
				$row->link_text = Services::Language()->translate('SUBMENU_' . strtoupper($submenu));
				$query_results[] = $row;
			}
		}
		Services::Registry()->set('Trigger', 'AdminSubmenu', $query_results);



		return true;
	}
}
