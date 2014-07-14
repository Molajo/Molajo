<?php
/**
 * Page Type Edit Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Pagetypeedit;

use CommonApi\Event\DisplayInterface;
use Molajo\Plugins\DisplayEventPlugin;

/**
 * Page Type Edit Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
class PagetypeeditPlugin extends DisplayEventPlugin implements DisplayInterface
{
    /**
     * Get Data for Edit Page Type
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeRender()
    {
        if ($this->processPagetypeeditPlugin() === false) {
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
    protected function processPagetypeeditPlugin()
    {
        if (strtolower($this->runtime_data->route->page_type) === 'edit') {
            return true;
        }

        return false;
    }

    /**
     * Process Plugin
     *
     * @return  PagetypeeditPlugin
     * @since   1.0.0
     */
    protected function processPlugin()
    {
        return $this;
    }
}
