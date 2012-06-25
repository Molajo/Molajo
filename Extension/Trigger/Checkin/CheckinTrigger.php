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
}
