<?php
/**
 * @package   Molajo
 * @copyright Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Service\Services;

use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Date
 *
 * @package   Molajo
 * @subpackage  Service
 * @since           1.0
 */
Class DateService
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
			self::$instance = new DateService();
		}
		return self::$instance;
	}

	/**
	 * __construct
	 *
	 * Class constructor.
	 *
	 * @return boolean
	 * @since  1.0
	 */
	protected function __construct()
	{
		return $this->getDate();
	}

	/**
	 * getDate
	 *
	 * Return the Date object
	 *
	 * @param   mixed  $time     The initial time for the JDate object
	 * @param   mixed  $tzOffset The timezone offset.
	 *
	 * @return JDate object
	 * @since   1.0
	 */
	public function getDate($time = 'now', $tzOffset = null)
	{
		if ($time == '') {
			$time = 'now';
		}
		$locale = Services::Language()->get('tag', 'en-GB');

		$class = str_replace('-', '_', $locale) . 'Date';
		if (class_exists($class)) {
		} else {
			$class = 'Joomla\\date\\JDate';
		}

		return new $class($time, $tzOffset);
	}

	/**
	 * convertCCYYMMDD
	 *
	 * @param date $date
	 * @return string CCYY-MM-DD
	 */
	public function convertCCYYMMDD($date = null)
	{
		if (strlen($date) == 8) {
			return substr($date, 0, 4) .
				'-' . substr($date, 5, 2) .
				'-' . substr($date, 8, 2);
		} else {
			return false;
		}
	}

	/**
	 * differenceDays
	 *
	 * @param string $date1 expressed as CCYY-MM-DD
	 * @param string $date2 expressed as CCYY-MM-DD
	 *
	 * @since 1.0
	 * @return integer
	 */
	public function differenceDays($date1, $date2)
	{
		$day_number1mm = substr($date1, 5, 2);
		$day_number1dd = substr($date1, 8, 2);
		$day_number1ccyy = substr($date1, 0, 4);
		$gregdate1 = gregoriantojd($day_number1mm, $day_number1dd, $day_number1ccyy);

		$day_number2mm = substr($date2, 5, 2);
		$day_number2dd = substr($date2, 8, 2);
		$day_number2ccyy = substr($date2, 0, 4);
		$gregdate2 = gregoriantojd($day_number2mm, $day_number2dd, $day_number2ccyy);

		return $gregdate1 - $gregdate2;
	}

	/**
	 * prettydate
	 *
	 * @param  $date
	 *
	 * @return string human-readable pretty date
	 * @since  1.0
	 */
	public function prettydate($source_date)
	{
		/** user time zone */
		$source_date = $this->getUTCDate(
			$source_date, 'system'
		);
		$current_date = $this->getUTCDate(
			date('m/d/Y h:i:s a', time()), 'system'
		);

		$source_date = strtotime($source_date);
		$current_date = strtotime($current_date);

		/** verify dates */
		if ($source_date === false
			|| $source_date < 0
			|| $source_date > $current_date
		) {
			return false;
		}

		/** difference in years */
		$years = date('Y', $current_date) - date('Y', $source_date);

		/** difference in months */
		$endMonth = date('m', $current_date);
		$startMonth = date('m', $source_date);
		$months = $endMonth - $startMonth;
		if ($months < 0) {
			$months = $months + 12;
			$years = $years - 1;
		}

		/** difference in days */
		$remove_years_months = array();
		if ($years > 0) {
			$remove_years_months[] = $years . (($years == 1) ? ' year' : ' years');
		}
		if ($months > 0) {
			$remove_years_months[] = $months . (($months == 1) ? ' month' : ' months');
		}
		$remove_years_months = count($remove_years_months) > 0 ? '+' . implode(' ', $remove_years_months) : 'now';

		$day_numbers = $current_date - strtotime($remove_years_months, $source_date);
		$day_numbers = date('z', $day_numbers);

		/** only calculate hours, minutes and seconds for current date */
		$hours = 0;
		$minutes = 0;
		$seconds = 0;
		if ($years == 0 && $months == 0 && $day_numbers == 0) {
			$seconds = date('s', $current_date) - date('s', $source_date);

			/** difference in hours */
			$hours = round($seconds / (60 * 60), 0);
			if ($hours > 0) {
				$seconds = $seconds - round($seconds / (60 * 60), 0);
			}

			/** difference in minutes */
			$minutes = 0;
			if ($seconds > 0) {
				$minutes = round($seconds / 60, 0);

				/** difference in seconds */
				if ($minutes > 0) {
					$seconds = $seconds - round($seconds / 60, 0);
				}
			}
		}

		/** no date differences */
		if ($years == 0 && $months == 0 && $day_numbers == 0 && $hours == 0 && $minutes == 0 && $seconds == 0) {
			return '';
		}

		/** format pretty date */
		$prettyDate = $this->prettyDateFormat($years, 'DATE_YEAR_SINGULAR', 'DATE_YEAR_PLURAL');
		$prettyDate .= $this->prettyDateFormat($months, 'DATE_MONTH_SINGULAR', 'DATE_MONTH_PLURAL');
		$prettyDate .= $this->prettyDateFormat($day_numbers, 'DATE_DAY_SINGULAR', 'DATE_DAY_PLURAL');
		$prettyDate .= $this->prettyDateFormat($hours, 'DATE_HOUR_SINGULAR', 'DATE_HOUR_PLURAL');
		$prettyDate .= $this->prettyDateFormat($minutes, 'DATE_MINUTE_SINGULAR', 'DATE_MINUTE_PLURAL');
		$prettyDate .= $this->prettyDateFormat($seconds, 'DATE_SECOND_SINGULAR', 'DATE_SECOND_PLURAL');

		/** remove leading comma */
		return trim(substr($prettyDate, 1, strlen($prettyDate) - 1));
	}

	/**
	 * prettyDateFormat
	 *
	 * @param  $numeric_value
	 * @param  $singular_literal
	 * @param  $plural_literal
	 *
	 * @return mixed
	 * @since  1.0
	 */
	function prettyDateFormat($numeric_value, $singular_literal, $plural_literal)
	{
		if ($numeric_value == 0) {
			return false;
		}

		if ($numeric_value == 1) {
			return ', ' . $numeric_value . ' ' .
				strtolower(Services::Language()->translate($singular_literal));
		}

		return ', ' . $numeric_value . ' ' .
			strtolower(Services::Language()->translate($plural_literal));
	}

	/**
	 * getDayName
	 *
	 * Provides translated name of day in abbreviated or full format, given day number
	 *
	 * @param $day_number
	 * @param bool $abbreviation
	 *
	 * @return string
	 * @since  1.0
	 */
	public function getDayName($day_number, $abbreviation = false)
	{
		switch ($day_number) {
			case 1:
				if ($abbreviation === true) {
					Services::Language()->translate('DATE_MON');
				} else {
					Services::Language()->translate('DATE_MONDAY');
				}
			case 2:
				if ($abbreviation === true) {
					Services::Language()->translate('DATE_TUE');
				} else {
					Services::Language()->translate('DATE_TUESDAY');
				}
			case 3:
				if ($abbreviation === true) {
					Services::Language()->translate('DATE_WED');
				} else {
					Services::Language()->translate('DATE_WEDNESDAY');
				}
			case 4:
				if ($abbreviation === true) {
					Services::Language()->translate('DATE_THU');
				} else {
					Services::Language()->translate('DATE_THURSDAY');
				}
			case 5:
				if ($abbreviation === true) {
					Services::Language()->translate('DATE_FRI');
				} else {
					Services::Language()->translate('DATE_FRIDAY');
				}
			case 6:
				if ($abbreviation === true) {
					Services::Language()->translate('DATE_SAT');
				} else {
					Services::Language()->translate('DATE_SATURDAY');
				}
			default:
				if ($abbreviation === true) {
					Services::Language()->translate('DATE_SUN');
				} else {
					Services::Language()->translate('DATE_SUNDAY');
				}
		}
	}

	/**
	 * getMonthName
	 *
	 * Provides translated name of month in abbreviated or full format, given month number
	 *
	 * @param  string  $month_number
	 * @param  bool    $abbreviation
	 *
	 * @return string
	 * @since  1.0
	 */
	public function getMonthName($month_number, $abbreviation = false)
	{
		switch ($month_number) {
			case 1:
				if ($abbreviation === true) {
					Services::Language()->translate('DATE_JAN');
				} else {
					Services::Language()->translate('DATE_JANUARY');
				}
			case 2:
				if ($abbreviation === true) {
					Services::Language()->translate('DATE_FEB');
				} else {
					Services::Language()->translate('DATE_FEBRUARY');
				}
			case 3:
				if ($abbreviation === true) {
					Services::Language()->translate('DATE_MAR');
				} else {
					Services::Language()->translate('DATE_MARCH');
				}
			case 4:
				if ($abbreviation === true) {
					Services::Language()->translate('DATE_APR');
				} else {
					Services::Language()->translate('DATE_APRIL');
				}
			case 5:
				if ($abbreviation === true) {
					Services::Language()->translate('DATE_MAY');
				} else {
					Services::Language()->translate('DATE_MAY');
				}
			case 6:
				if ($abbreviation === true) {
					Services::Language()->translate('DATE_JUN');
				} else {
					Services::Language()->translate('DATE_JUNE');
				}
			case 7:
				if ($abbreviation === true) {
					Services::Language()->translate('DATE_JUL');
				} else {
					Services::Language()->translate('DATE_JULY');
				}
			case 8:
				if ($abbreviation === true) {
					Services::Language()->translate('DATE_AUG');
				} else {
					Services::Language()->translate('DATE_AUGUST');
				}
			case 9:
				if ($abbreviation === true) {
					Services::Language()->translate('DATE_SEP');
				} else {
					Services::Language()->translate('DATE_SEPTEMBER');
				}
			case 10:
				if ($abbreviation === true) {
					Services::Language()->translate('DATE_OCT');
				} else {
					Services::Language()->translate('DATE_OCTOBER');
				}
			case 11:
				if ($abbreviation === true) {
					Services::Language()->translate('DATE_NOV');
				} else {
					Services::Language()->translate('DATE_NOVEMBER');
				}
			default:
				if ($abbreviation === true) {
					Services::Language()->translate('DATE_DECEMBER');
				} else {
					Services::Language()->translate('DATE_DECEMBER');
				}
		}
	}

	/**
	 * buildCalendar
	 *
	 *
	 * $d = getdate();
	 * $month = $d['mon'];
	 * $year = $d['year'];
	 * $calendar = Services::Date()->buildCalendar ($month, $year);
	 *
	 * @param    string $month
	 * @param    string $year
	 *
	 * @return    string CCYY-MM-DD
	 * @since   1.0
	 */
