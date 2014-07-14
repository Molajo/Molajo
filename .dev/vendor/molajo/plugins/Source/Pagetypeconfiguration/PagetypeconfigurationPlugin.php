<?php
/**
 * Page Type Configuration Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Pagetypeconfiguration;

use CommonApi\Event\DisplayInterface;
use Molajo\Plugins\DisplayEventPlugin;

/**
 * Page Type Configuration Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
class PagetypeconfigurationPlugin extends DisplayEventPlugin implements DisplayInterface
{
    /**
     * Get Data for Configuration Page Type
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeRender()
    {
        if ($this->processPagetypeconfigurationPlugin() === false) {
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
    protected function processPagetypeconfigurationPlugin()
    {
        if (strtolower($this->runtime_data->route->page_type) === 'configuration') {
            return true;
        }

        return false;
    }

    /**
     * Process Plugin
     *
     * @return  PagetypeconfigurationPlugin
     * @since   1.0.0
     */
    protected function processPlugin()
    {
        return $this;
    }
}
