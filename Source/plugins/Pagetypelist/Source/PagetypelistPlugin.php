<?php
/**
 * Page Type List Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Pagetypelist;

use CommonApi\Event\DisplayEventInterface;
use Molajo\Plugins\DisplayEvent;

/**
 * Page Type List Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
class PagetypelistPlugin extends DisplayEvent implements DisplayEventInterface
{
    /**
     * Get Data for List Page Type
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
