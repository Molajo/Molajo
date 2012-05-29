<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Trigger\Fullname;

use Molajo\Extension\Trigger\Content\ContentTrigger;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Full name
 *
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class FullnameTrigger extends ContentTrigger
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
			self::$instance = new FullnameTrigger();
		}
		return self::$instance;
	}

	/**
	 * After-read processing
	 *
	 * Adds formatted dates to 'normal' or special fields recordset
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	public function onAfterRead()
	{

		$name = 'Fullname';

		/** Retrieves the actual field value */
		$fieldValue1 = $this->getFieldValue('first_name');
		$fieldValue2 = $this->getFieldValue('last_name');

		if ($fieldValue1 == false && $fieldValue2 == false) {

		} else {

			/** Concatenate first and last name */
			$newFieldValue = $fieldValue1 . ' ' . $fieldValue2;

			if ($newFieldValue == false) {
			} else {

				/** Creates the new 'normal' or special field and populates the value */
				$this->addField('last_name', 'fullname', $newFieldValue);
			}
		}

		return true;
	}

	/**
	 * itemDateRoutine
	 *
	 * Creates formatted date fields based on a named field
	 *
	 * @param $field
	 * @param $this->query_results
	 *
	 * @return array
	 * @since 1.0
	 */
	protected function itemDateRoutine($field)
	{
		return false;
	}
}
