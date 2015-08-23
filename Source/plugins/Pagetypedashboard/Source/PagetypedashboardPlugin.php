<?php
/**
 * Page Type Dashboard Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Pagetypedashboard;

use CommonApi\Event\DisplayEventInterface;
use Molajo\Plugins\DisplayEvent;

/**
 * Page Type Dashboard Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
final class PagetypedashboardPlugin extends DisplayEvent implements DisplayEventInterface
{
    /**
     * Get Data for Dashboard Page Type
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeRender()
    {
        if ($this->checkProcessPlugin() === false) {
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
    protected function checkProcessPlugin()
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
        $this->setPluginDataFormBeginValues('PUT', strtolower($this->runtime_data->route->page_type));
        return $this;
    }
}
