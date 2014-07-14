<?php
/**
 * Page Type Item Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Pagetypeitem;

use CommonApi\Event\DisplayInterface;
use Molajo\Plugins\DisplayEventPlugin;

/**
 * Page Type Item Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
class PagetypeitemPlugin extends DisplayEventPlugin implements DisplayInterface
{
    /**
     * Get Data for Item Page Type
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeRender()
    {
        if ($this->processPagetypeitemPlugin() === false) {
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
    protected function processPagetypeitemPlugin()
    {
        $page_types = array('item', 'edit', 'form');

        if (in_array(strtolower($this->runtime_data->route->page_type), $page_types)) {
            return true;
        }

        return false;
    }

    /**
     * Process Plugin
     *
     * @return  PagetypeitemPlugin
     * @since   1.0.0
     */
    protected function processPlugin()
    {
        return $this;
    }
}
