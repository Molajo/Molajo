<?php
/**
 * Page Type Dashboard Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Pagetypedashboard;

use CommonApi\Event\DisplayInterface;
use Molajo\Plugins\DisplayEventPlugin;

/**
 * Page Type Dashboard Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
class PagetypedashboardPlugin extends DisplayEventPlugin implements DisplayInterface
{
    /**
     * Get Data for Dashboard Page Type
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeRender()
    {
        if ($this->processPagetypedashboardPlugin() === false) {
            return $this;
        }

        return $this->processPlugin();
    }

    /**
     * Should plugin be executed?
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function processPagetypedashboardPlugin()
    {
        if (strtolower($this->runtime_data->route->page_type) === 'dashboard') {
            return true;
        }

        return false;
    }

    /**
     * Process Plugin
     *
     * @return  PagetypedashboardPlugin
     * @since   1.0.0
     */
    protected function processPlugin()
    {
        return $this;
    }
}
