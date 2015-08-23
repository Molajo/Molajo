<?php
/**
 * Checkin Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Checkin;

use CommonApi\Event\UpdateEventInterface;
use Molajo\Plugins\UpdateEvent;

/**
 * Checkin Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
final class CheckinPlugin extends UpdateEvent implements UpdateEventInterface
{
    /**
     * Fires after read for each row
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeUpdate()
    {
        $this->controller['row']->checked_out_datetime = null;
        $this->controller['row']->checked_out_by       = null;

        return $this;
    }
}
