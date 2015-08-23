<?php
/**
 * Checkout Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Checkout;

use CommonApi\Event\UpdateEventInterface;
use Molajo\Plugins\UpdateEvent;

/**
 * Checkout Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
final class CheckoutPlugin extends UpdateEvent implements UpdateEventInterface
{
    /**
     * Fires after read for each row
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterUpdate()
    {
        if ($this->checkProcessPlugin() === false) {
            return $this;
        }

        return $this;
    }

    /**
     * Should plugin be executed?
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function checkProcessPlugin()
    {
        return false;
    }
}
