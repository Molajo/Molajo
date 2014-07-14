<?php
/**
 * Page Type List Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Pagetypelist;

use CommonApi\Event\DisplayInterface;
use Molajo\Plugins\DisplayEventPlugin;

/**
 * Page Type List Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
class PagetypelistPlugin extends DisplayEventPlugin implements DisplayInterface
{
    /**
     * Get Data for List Page Type
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeRender()
    {
        if ($this->processPagetypelistPlugin() === false) {
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
    protected function processPagetypelistPlugin()
    {
        if (strtolower($this->runtime_data->route->page_type) === 'list') {
            return true;
        }

        return false;
    }

    /**
     * Process Plugin
     *
     * @return  PagetypelistPlugin
     * @since   1.0.0
     */
    protected function processPlugin()
    {
        return $this;
    }
}
