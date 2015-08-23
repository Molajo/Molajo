<?php
/**
 * Breadcrumbs Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Breadcrumbs;

use CommonApi\Event\DisplayEventInterface;
use Molajo\Plugins\DisplayEvent;
use stdClass;

/**
 * Breadcrumbs Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
final class BreadcrumbsPlugin extends DisplayEvent implements DisplayEventInterface
{
    /**
     * Prepare Data for Injecting into Template
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onGetTemplateData()
    {
        if ($this->checkProcessPlugin() === false) {
            return $this;
        }

        $this->processPlugin();

        return $this;
    }

    /**
     * Should plugin be executed?
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function checkProcessPlugin()
    {
        if (strtolower($this->controller['parameters']->token->name) === 'breadcrumbs') {
        } else {
            return false;
        }

        return true;
    }

    /**
     * Process Plugin
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function processPlugin()
    {
        if ($this->getBreadcrumbsCache() === true) {
            return $this;
        }

        $this->getBreadcrumbsMenu();

        $this->setBreadcrumbsCache();

        return $this;
    }

    /**
     * Get Breadcrumbs Cache
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function getBreadcrumbsCache()
    {
        if ($this->usePluginCache() === false) {
            return false;
        }

        $cache_key  = $this->getBreadcrumbsCacheKey();
        $cache_item = $this->getPluginCache($cache_key);

        if ($cache_item->isHit() === false) {
            return false;
        }

        $this->plugin_data->{strtolower($this->controller['parameters']->token->model_name)} = $cache_item->getValue();

        return true;
    }

    /**
     * Create and Execute Breadcrumbs Query
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function getBreadcrumbsMenu()
    {
        $this->setBreadcrumbsQuery();

        $this->plugin_data->{strtolower($this->controller['parameters']->token->model_name)} = new stdClass();

        $this->plugin_data->{strtolower($this->controller['parameters']->token->model_name)}->data
            = $this->runQuery();
        $this->plugin_data->{strtolower($this->controller['parameters']->token->model_name)}->model_registry
            = $this->query->getModelRegistry();

        return $this;
    }

    /**
     * Set Query for Menu Breadcrumbs
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setBreadcrumbsQuery()
    {
        $this->setQueryController('Molajo//Model//Datasource//Menuitem.xml');

        $this->setQueryControllerDefaults(
            $process_events = 1,
            $query_object = 'list',
            $get_customfields = 0,
            $use_special_joins = 1,
            $use_pagination = 0,
            $check_view_level_access = 1,
            $get_item_children = 0
        );

        $prefix = $this->query->getModelRegistry('primary_prefix', 'a');

        $this->query->setDistinct(true);
        $this->query->from('#__extension_instances', 'current_menu');

        $this->query->where('column', 'current_menu.id', '=', 'integer', (int)$this->plugin_data->page->menuitem_id);
        $this->query->where('column', 'current_menu.catalog_type_id', '=', 'column', $prefix . '.catalog_type_id');

        $this->query->where('column', $prefix . '.status', '>', 'integer', '0');
        $this->query->where('column', 'current_menu.status', '>', 'integer', '0');

        $this->query->where('column', $prefix . '.lft', '<=', 'column', 'current_menu.lft');
        $this->query->where('column', $prefix . '.rgt', '>=', 'column', 'current_menu.rgt');
        $this->query->where('column', $prefix . '.root', '=', 'column', 'current_menu.root');

        return $this;
    }

    /**
     * Set Breadcrumbs Cache
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setBreadcrumbsCache()
    {
        if ($this->usePluginCache() === false) {
            return $this;
        }

        $cache_key = $this->getBreadcrumbsCacheKey();

        $this->setPluginCache(
            $cache_key,
            $this->plugin_data->{strtolower($this->controller['parameters']->token->model_name)}
        );

        return $this;
    }

    /**
     * Get Breadcrumbs Cache Key
     *
     * @return  string
     * @since   1.0.0
     */
    protected function getBreadcrumbsCacheKey()
    {
        return 'Breadcrumbs-' . (int)$this->plugin_data->page->menuitem_id;
    }
}
