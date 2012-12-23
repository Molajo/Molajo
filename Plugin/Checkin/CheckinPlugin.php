<?php
/**
 * @package    Niambie
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    MIT
 */
namespace Molajo\Plugin\Checkin;

use Molajo\Plugin\Plugin\Plugin;

defined('NIAMBIE') or die;

/**
 * Checkin
 *
 * @package     Niambie
 * @license     MIT
 * @since       1.0
 */
class CheckinPlugin extends Plugin
{

    /**
     * After-update processing
     *
     * @param   $this->row
     * @param   $model
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterUpdate()
    {
        // make certain the correct person is in checkout
        // if so, checkin by zeroing otu that value and the date
        return true;
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
