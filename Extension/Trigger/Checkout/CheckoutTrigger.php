<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Trigger\Checkout;

use Molajo\Extension\Trigger\Content\ContentTrigger;

defined('MOLAJO') or die;
/**
 * Checkout
 *
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class CheckoutTrigger extends ContentTrigger
{
	/**
	 * Static instance
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected static $instance;

	/**
	 * getInstance
	 *
	 * @static
	 * @return bool|object
	 * @since  1.0
	 */
	public static function getInstance()
	{
		if (empty(self::$instance)) {
			self::$instance = new CheckoutTrigger();
		}
		return self::$instance;
	}

    /**
     * Pre-read processing
     *
     * @param   $data
     * @param   $model
     *
     * @return  $data
     * @since   1.0
     */
    public function onBeforeRead($data, $model)
    {
		// dirty read?
        return $data;
    }

    /**
     * Pre-update processing
     *
     * @param   $data
     * @param   $model
     *
     * @return  $data
     * @since   1.0
     */
    public function onBeforeUpdate($data, $model)
    {
		// verify user has rights to update
		// and that no one else has it updated
		// if so, check checkout date and user
        return $data;
    }

    /**
     * Pre-delete processing
     *
     * @param   $data
     * @param   $model
     *
     * @return  $data
     * @since   1.0
     */
    public function onBeforeDelete($data, $model)
    {
		// verify user has rights to delete
		// and that no one else has it checked out
		// if so, allow, else cancel
        return $data;
    }
}
