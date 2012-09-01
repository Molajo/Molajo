<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Admin;

use Molajo\Application;
use Molajo\Plugin\Content\ContentPlugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class AdminPlugin extends ContentPlugin
{
	/**
	 * Prepares Admin Menus
	 *
	 * Run this LAST
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onBeforeParse()
	{
		/** Only used for the Admin */
		if (APPLICATION_ID == 2) {
		} else {
			return true;
		}

		/** Not authorised and not found */
		if ($this->get('model_type') == '' || $this->get('model_name') == '') {
			return true;
		}

		$current_menuitem_id = (int) Services::Registry()->get('Parameters', 'menuitem_id');

		$item_indicator = 0;
		if ((int) $current_menuitem_id == 0) {
			$item_indicator = 1;
			$current_menuitem_id = (int) Services::Registry()->get('Parameters', 'parent_menu_id');
		}

		if ((int) $current_menuitem_id == 0) {
			return true;
		}

		$this->urls();

		/** Data Source Connection */
		$controllerClass = 'Molajo\\MVC\\Controller\\Controller';
		$connect = new $controllerClass();

		$results = $connect->connect($this->get('model_type'), $this->get('model_name'));
		if ($results == false) {
			return false;
		}

		$this->setBreadcrumbs($current_menuitem_id);

		$this->setMenu($current_menuitem_id);

		$this->setPageTitle($item_indicator);

		return true;
	}

	/**
	 * Build the home and page url to be used in links
	 *
	 * @return  bool
	 * @since   1.0
	 */
	protected function urls()
	{
		$url = Application::Request()->get('base_url_path_for_application') .
			Application::Request()->get('requested_resource_for_route');

		Services::Registry()->set('Plugindata', 'page_url', $url);

		Services::Asset()->addLink($url, 'canonical', 'rel', array(), 1);

		$url = Services::Registry()->get('Configuration', 'application_base_url');
		Services::Registry()->set('Plugindata', 'home_url', $url);

		return true;
	}

	/**
	 * Set breadcrumbs
	 *
	 * @return void
	 * @since  1.0
	 */
	protected function setBreadcrumbs($current_menuitem_id)
	{
		$bread_crumbs = Services::Menu()->getMenuBreadcrumbIds($current_menuitem_id);
		Services::Registry()->set('Plugindata', 'Adminbreadcrumbs', $bread_crumbs);
	}

	/**
	 * Retrieve an array of values that represent the active menuitem ids for a specific menu
	 *
	 * @return void
	 * @since  1.0
	 */
	protected function setMenu($current_menu_item = 0)
	{
		$bread_crumbs = Services::Registry()->get('Plugindata', 'Adminbreadcrumbs');

		$menuArray = array();
		$menuArray[] = 'Adminhome';
		$menuArray[] = 'Adminnavigationbar';
		$menuArray[] = 'Adminasectionmenu';

		$i = 0;
		foreach ($bread_crumbs as $level) {

			$menu_id = $level->extension_id;
			$parent_id = $level->parent_id;

			if ($i == 0) {
				$query_results = Services::Menu()->get($menu_id, $current_menu_item, $bread_crumbs);
				Services::Registry()->set('Plugindata', 'Adminapplicationmenu', $query_results);
				$level = 0;
			}

			$list = array();
			foreach ($query_results as $menu_items) {
				if ((int) $parent_id == (int) $menu_items->parent_id) {
					$list[] = $menu_items;
				}
			}

			Services::Registry()->set('Plugindata', $menuArray[$i], $list);

			$i++;
			if ($i > count($menuArray) - 1) {
				break;
			}
		}

/**
		echo '<br />Adminhome <br />';
		echo '<pre>';
		var_dump(Services::Registry()->get('Plugindata','Adminhome'));
		echo '</pre>';

		echo '<br />Adminnavigationbar <br />';
		echo '<pre>';
		var_dump(Services::Registry()->get('Plugindata','Adminnavigationbar'));
		echo '</pre>';

		echo '<br />Adminasectionmenu <br />';
		echo '<pre>';
		var_dump(Services::Registry()->get('Plugindata','Adminasectionmenu'));
		echo '</pre>';

		echo '<br />Adminbreadcrumbs <br />';
		echo '<pre>';
		var_dump(Services::Registry()->get('Plugindata','Adminbreadcrumbs'));
		echo '</pre>';

		echo '<br />Adminapplicationmenu <br />';
		echo '<pre>';
		var_dump(Services::Registry()->get('Plugindata','Adminapplicationmenu'));
		echo '</pre>';
*/
		return;

	}

	/**
	 * Set the Header and Page Titles
	 *
	 * @return object
	 * @since   1.0
	 */
	protected function setPageTitle($item_indicator = 0)
	{
		$title = '<strong>Molajo</strong> '. Services::Language()->translate('Administrator');

		if ((int) $item_indicator == 0) {
			$bread_crumbs = Services::Registry()->get('Plugindata', 'Adminbreadcrumbs');
			$subtitle = $bread_crumbs[count($bread_crumbs) - 1]->title;
		} else {
			$subtitle = '';
		}
		if (trim($subtitle) == '') {
			$subtitle = $this->parameters['criteria_title'];
		}

		if (trim($subtitle) == '') {
		} else {
			$title .= ' - ' . $subtitle;
		}

		Services::Registry()->set('Plugindata', 'HeaderTitle', $title);

		Services::Registry()->set('Plugindata', 'PageTitle', $subtitle);

		return $this;
	}
}
