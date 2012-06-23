<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Trigger\Smilies;

use Molajo\Extension\Trigger\Content\ContentTrigger;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class SmiliesTrigger extends ContentTrigger
{

	/**
	 * After-read processing
	 *
	 * Replaces text with emotion images
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onAfterRead()
	{
		$fields = $this->retrieveFieldsByType('text');

		if (is_array($fields) && count($fields) > 0) {

			/** @noinspection PhpWrongForeachArgumentTypeInspection */
			foreach ($fields as $field) {

				$name = $field->name;

				/** Retrieves the actual field value from the 'normal' or special field */
				$fieldValue = $this->getFieldValue($field);

				if ($fieldValue == false) {
				} else {

					$value = Services::Text()->smilies($fieldValue);

					if ($value == false) {
					} else {
						/** Creates the new 'normal' or special field and populates the value */
						$fieldValue = $this->saveField($field, $name, $value);
					}
				}

			}
		}

		return true;
	}
}
