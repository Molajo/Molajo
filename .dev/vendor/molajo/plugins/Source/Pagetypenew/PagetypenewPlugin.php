<?php
/**
 * Page Type New Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Pagetypenew;

use CommonApi\Event\DisplayInterface;
use Molajo\Plugins\DisplayEventPlugin;

/**
 * Page Type New Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
class PagetypenewPlugin extends DisplayEventPlugin implements DisplayInterface
{
    /**
     * Get Data for New Page Type
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeRender()
    {
        if ($this->processPagetypenewPlugin() === false) {
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
    protected function processPagetypenewPlugin()
    {
        if (strtolower($this->runtime_data->route->page_type) === 'new') {
            return true;
        }

        return false;
    }

    /**
     * Process Plugin
     *
     * @return  PagetypenewPlugin
     * @since   1.0.0
     */
    protected function processPlugin()
    {
        return $this;
    }
}
