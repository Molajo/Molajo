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
			self::$instance = new DateformatsTrigger();
		}
		return self::$instance;
	}

	/**
	 * After-read processing
	 *
	 * Adds formatted dates to $this->query_results
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	public function onAfterRead()
	{

		if (isset($this->query_results->created_datetime)) {
			if ($this->query_results->created_datetime == '0000-00-00 00:00:00') {
			} else {
				$this->itemDateRoutine('created_datetime');
			}
		}

		if (isset($this->query_results->modified_datetime)) {
			if ($this->query_results->modified_datetime == '0000-00-00 00:00:00') {
			} else {
				$this->itemDateRoutine('modified_datetime');
			}
		}

		if (isset($this->query_results->start_publishing_datetime)) {
			if ($this->query_results->start_publishing_datetime == '0000-00-00 00:00:00') {
			} else {
				$this->itemDateRoutine('start_publishing_datetime');
			}
		}

		if (isset($this->query_results->stop_publishing_datetime)) {
			if ($this->query_results->stop_publishing_datetime == '0000-00-00 00:00:00') {
			} else {
				$this->itemDateRoutine('stop_publishing_datetime');
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
	 * @param $this->query_results
	 *
	 * @return array
	 * @since 1.0
	 */
	protected function itemDateRoutine($field)
	{
		return false;

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
