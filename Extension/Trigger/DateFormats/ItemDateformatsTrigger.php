<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Trigger\DateFormats;

use Molajo\Extension\Trigger\Content\ContentTrigger;

defined('MOLAJO') or die;

/**
 * Date Formats
 *
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class ItemDateFormatsTrigger extends ContentTrigger
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
			self::$instance = new ItemDateFormatsTrigger();
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
	 * @return  boolean
	 * @since   1.0
	 */
	public function onAfterRead()
	{
		if (isset($this->query_results->created_by)
			&& (int)$this->query_results->created_by > 0
		) {
		} else {
			return;
		}

		if (isset($this->query_results->created_datetime)) {
			if ($this->query_results->created_datetime == '0000-00-00 00:00:00') {
			} else {
				$data = $this->itemDateRoutine('created_datetime', $data);
			}
		}

		if (isset($this->query_results->modified_datetime)) {
			if ($this->query_results->modified_datetime == '0000-00-00 00:00:00') {
			} else {
				$data = $this->itemDateRoutine('modified_datetime', $data);
			}
		}

		if (isset($this->query_results->start_publishing_datetime)) {
			if ($this->query_results->start_publishing_datetime == '0000-00-00 00:00:00') {
			} else {
				$data = $this->start_publishing_datetime('created_datetime', $data);
			}
		}

		if (isset($this->query_results->stop_publishing_datetime)) {
			if ($this->query_results->stop_publishing_datetime == '0000-00-00 00:00:00') {
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
		if ($this->query_results->$field == '0000-00-00 00:00:00') {
			return false;
		}

		$newField = $field . '_ccyymmdd';
		$this->query_results->$newField = Services::Date()->convertCCYYMMDD($this->query_results->$field);

		$this->query_results->$newField = str_replace('-', '', $this->query_results->$newField);

		$newField = $field . '_n_days_ago';
		$this->query_results->$newField = Services::Date()->differenceDays(date('Y-m-d'), $this->query_results->$field);

		$newField = $field . '_pretty_date';
		$this->query_results->$newField = Services::Date()->prettydate($this->query_results->$field);

		return false;
	}
}
