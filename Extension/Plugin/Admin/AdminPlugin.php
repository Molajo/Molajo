<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Extension\Plugin\Admin;

use Molajo\Extension\Plugin\Content\ContentPlugin;
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

        /** Data Source Connection */
        $controllerClass = 'Molajo\\Controller\\Controller';
        $connect = new $controllerClass();

        $results = $connect->connect(
            $this->get('model_type'),
            $this->get('model_name')
        );

        if ($results == false) {
            return false;
        }

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
        /** Detail rows are not defined as menu items but rather tied to a parent menuitem id */
        $current_menuitem_id = Services::Registry()->get('Parameters', 'parent_menuid', '0');

        /** Normal menu item is current */
        if ($current_menuitem_id == 0) {
            $current_menuitem_id = Services::Registry()->get('Parameters', 'catalog_source_id');
            $item_id = 0;
        } else {
            $item_id = Services::Registry()->get('Parameters', 'catalog_id');
        }

        /** Breadcrumbs */
        $bread_crumbs = Services::Menu()->getMenuBreadcrumbIds($current_menuitem_id);

        $activeCatalogID = array();
        foreach ($bread_crumbs as $item) {
            $activeCatalogID[] = $item->catalog_id;
        }
        if ($item_id > 0) {
            $activeCatalogID[] = $item_id;
        }

        Services::Registry()->set('Plugindata', 'Adminbreadcrumbs', $bread_crumbs);

        $menuArray = array();

        // 1. Home
        $menuArray[] = 'Adminnavigationbar';
        $menuArray[] = 'Adminsectionmenu';
        if (count($bread_crumbs) > 2) {
            $menuArray[] = 'Adminstatusmenu';
        }

        $i = 0;
        foreach ($bread_crumbs as $item) {

            $extension_instance_id = $item->extension_instance_id;
            $lvl = $item->lvl + 1;
            $parent_id = $item->id;

            $query_results = Services::Menu()->runMenuQuery(
                $extension_instance_id, $lvl, $lvl, $parent_id, $activeCatalogID
            );

            Services::Registry()->set('Plugindata', $menuArray[$i], $query_results);
            $i++;

            if ($i > count($menuArray) - 1) {
                break;
            }
        }
/**
        echo '<br />Adminnavigationbar <br />';
        echo '<pre>';
        var_dump(Services::Registry()->get('Plugindata','Adminnavigationbar'));
        echo '</pre>';

        echo '<br />Adminsectionmenu <br />';
        echo '<pre>';
        var_dump(Services::Registry()->get('Plugindata','Adminsectionmenu'));
        echo '</pre>';

        echo '<br />Adminstatusmenu <br />';
        echo '<pre>';
        var_dump(Services::Registry()->get('Plugindata','Adminstatusmenu'));
        echo '</pre>';

        echo '<br />Adminbreadcrumbs <br />';
        echo '<pre>';
        var_dump(Services::Registry()->get('Plugindata','Adminbreadcrumbs'));
        echo '</pre>';
*/

        return;
    }

    /**
     * Set the Page Title, given Breadcrumb values
     *
     * @param int $extension_instance_id - menu
     *
     * @return object
     * @since   1.0
     */
    public function setPageTitle()
    {
        $bread_crumbs = Services::Registry()->get('Plugindata', 'Adminbreadcrumbs');

        $title = $bread_crumbs[count($bread_crumbs) - 1]->title;

        Services::Registry()->set('Plugindata', 'PageTitle', $title);

        return $this;
    }
}
