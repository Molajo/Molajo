<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Application1;

use Molajo\Application;
use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class Application1Plugin extends Plugin
{
    /**
     * Prepares Application1 Menus
     *
     * @return boolean
     * @since   1.0
     */
    public function onBeforeParse()
    {
        /** Only used for the Application1 */
        if (APPLICATION_ID == 1) {
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
            //return true;
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
     * @return boolean
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
        Services::Registry()->set('Plugindata', 'Application1breadcrumbs', $bread_crumbs);

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
        $bread_crumbs = Services::Registry()->get('Plugindata', 'Application1breadcrumbs');

        $menu_id = $bread_crumbs[0]->extension_id;
        $query_results = Services::Menu()->get($menu_id, $current_menu_item, $bread_crumbs);
        Services::Registry()->set('Plugindata', 'Application1applicationmenu', $query_results);

        return true;
    }

    /**
     * Set the Header Title
     *
     * @return boolean
     * @since   1.0
     */
    protected function setPageTitle($item_indicator = 0)
    {
        $title = '<strong> Molajo</strong> '. Services::Language()->translate('Integration Framework');

        Services::Registry()->set('Plugindata', 'HeaderTitle', $title);

        return true;
    }
}
