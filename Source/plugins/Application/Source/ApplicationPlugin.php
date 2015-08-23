<?php
/**
 * Application Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Application;

use CommonApi\Event\SystemEventInterface;
use stdClass;

/**
 * Application Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
final class ApplicationPlugin extends Metadata implements SystemEventInterface
{
    /**
     * Menu Item Catalog Type ID
     *
     * @var    string
     * @since  1.0.0
     */
    protected $menu_item_catalog_type_id = 11000;

    /**
     * Prepares Page Information storing results in $this->plugin_data->page
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeExecute()
    {
        $this->plugin_data->messages = array();

        if ($this->checkProcessPlugin() === false) {
            return $this;
        }

        return $this->createApplicationData();
    }

    /**
     * Should plugin be executed?
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function checkProcessPlugin()
    {
        if ($this->runtime_data->request->client->ajax === 1) {
            return false;
        }

        return true;
    }

    /**
     * Prepares Page Information storing results in $this->plugin_data->page
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function createApplicationData()
    {
        $this->plugin_data->page = new stdClass();

        $this->setPageType();
        $this->setMenuItemIds();
        $this->getPageTitle();
        $this->setPageMeta();

        return $this;
    }

    /**
     * Set Page Type
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setPageType()
    {
        $this->page_type = strtolower($this->runtime_data->route->page_type);

        return $this;
    }

    /**
     * Set Menu Item IDs
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setMenuItemIds()
    {
        if (in_array($this->page_type, array('item', 'edit', 'new', 'list'))) {
            $menu_id    = $this->runtime_data->resource->parameters->parent_menu_id;
            $current_id = 0;
        } else {
            $menu_id    = $this->runtime_data->resource->data->id;
            $current_id = $menu_id;
        }

        $current_menu_item
            = $this->runtime_data
                  ->reference_data
                  ->extensions
                  ->extensions[$this->menu_item_catalog_type_id]
                  ->extensions[$menu_id];

        if ($current_id === 0) {
            $parent_id = $menu_id;
        } else {
            $parent_id = $current_menu_item->parent_id;
        }

        $this->setMenuItemPageValues($current_id, $parent_id, $current_menu_item->root);

        return $this;
    }

    /**
     * Set Menuitem Page values
     *
     * @param   int $current_id
     * @param   int $parent_id
     * @param   int $root_id
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setMenuItemPageValues($current_id, $parent_id, $root_id)
    {
        $this->plugin_data->page->root_menuitem_id    = $root_id;
        $this->plugin_data->page->parent_menuitem_id  = $parent_id;
        $this->plugin_data->page->current_menuitem_id = $current_id;
        $this->plugin_data->page->menuitem_id         = $parent_id;

        return $this;
    }
}
