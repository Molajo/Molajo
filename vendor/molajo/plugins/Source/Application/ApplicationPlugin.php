<?php
/**
 * Application Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Application;

use CommonApi\Event\SystemInterface;
use CommonApi\Exception\RuntimeException;
use stdClass;

/**
 * Application Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
class ApplicationPlugin extends ApplicationMetadata implements SystemInterface
{
    /**
     * Prepares Page Information storing results in $this->plugin_data->page
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeExecute()
    {
        $results = $this->processApplicationPlugin();
        if ($results === false) {
            return $this;
        }

        $this->setCurrentMenuitemId($results);

        $this->getUrls();

        $this->processMenus();

        $this->getPageTitle();

        $this->setPageMeta();

        return $this;
    }

    /**
     * Should plugin be executed?
     *
     * @return  string
     * @since   1.0.0
     */
    protected function processApplicationPlugin()
    {
        if ($this->runtime_data->request->client->ajax === 1) {
            return false;
        }

        $page_type = $this->setPageType();
        if ($page_type === 'dashboard') {
            return false;
        }

        return $page_type;
    }

    /**
     * Set Page Type
     *
     * @return  string
     * @since   1.0.0
     */
    protected function setPageType()
    {
        return strtolower($this->runtime_data->route->page_type);
    }

    /**
     * Build the home and page url to be used in links
     *
     * @return  $this
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getUrls()
    {
        $this->plugin_data->page->urls['home']      = $this->runtime_data->application->base_url;
        $this->plugin_data->page->urls['base']      = $this->runtime_data->application->base_url;
        $this->plugin_data->page->urls['page']      = $this->runtime_data->request->data->url;
        $this->plugin_data->page->urls['canonical'] = $this->runtime_data->request->data->url;
        $this->plugin_data->page->urls['resource']  = $this->runtime_data->application->base_url
            . strtolower($this->runtime_data->route->b_alias);

        //@todo add links for prev and next
        return $this;
    }

    /**
     * Process Breadcrumbs, Menu and Navbar menu
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function processMenus()
    {
        if ((int)$this->runtime_data->resource->menuitem->data->id === 0) {
            $this->plugin_data->page->breadcrumbs = new stdClass();

        } else {
            $this->plugin_data->page->breadcrumbs = $this->getMenuBreadcrumbIds();
            $this->getMenu();
            $this->getNavbarMenu();
        }

        return $this;
    }
}
