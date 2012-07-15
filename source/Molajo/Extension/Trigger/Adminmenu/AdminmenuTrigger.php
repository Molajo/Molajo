<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
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
     * Prepares Administrator Menus
     *
     * Run this LAST
     *
     * @return boolean
     * @since   1.0
     */
    public function onBeforeParse()
    {
        /** Data Source Connection */
        $controllerClass = 'Molajo\\Controller\\ReadController';
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
        $current_menuitem_id = Services::Registry()->get('Parameters', 'parent_menuitem', '0');

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

        Services::Registry()->set('Triggerdata', 'AdminBreadcrumbs', $bread_crumbs);

        $menuArray = array();

        // 1. Home
        $menuArray[] = 'Adminnavigationbar';
        $menuArray[] = 'Adminsectionmenu';
        $menuArray[] = 'Adminresourcemenu';
        $menuArray[] = 'Adminitemmenu';

        $i = 0;
        foreach ($bread_crumbs as $item) {

            $extension_instance_id = $item->extension_instance_id;
            $lvl = $item->lvl + 1;
            $parent_id = $item->id;

            $query_results = Services::Menu()->runMenuQuery(
                $extension_instance_id, $lvl, $lvl, $parent_id, $activeCatalogID
            );

            Services::Registry()->set('Triggerdata', $menuArray[$i], $query_results);
			$i++;

            if ($i > 3) {
                break;
            }
        }

		echo '<br />Adminnavigationbar <br />';
		echo '<pre>';
		var_dump(Services::Registry()->get('Triggerdata','Adminnavigationbar'));
		echo '</pre>';

		echo '<br />Adminsectionmenu <br />';
		echo '<pre>';
		var_dump(Services::Registry()->get('Triggerdata','Adminsectionmenu'));
		echo '</pre>';

		echo '<br />Adminresourcemenu <br />';
		echo '<pre>';
		var_dump(Services::Registry()->get('Triggerdata','Adminresourcemenu'));
		echo '</pre>';

		echo '<br />Adminitemmenu <br />';
		echo '<pre>';
		var_dump(Services::Registry()->get('Triggerdata','Adminitemmenu'));
		echo '</pre>';

		echo '<br />AdminBreadcrumbs <br />';
		echo '<pre>';
		var_dump(Services::Registry()->get('Triggerdata','AdminBreadcrumbs'));
		echo '</pre>';
		die;
		return;
    }

    /**
     * Set the Title, given the Breadcrumb values
     *
     * @param int $extension_instance_id - menu
     *
     * @return object
     * @since   1.0
     */
    public function setPageTitle()
    {
        $bread_crumbs = Services::Registry()->get('Triggerdata', 'AdminBreadcrumbs');

        $title = '';
        foreach ($bread_crumbs as $item) {
            $title = $item->title;
        }

        Services::Registry()->set('Triggerdata', 'AdminTitle', $title);

        return $this;
    }
}