//todo: Amy - redo to generate a set of dates in a model, combine with other data, pass to a view for rendering

	function buildCalendar($month, $year)
	{
		$day_numbersOfWeek = array('S', 'M', 'T', 'W', 'T', 'F', 'S');
		$firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
		$numberDays = date('t', $firstDayOfMonth);
		$dateComponents = getdate($firstDayOfMonth);
		$monthName = $dateComponents['month'];
		$day_numberOfWeek = $dateComponents['wday'];

		$calendar = "<table class='calendar'>";
		$calendar .= "<caption>$monthName $year</caption>";
		$calendar .= "<tr>";
		foreach ($day_numbersOfWeek as $day_number) {
			$calendar .= "<th class='header'>$day_number</th>";
		}

		$currentDay = 1;
		$calendar .= "</tr><tr>";
		if ($day_numberOfWeek > 0) {
			$calendar .= "<td colspan='$day_numberOfWeek'>&nbsp;</td>";
		}

		$month = str_pad($month, 2, "0", STR_PAD_LEFT);
		while ($currentDay <= $numberDays) {
			if ($day_numberOfWeek == 7) {
				$day_numberOfWeek = 0;
				$calendar .= "</tr><tr>";
			}
			$currentDayRel = str_pad($currentDay, 2, "0", STR_PAD_LEFT);
			$date = "$year-$month-$currentDayRel";
			$calendar .= "<td class='day' rel='$date'>$currentDay</td>";
			$currentDay++;
			$day_numberOfWeek++;
		}

		if ($day_numberOfWeek != 7) {
			$remainingDays = 7 - $day_numberOfWeek;
			$calendar .= "<td colspan='$remainingDays'>&nbsp;</td>";
		}

		$calendar .= "</tr>";
		$calendar .= "</table>";

		return $calendar;
	}

	/**
	 * getUTCDate
	 *
	 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
	 * @license     GNU General Public License version 2 or later; see LICENSE
	 *
	 * @param $input_date
	 * @param string $server_or_user_UTC
	 *
	 * @return string
	 * @since  1.0
	 */
	function getUTCDate($input_date, $server_or_user_UTC = 'user')
	{
		//todo: amy fix user

		$server_or_user_UTC = 'SERVER_UTC';
		$config = Services::Registry()->get('Configuration');
		$user = Services::User();

		// If a known filter is given use it.
		switch (strtoupper((string)$server_or_user_UTC)) {
			case 'SERVER_UTC':
				// Convert a date to UTC based on the server timezone.
				if (intval($input_date)) {
					// Get a date object based on the correct timezone.
					$date = $this->getDate($input_date, 'UTC');
					$date->setTimezone(new \DateTimeZone($config->get('offset')));

					// Transform the date string.
					return $date->toSql(true);
				}
				break;

			default:
				// Convert a date to UTC based on the user timezone.
				if (intval($input_date)) {
					// Get a date object based on the correct timezone.
					$date = $this->getDate($input_date, 'UTC');
					$date->setTimezone(new \DateTimeZone($user->get('timezone', $config->get('offset'))));

					// Transform the date string.
					return $date->toSql(true);
				}
				break;
		}

		return false;
	}
}
