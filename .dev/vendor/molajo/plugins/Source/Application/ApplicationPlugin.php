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
     * Page Type
     *
     * @var    string
     * @since  1.0.0
     */
    protected $page_type;

    /**
     * Prepares Page Information storing results in $this->plugin_data->page
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeExecute()
    {
        if ($this->processApplicationPlugin() === false) {
            return $this;
        }

        $this->setCurrentMenuitemId();

        $this->getUrls();

        $this->processMenus();
        echo 'getUrls';
        die;
        $this->getPageTitle();

        $this->setPageMeta();

        return $this;
    }

    /**
     * Should plugin be executed?
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function processApplicationPlugin()
    {
        if ($this->runtime_data->request->client->ajax === 1) {
            return false;
        }

        $this->page_type = $this->setPageType();
        if ($this->page_type === 'dashboard') {
            return false;
        }

        return true;
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
