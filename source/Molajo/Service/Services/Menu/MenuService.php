<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Service\Services\Menu;

use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Menu
 *
 * @package     Molajo
 * @subpackage  Service
 * @since       1.0
 */
Class MenuService
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
			self::$instance = new MenuService();
		}

		return self::$instance;
	}

	/**
	 * Retrieve requested menu, format data, build link, verify ACL
	 *
	 * @param   int $extension_instance_id - menu
	 * @param   int $start_lvl layer within a menu
	 * @param   int $end_lvl layer within a menu
	 * @param   int $parent_id
	 * @param   int $catalog_type_id
	 *
	 * CATALOG_TYPE_MENU_ITEM_COMPONENT
	 * CATALOG_TYPE_MENU_ITEM_LINK
	 * CATALOG_TYPE_MENU_ITEM_TEMPLATE_VIEW
	 * CATALOG_TYPE_MENU_ITEM_SEPARATOR
	 *
	 * Range:
	 * CATALOG_TYPE_MENU_ITEM_BEGIN
	 * CATALOG_TYPE_MENU_ITEM_END
	 *
	 * @return  array|bool
	 * @since   1.0
	 */
	public function runMenuQuery($extension_instance_id, $start_lvl = 0, $end_lvl = 0, $parent_id = 0, $catalog_type_id = 0)
	{

		echo ' <br /><br /><br />'
			.'Menu ID  '. $extension_instance_id
			. ' Lvl '.  $start_lvl . ' - ' . $end_lvl
			. ' Parent '.  $parent_id
			. ' Catalog Type ID: ' . $catalog_type_id . ' <br /><br /><br />';

		if ($extension_instance_id == 0) {
			return false;
		}

		/** Query Connection */
		$controllerClass = 'Molajo\\MVC\\Controller\\ModelController';
		$m = new $controllerClass();

		$results = $m->connect('Table', 'Menuitem');
		if ($results == false) {
			return false;
		}

		/** Set Criteria */
		if ($end_lvl == 0) {
			$end_lvl = 999999;
		}
		if ($start_lvl == 0 && $end_lvl == 999999) {
		} else {
			$m->model->query->where($m->model->db->qn('a.lvl') . ' >= ' . (int) $start_lvl
				. ' AND ' . $m->model->db->qn('a.lvl') . ' <= ' . (int) $end_lvl);
		}

		if ($catalog_type_id == 0) {
		} else {
			$m->model->query->where($m->model->db->qn('a.catalog_type_id') . ' = ' . (int) $catalog_type_id);
		}

		$m->model->query->where($m->model->db->qn('a.extension_instance_id') . ' = ' . (int) $extension_instance_id);

		if ((int) $parent_id == 0) {
		} else {
			$m->model->query->where($m->model->db->qn('a.parent_id') . ' = ' . (int) $parent_id);
		}

		$m->model->query->order('a.ordering');

		/** Execute query */
		$query_results = $m->getData('list');

//		echo '<br /><br /><br />';
//		echo $m->model->query->__toString();
//		echo '<br /><br /><br />';

		if ($query_results === false) {
			return array();
		}

		/** Add in URL */
		foreach ($query_results as $item) {
			if  ($item->catalog_type_id == CATALOG_TYPE_MENU_ITEM_COMPONENT) {
				if (Services::Registry()->get('Configuration', 'url_sef', 1) == 1) {
					$item->url = Services::Url()->getApplicationURL($item->catalog_sef_request);
				} else {
					$item->url = Services::Url()->getApplicationURL('index.php?id='.(int) $item->id);
				}
			}
		}

		return $query_results;
	}


	/**
	 * Retrieves an array of active menuitems, including the current menuitem and its parents
	 *
	 * @param   int $extension_instance_id - menu
	 *
	 * @return  array|bool
	 * @since   1.0
	 */
	public function getMenuBreadcrumbIds($extension_instance_id)
	{

		echo ' <br /><br /><br />'
			.'Get Current Menuitem IDs  '. $extension_instance_id
			. ' <br /><br /><br />';

		if ($extension_instance_id == 0) {
			return false;
		}
		Services::Registry()->get('Parameters', '*');

		/** Current */
		$current_menu_item_url = Services::Registry()->get('Configuration', 'application_base_url');

		if (Services::Registry()->get('Configuration', 'url_sef') == 1) {
			$current_menu_item_url .= '/' . Services::Registry()->get('Parameters', 'catalog_url_sef_request');
		} else {
			$current_menu_item_url .= '/' . Services::Registry()->get('Parameters', 'catalog_url_request');
		}

		$current_menuitem_id =  Services::Registry()->get('Parameters', 'catalog_source_id');

		/** Query Connection */
		$controllerClass = 'Molajo\\MVC\\Controller\\ModelController';
		$m = new $controllerClass();

		$results = $m->connect('Table', 'MenuitemNested');
		if ($results == false) {
			return false;
		}

		$m->model->query->where($m->model->db->qn('current_menuitem.id') . ' = ' . (int) $current_menuitem_id);

		$m->model->query->order('a.lft');

		/** Execute query */
		$query_results = $m->getData('list');
/**
		echo '<br /><br /><br />';
		echo $m->model->query->__toString();
		echo '<br /><br /><br />';

		echo '<br /><br /><br /><pre>';
		var_dump($query_results);
		echo '</pre><br /><br /><br />';
		die;
		if ($query_results === false) {
			return array();
		}
*/
		/** Add in URL */
		foreach ($query_results as $item) {
			if  ($item->catalog_type_id == CATALOG_TYPE_MENU_ITEM_COMPONENT) {
				if (Services::Registry()->get('Configuration', 'url_sef', 1) == 1) {
					$item->url = Services::Url()->getApplicationURL($item->catalog_sef_request);
				} else {
					$item->url = Services::Url()->getApplicationURL('index.php?id='.(int) $item->id);
				}
			}
		}

//echo '<br /><br /><br /><pre>';
//var_dump($query_results);
//echo '</pre><br /><br /><br />';

		return $query_results;
	}

	/**
	 *     Dummy functions to pass service off as a DBO to interact with model
	 */
	public function get($option = null)
	{
		if ($option == 'db') {
			return $this;
		}
	}

	public function getNullDate()
	{
		return $this;
	}

	public function getQuery()
	{
		return $this;
	}

	public function toSql()
	{
		return $this;
	}

	public function clear()
	{
		return $this;
	}

	/**
	 * getData - simulates DBO - interacts with the Model getMenulist method
	 *
	 * @param $registry
	 * @param $element
	 * @param $single_result
	 *
	 * @return array
	 * @since    1.0
	 */
	public function getData($menu, $menu_item)
	{
		$query_results = array();

		/** Return results to Model */
		return $query_results;
	}
}
