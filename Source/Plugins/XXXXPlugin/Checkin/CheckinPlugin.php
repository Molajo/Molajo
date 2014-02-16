<?php
/**
 * Checkin Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Checkin;

use CommonApi\Event\UpdateInterface;
use Molajo\Plugins\UpdateEventPlugin;

/**
 * Checkin Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
class CheckinPlugin extends UpdateEventPlugin implements UpdateInterface
{
    /**
     * After-update processing
     *
     * @return  $this
     * @since   1.0
     */
    public function onAfterUpdate()
    {
        // make certain the correct person is in checkout
        // if so, checkin by zeroing otu that value and the date
        return $this;
    }

    /**
     * Method to check in an item after processing
     *
     * @return  $this
     * @since   1.0
     */
    public function checkInItem()
    {
        if ($this->get('id') == 0) {
            return $this;
        }

        if (property_exists($this->model, 'checked_out')) {
        } else {
            return $this;
        }

        $results = $this->model->checkin($this->get('id'));

        if ($results === false) {
            // redirect
        }

        return $this;
    }
}
