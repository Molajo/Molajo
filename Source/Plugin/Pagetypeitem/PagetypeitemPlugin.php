<?php
/**
 * Page Type Item Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugin\Pagetypeitem;

use CommonApi\Event\DisplayInterface;
use Molajo\Plugin\DisplayEventPlugin;

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
     * Switches the model registry for an item since the Content Query already retrieved the data
     *  and saved it into the registry
     *
     * @return  $this
     * @since   1.0
     */
    public function onBeforeParse()
    {
        if (strtolower($this->runtime_data->route->page_type) == 'item') {
        } else {
            return $this;
        }

        return $this;
    }
}
