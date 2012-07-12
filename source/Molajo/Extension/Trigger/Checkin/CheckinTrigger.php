<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Extension\Trigger\Checkin;

use Molajo\Extension\Trigger\Content\ContentTrigger;

defined('MOLAJO') or die;

/**
 * Checkin
 *
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class CheckinTrigger extends ContentTrigger
{

    /**
     * After-update processing
     *
     * @param   $this->data
     * @param   $model
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterUpdate()
    {
        // make certain the correct person is in checkout
        // if so, checkin by zeroing otu that value and the date
        return false;
    }

    /**
     * checkinItem
     *
     * Method to check in an item after processing
     *
     * @return bool
     */
    public function checkinItem()
    {
        if ($this->get('id') == 0) {
            return true;
        }

        if (property_exists($this->model, 'checked_out')) {
        } else {
            return true;
        }

        $results = $this->model->checkin($this->get('id'));

        if ($results === false) {
            // redirect
        }

        return true;
    }
}
