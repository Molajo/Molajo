<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Trigger\Dateformats;

use Molajo\Extension\Trigger\Content\ContentTrigger;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Date Formats
 *
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class DateformatsTrigger extends ContentTrigger
{
	/**
	 * Pre-create processing
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onBeforeCreate()
	{
		$fields = $this->retrieveFieldsByType('datetime');

		if (is_array($fields) && count($fields) > 0) {

			foreach ($fields as $field) {

				$name = $field->name;

				/** Retrieves the actual field value from the 'normal' or special field */
				$fieldValue = $this->getFieldValue($field);

				$newFieldValue = '';

				if ($name == 'modified_datetime') {

					$newFieldValue = $this->now;
					$this->saveField($field, $name, $newFieldValue);
					$fieldValue = $newFieldValue;

					$modifiedByField = $this->getField('modified_by');
					$modifiedByValue = $this->getFieldValue($modifiedByField);
					if ($modifiedByValue == false) {
						$modifiedByValue = Services::Registry()->get('User', 'id');
						$this->saveField($modifiedByField, 'modified_by', $modifiedByValue);
					}

				} elseif ($fieldValue == false
					|| $fieldValue == '0000-00-00 00:00:00'
				) {

					if ($name == 'created_datetime') {

						$newFieldValue = $this->now;
						$this->saveField($field, $name, $newFieldValue);
						$fieldValue = $newFieldValue;

						$createdByField = $this->getField('created_by');
						$createdByValue = $this->getFieldValue($createdByField);
						if ($createdByValue == false) {
							$createdByValue = Services::Registry()->get('User', 'id');
							$this->saveField($createdByField, 'created_by', $createdByValue);
						}


					} elseif ($name == 'start_publishing_datetime') {

						$newFieldValue = $this->now;
						$this->saveField($field, $name, $newFieldValue);
						$fieldValue = $newFieldValue;

					} else {

						$newFieldValue = $this->null_date;
						$this->saveField($field, $name, $newFieldValue);
						$fieldValue = $newFieldValue;
					}
				}
			}
		}

		return true;
	}

	/**
	 * After-read processing
	 *
	 * Adds formatted dates to 'normal' or special fields recordset
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onAfterRead()
	{
		$fields = $this->retrieveFieldsByType('datetime');

		if (method_exists('DateService', 'convertCCYYMMDD')) {
		} else {
			return true;
		}

		if (is_array($fields) && count($fields) > 0) {

			foreach ($fields as $field) {

				$name = $field->name;

				/** Retrieves the actual field value from the 'normal' or special field */
				$fieldValue = $this->getFieldValue($field);

				if ($fieldValue == false
					|| $fieldValue == '0000-00-00 00:00:00'
				) {

				} else {

					/** formats the date for CCYYMMDD */
					$newFieldValue = Services::Date()->convertCCYYMMDD($fieldValue);

					if ($newFieldValue == false) {
					} else {

						/** Creates the new 'normal' or special field and populates the value */
						$new_name = $name . '_ccyymmdd';
						$this->saveField($field, $new_name, $newFieldValue);
						$fieldValue = $newFieldValue;
					}

					/** Using newly formatted date, calculate NN days ago */
					$newFieldValue = Services::Date()->differenceDays($fieldValue);

					if ($newFieldValue == false) {
					} else {

						/** Creates the new 'normal' or special field and populates the value */
						$new_name = $name . '_n_days_ago';
						$this->saveField($field, $new_name, $newFieldValue);
					}

					/** Pretty Date */
					$newFieldValue = Services::Date()->prettydate($fieldValue);

					if ($newFieldValue == false) {
					} else {

						/** Creates the new 'normal' or special field and populates the value */
						$new_name = $name . '_pretty_date';
						$this->saveField($field, $new_name, $newFieldValue);
					}
				}
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
