<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Trigger;

defined('MOLAJO') or die;

/**
 * Item Author
 *
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class ItemDateformatsTrigger extends ContentTrigger
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
			self::$instance = new ItemDateformatsTrigger();
		}
		return self::$instance;
	}

	/**
     * After-read processing
	 *
	 * Adds formatted dates to $data
     *
     * @param   $data
     * @param   $model
     *
     * @return  $data
     * @since   1.0
     */
    public function onAfterRead($data, $model)
    {
		if (isset($data->created_by)
			&& (int) $data->created_by > 0) {
		} else {
			return;
		}

		if (isset($data->created_datetime)) {
			if ($data->created_datetime == '0000-00-00 00:00:00') {
			} else {
				$data = $this->itemDateRoutine('created_datetime', $data);
			}
		}

		if (isset($data->modified_datetime)) {
			if ($data->modified_datetime == '0000-00-00 00:00:00') {
			} else {
				$data = $this->itemDateRoutine('modified_datetime', $data);
			}
		}

		if (isset($data->start_publishing_datetime)) {
			if ($data->start_publishing_datetime == '0000-00-00 00:00:00') {
			} else {
				$data = $this->start_publishing_datetime('created_datetime', $data);
			}
		}

		if (isset($data->stop_publishing_datetime)) {
			if ($data->stop_publishing_datetime == '0000-00-00 00:00:00') {
			} else {
				$data = $this->itemDateRoutine('stop_publishing_datetime', $data);
			}
		}

		return;
    }

	/**
	 * itemDateRoutine
	 *
	 * Creates formatted date fields based on a named field
	 *
	 * @param $field
	 * @param $data
	 *
	 * @return array
	 * @since 1.0
	 */
	protected function itemDateRoutine($field, $data)
	{
		if ($data->$field == '0000-00-00 00:00:00') {
			return $data;
		}

		$newField = $field . '_ccyymmdd';
		$data->$newField = Services::Date()->convertCCYYMMDD($data->$field);

		$data->$newField = str_replace('-', '', $data->$newField);

		$newField = $field . '_n_days_ago';
		$data->$newField = Services::Date()->differenceDays(date('Y-m-d'), $data->$field);

		$newField = $field . '_pretty_date';
		$data->$newField = Services::Date()->prettydate($data->$field);

		return $data;
	}
}
