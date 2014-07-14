<?php
/**
 * Page Type Application Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Pagetypeapplication;

use CommonApi\Event\DisplayInterface;
use Molajo\Plugins\DisplayEventPlugin;

/**
 * Page Type Application Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
class PagetypeapplicationPlugin extends DisplayEventPlugin implements DisplayInterface
{
    /**
     * Get Data for Application Page Type
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeRender()
    {
        if ($this->processPagetypeapplicationPlugin() === false) {
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
    protected function processPagetypeapplicationPlugin()
    {
        if (strtolower($this->runtime_data->route->page_type) === 'application') {
            return true;
        }

        return false;
    }

    /**
     * Process Plugin
     *
     * @return  PagetypeapplicationPlugin
     * @since   1.0.0
     */
    protected function processPlugin()
    {
        return $this;
    }
}
