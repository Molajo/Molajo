<?php
/**
 * Checkout Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Checkout;

use Molajo\Plugins\AbstractPlugin;

/**
 * Checkout Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
class CheckoutPlugin extends AbstractPlugin
{
    /**
     * Pre-update processing
     *
     * @return  $this
     * @since   1.0
     */
    public function onBeforeUpdate()
    {
        // verify user has rights to update
        // and that no one else has it updated
        // if so, check checkout date and user
        return $this;
    }

    /**
     * Pre-delete processing
     *
     * @return  $this
     * @since   1.0
     */
    public function onBeforeDelete()
    {
        // verify user has rights to delete
        // and that no one else has it checked out
        // if so, allow, else cancel
        return $this;
    }

    /**
     * Checks that the current user is the checked_out user for item
     *
     * @return  $this
     * @since   1.0
     */
    public function verifyCheckout()
    {
        if ($this->get('id') == 0) {
            return $this;
        }

        if (property_exists($this->model, 'checked_out')) {
        } else {
            return $this;
        }
// or super admin
        if ($this->model->checked_out == $this->runtime_data->user->get('id')) {
        } else {
            // redirect error
            return $this;
        }

        return $this;
    }

    /**
     * Method to set the checkout_time and checked_out values of the item
     *
     * @return  $this
     * @since   1.0
     */
    public function checkoutItem()
    {
        if ($this->get('id') == 0) {
            return $this;
        }

        if (property_exists($this->model, 'checked_out')) {
        } else {
            return $this;
        }

        $results = $this->model->checkout($this->get('id'));
        if ($results === false) {
            // redirect error
            return $this;
        }

        return $this;
    }
}
