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
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
class PagetypenewPlugin extends DisplayEventPlugin implements DisplayInterface
{
    /**
     * Prepares Configuration Data
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeRender()
    {
        if (strtolower($this->runtime_data->route->page_type) === 'new') {
        } else {
            return $this;
        }

    }
}
