<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Application2;

use Molajo\Application;
use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class Application2Plugin extends Plugin
{
	/**
	 * Prepares Application2 Menus
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onBeforeParse()
	{
		/** Only used for the Site */
		if (APPLICATION_ID == 2) {
		} else {
			return true;
		}

		$current_menuitem_id = (int) $this->get('menuitem_id');

		$item_indicator = 0;
		if ((int) $current_menuitem_id == 0) {
			$item_indicator = 1;
			$current_menuitem_id = (int) $this->get('parent_menu_id');
		}

		if ((int) $current_menuitem_id == 0) {
			return true;
		}

		$this->urls();

		$this->setBreadcrumbs($current_menuitem_id);

		$this->setMenu($current_menuitem_id);

		$this->setPageTitle($item_indicator);

		return true;
	}

	/**
	 * Build the home and page url to be used in links
	 *
	 * @return  boolean
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
	 * @return boolean
	 * @since  1.0
	 */
	protected function setBreadcrumbs($current_menuitem_id)
	{
		$bread_crumbs = Services::Menu()->getMenuBreadcrumbIds($current_menuitem_id);
		Services::Registry()->set('Plugindata', 'Breadcrumbs', $bread_crumbs);

		return true;
	}

	/**
	 * Retrieve an array of values that represent the active menuitem ids for a specific menu
	 *
	 * @return boolean
	 * @since  1.0
	 */
	protected function setMenu($current_menu_item = 0)
	{
		$bread_crumbs = Services::Registry()->get('Plugindata', 'Breadcrumbs');

		if ($bread_crumbs == false || count($bread_crumbs) == 0) {
			$query_results = array();
		} else {
			$menu_id = $bread_crumbs[0]->extension_id;
			$query_results = Services::Menu()->get($menu_id, $current_menu_item, $bread_crumbs);
		}

		Services::Registry()->set('Plugindata', 'Applicationmenu', $query_results);

		return true;
	}

	/**
	 * Set the Header Title
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	protected function setPageTitle($item_indicator = 0)
	{
		$title = '<strong> Molajo</strong> '. Services::Language()->translate('Site');

		Services::Registry()->set('Plugindata', 'HeaderTitle', $title);

		return true;
	}
}
