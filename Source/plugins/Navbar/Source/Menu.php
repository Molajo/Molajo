<?php
/**
 * Menu Class for Navbar Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Navbar;

use stdClass;
use Molajo\Plugins\DisplayEvent;

/**
 * Menu Class for Navbar Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
abstract class Menu extends DisplayEvent
{
    /**
     * Page Type
     *
     * @var    string
     * @since  1.0.0
     */
    protected $page_type;

    /**
     * Current Menu Item
     *
     * @var    object
     * @since  1.0.0
     */
    protected $current_menu_item;

    /**
     * Menu Item Catalog Type ID
     *
     * @var    string
     * @since  1.0.0
     */
    protected $menu_item_catalog_type_id = 11000;

    /**
     * Menu
     *
     * @var    object
     * @since  1.0.0
     */
    protected $menu;

    /**
     * Retrieve an array of values that represent the active menuitem ids for a specific menu
     *
     * @return  $this
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getMenu()
    {
        if ($this->getMenuCache() === true) {
            return $this;
        }

        if (in_array($this->page_type, array('item', 'edit', 'new', 'list'))) {
            $menu_id    = $this->runtime_data->resource->parameters->parent_menu_id;
        } else {
            $menu_id    = $this->runtime_data->resource->data->id;
        }

        $this->current_menu_item
            = $this->runtime_data
                  ->reference_data
                  ->extensions
                  ->extensions[$this->menu_item_catalog_type_id]
                  ->extensions[$menu_id];

        $this->setMenuQuery();

        $rows = $this->runQuery();

        $this->setMenu($rows);

        $this->setMenuCache();

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
     * Set Current Menu
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setCurrentMenu()
    {
        if (in_array($this->page_type, array('item', 'edit', 'new', 'list'))) {
            $menu_id    = $this->runtime_data->resource->parameters->parent_menu_id;
        } else {
            $menu_id    = $this->runtime_data->resource->data->id;
        }

        $this->current_menu_item
            = $this->runtime_data
                  ->reference_data
                  ->extensions
                  ->extensions[$this->menu_item_catalog_type_id]
                  ->extensions[$menu_id];

        return $this;
    }

    /**
     * Set Menu
     *
     * @param   $rows array
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setMenu(array $rows = array())
    {
        $model_registry = $this->query->getModelRegistry();

        if (count($rows) == 0) {
            return $this;
        }

        foreach ($rows as $item) {
            $this->setMenuItem($item);
        }

        $data = $this->setMenuItemUrl($rows);

        $this->menu                 = new stdClass();
        $this->menu->data           = $data;
        $this->menu->model_registry = $model_registry;

        $this->plugin_data->{strtolower($this->controller['parameters']->token->model_name)} = $this->menu;

        return $this;
    }

    /**
     * Set Menu Query
     *
     * @return  $this
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function setMenuQuery()
    {
        $menu_id = $this->plugin_data->page->root_menuitem_id;

        $this->setQueryController('Molajo//Model//Datasource//Menuitem.xml');

        $this->setQueryControllerDefaults(
            $process_events = 1,
            $query_object = 'list',
            $get_customfields = 1,
            $use_special_joins = 1,
            $use_pagination = 0,
            $check_view_level_access = 1,
            $get_item_children = 0
        );

        $prefix = $this->query->getModelRegistry('primary_prefix', 'a');

        $this->query->where(
            'column',
            $prefix . '.' . 'catalog_type_id',
            '=',
            'integer',
            (int)$this->menu_item_catalog_type_id
        );
        $this->query->where('column', $prefix . '.' . 'root', '=', 'integer', (int)$menu_id);
        $this->query->where('column', $prefix . '.' . 'status', '>', 'integer', 0);
        $this->query->where('column', 'catalog.enabled', '=', 'integer', 1);

        $this->query->orderBy($prefix . '.' . 'menu', 'ASC');
        $this->query->orderBy($prefix . '.' . 'lft', 'ASC');

        return $this;
    }

    /**
     * Process a single menu item
     *
     * @param   object $item
     *
     * @return  object
     * @since   1.0.0
     */
    protected function setMenuItem($item)
    {
        $item->menu_id = $item->extension_id;

        $this->setMenuItemCurrent($item);
        $this->setMenuItemActive($item);
        $this->setMenuItemLink($item);

        if ($item->current === 1) {
            $this->plugin_data->page->menuitem = $item;
        }

        return $item;
    }

    /**
     * Process menu item CSS
     *
     * @param   object $item
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setMenuItemCurrent($item)
    {
        if ($item->id === $this->plugin_data->page->current_menuitem_id) {
            $item->css_class = 'current';
            $item->current   = 1;

        } else {
            $item->css_class = '';
            $item->current   = 0;
        }

        return $this;
    }

    /**
     * Process current menu item
     *
     * @param   object $item
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setMenuItemActive($item)
    {
        $item->active = 0;

        if ($item->root === $this->current_menu_item->root
            && $item->lft <= $this->current_menu_item->lft
            && $item->rgt >= $this->current_menu_item->rgt
        ) {
            $item->css_class .= ' active';
            $item->active = 1;
        }

        $item->css_class = trim($item->css_class);

        return $this;
    }

    /**
     * Process current menu item
     *
     * @param   object $item
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setMenuItemLink($item)
    {
        $base = $this->runtime_data->application->base_url;

        $item->url  = $base . 'index.php?id=' . (int)$item->catalog_id;
        $item->link = $base . $item->catalog_sef_request;

        if ($item->subtitle === '' || $item->subtitle === null) {
            $item->link_text = $item->title;
        } else {
            $item->link_text = $item->subtitle;
        }

        return $this;
    }

    /**
     * Get Navigation Bar Data
     *
     * @param   object $menu
     *
     * @return  array
     * @since   1.0.0
     */
    protected function setMenuItemUrl($menu)
    {
        $previous_lvl = 0;
        $count        = 0;
        $data = array();

        foreach ($menu as $item) {
            $temp                  = clone $item;
            $temp->previous_lvl    = $previous_lvl;
            $temp->home_url        = $this->runtime_data->application->base_url;
            $temp->page_url        = $this->runtime_data->request->data->url;
            $temp->count           = $count++;

            $data[] = $temp;

            $previous_lvl          = $temp->lvl;
        }

        return $data;
    }

    /**
     * Get Menu from Cache, if available
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function getMenuCache()
    {
        if ($this->usePluginCache() === false) {
            return false;
        }

        $cache_key = $this->setMenuCacheKey();

        $cache_item = $this->getPluginCache($cache_key);

        if ($cache_item->isHit() === false) {
            return false;
        }

        $this->plugin_data->{strtolower($this->controller['parameters']->token->model_name)}
            = $cache_item->getValue();

        return true;
    }

    /**
     * Set Menu Cache
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setMenuCache()
    {
        if ($this->usePluginCache() === false) {
            return $this;
        }

        $cache_key = $this->setMenuCacheKey();

        $this->setPluginCache(
            $cache_key,
            $this->plugin_data->{strtolower($this->controller['parameters']->token->model_name)}
        );

        return $this;
    }

    /**
     * Set Menu Cache Key
     *
     * @return  string
     * @since   1.0.0
     */
    protected function setMenuCacheKey()
    {
        return 'Navbar-Menu-'
        . (int)$this->plugin_data->page->root_menuitem_id
        . (int)$this->plugin_data->page->menuitem_id
        . (int)$this->menu_item_catalog_type_id;
    }
}
