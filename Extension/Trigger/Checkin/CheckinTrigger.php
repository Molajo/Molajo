<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
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
			self::$instance = new CheckinTrigger();
		}
		return self::$instance;
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
		// make certain the correct person is in checkout
		// if so, checkin by zeroing otu that value and the date
		return $data;
	}
}
