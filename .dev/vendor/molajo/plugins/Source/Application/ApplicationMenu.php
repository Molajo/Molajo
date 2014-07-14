<?php
/**
 * Application Menu
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Application;

use stdClass;

/**
 * Application Menu
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
abstract class ApplicationMenu extends ApplicationBreadcrumbs
{

    /**
     * Set Current Menu Item ID
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setCurrentMenuitemId()
    {
        if ($this->page_type === 'item' || $this->page_type === 'edit') {
            return $this->runtime_data->resource->parameters->parent_menu_id;

        } elseif ($this->page_type === 'list') {
            return $this->runtime_data->resource->parameters->list_parent_menu_id;
        }

        return $this->setPluginDataMenu();
    }

    /**
     * Set Menu Item Data in Plugin Data
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setPluginDataMenu()
    {
        if ((int)$this->runtime_data->resource->menuitem->data->id === 0) {
            $this->plugin_data->page->menuitem_id         = 0;
            $this->plugin_data->page->menuitem            = new stdClass();
            $this->plugin_data->page->menu                = new stdClass();
            $this->plugin_data->page->current_menuitem_id = 0;
            return $this;
        }

        $this->plugin_data->page->menuitem_id         = $this->runtime_data->resource->menuitem->data->id;
        $this->plugin_data->page->menuitem            = new stdClass();
        $this->plugin_data->page->menu                = new stdClass();
        $this->plugin_data->page->current_menuitem_id = $this->runtime_data->resource->menuitem->data->id;

        return $this;
    }

    /**
     * Retrieve an array of values that represent the active menuitem ids for a specific menu
     *
     * @return  $this
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getMenu()
    {
        $controller = $this->setMenuQuery();

        $rows = $controller->getData();
echo '<pre>rows';
var_dump($rows);
        die;
        if (count($rows) === 0) {
            return $this;
        }

        foreach ($rows as $item) {
            $this->setMenuItem($item);
        }

        $this->plugin_data->page->menu             = array();
        $menu_name                                 = $this->plugin_data->page->menuitem->menu;
        $this->plugin_data->page->menu[$menu_name] = $rows;


        return $this;
    }

    /**
     * Process menu item
     *
     * @param  object $item
     *
     * @return ApplicationMenu
     */
    protected function setMenuItem($item)
    {
        $item->menu_id = $item->extension_id;

        $item = $this->setMenuItemCss($item);

        $item = $this->setMenuItemCurrent($item);

        $item = $this->setMenuItemLink($item);

        if ($item->current === 1) {
            $this->plugin_data->page->menuitem = $item;
        }

        return $this;
    }

    /**
     * Process menu item CSS
     *
     * @param   object $item
     *
     * @return  object
     * @since   1.0.0
     */
    protected function setMenuItemCss($item)
    {
        if ($item->id === $this->plugin_data->page->current_menuitem_id
            && (int)$this->plugin_data->page->current_menuitem_id > 0
        ) {
            $item->css_class = 'current';
            $item->current   = 1;

        } else {
            $item->css_class = '';
            $item->current   = 0;
        }

        return $item;
    }

    /**
     * Process current menu item
     *
     * @param   object $item
     *
     * @return  object
     * @since   1.0.0
     */
    protected function setMenuItemCurrent($item)
    {
        $item->active = 0;

        foreach ($this->plugin_data->page->breadcrumbs as $crumb) {

            if ($item->id === $crumb->id) {
                $item->css_class .= ' active';
                $item->active = 1;
            }
        }

        $item->css_class = trim($item->css_class);

        return $item;
    }

    /**
     * Process current menu item
     *
     * @param   object $item
     *
     * @return  object
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

        return $item;
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
        $menu_id = $this->plugin_data->page->extension_id;

        $controller = $this->resource->get('query:///Molajo//Model//Datasource//Menuitem.xml');

        $controller->setModelRegistry('process_events', 0);
        $controller->setModelRegistry('get_customfields', 0);
        $controller->setModelRegistry('query_object', 'list');
        $controller->setModelRegistry('use_pagination', 0);

        $prefix = $controller->getModelRegistry('primary_prefix', 'a');

        $controller->where('column', $prefix . '.' . 'extension_id', '=', 'integer', (int)$menu_id);
        $controller->where('column', $prefix . '.' . 'status', '>', 'integer', 0);
        $controller->where('column', 'catalog.enabled', '=', 'integer', 1);

        $controller->orderBy($prefix . '.' . 'menu', 'ASC');
        $controller->orderBy($prefix . '.' . 'lft', 'ASC');

        return $controller;
    }

    /**
     * Get Navigation Bar Data
     *
     * @return  $this
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getNavbarMenu()
    {
        $menu         = $this->plugin_data->page->menu['Menuadmin'];
        $previous_lvl = 0;
        $count        = 0;
        $navbar       = array();

        foreach ($menu as $item) {
            $temp               = clone $item;
            $temp->previous_lvl = $previous_lvl;
            $temp->home_url     = $this->plugin_data->page->urls['home'];
            $temp->page_url     = $this->plugin_data->page->urls['page'];
            $temp->count        = $count ++;
            $navbar[]           = $temp;
            $previous_lvl       = $temp->lvl;
        }

        $this->plugin_data->navbar = $navbar;

        return $this;
    }
}
