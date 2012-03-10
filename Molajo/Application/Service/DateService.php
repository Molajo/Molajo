<?php
/**
 * @package	 	Molajo
 * @copyright	Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license	 	GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application\Service;
use Joomla\registry\Registry;
use Molajo\Application\MVC\Model\ApplicationsModel;

defined('MOLAJO') or die;

/**
 * Date
 *
 * @package     Molajo
 * @subpackage  Service
 * @since       1.0
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
        $locale = Services::Language()->get('tag', 'en-GB');

        $classname = str_replace('-', '_', $locale) . 'Date';
        if (class_exists($classname)) {
        } else {
            $classname = 'JDate';
        }

        return new $classname($time, $tzOffset);
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
     * @param $date1 string expressed as CCYY-MM-DD
     * @param $date2 string expressed as CCYY-MM-DD
     * returns integer difference in days
     *
     * @since 1.0
     * @return integer
     */
    public function differenceDays($date1, $date2)
    {
        $day1mm = substr($date1, 5, 2);
        $day1dd = substr($date1, 8, 2);
        $day1ccyy = substr($date1, 0, 4);
        $gregdate1 = gregoriantojd($day1mm, $day1dd, $day1ccyy);

        $day2mm = substr($date2, 5, 2);
        $day2dd = substr($date2, 8, 2);
        $day2ccyy = substr($date2, 0, 4);
        $gregdate2 = gregoriantojd($day2mm, $day2dd, $day2ccyy);

        return $gregdate1 - $gregdate2;
    }

    /**
     * prettydate
     *
     * @param  $date
     * @return string human-readable pretty date
     */
    public function prettydate($source_date)
    {
        /** user time zone */
        $source_date = Services::Date()->getUTCDate(
            $source_date, 'system'
        );
        $current_date = Services::Date()->getUTCDate(
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

        $days = $current_date - strtotime($remove_years_months, $source_date);
        $days = date('z', $days);

        /** only calculate hours, minutes and seconds for current date */
        $hours = 0;
        $minutes = 0;
        $seconds = 0;
        if ($years == 0 && $months == 0 && $days == 0) {
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
        if ($years == 0 && $months == 0 && $days == 0 && $hours == 0 && $days == 0 && $hours == 0) {
            return '';
        }

        /** format pretty date */
        $prettyDate = Services::Date()->prettyDateFormat($years, 'YEAR_SINGULAR', 'YEAR_PLURAL');
        $prettyDate .= Services::Date()->prettyDateFormat($months, 'MONTH_SINGULAR', 'MONTH_PLURAL');
        $prettyDate .= Services::Date()->prettyDateFormat($days, 'DAY_SINGULAR', 'DAY_PLURAL');
        $prettyDate .= Services::Date()->prettyDateFormat($hours, 'HOUR_SINGULAR', 'HOUR_PLURAL');
        $prettyDate .= Services::Date()->prettyDateFormat($minutes, 'MINUTE_SINGULAR', 'MINUTE_PLURAL');
        $prettyDate .= Services::Date()->prettyDateFormat($seconds, 'SECOND_SINGULAR', 'SECOND_PLURAL');

        /** remove leading comma */
        return trim(substr($prettyDate, 1, strlen($prettyDate) - 1));
    }

    /**
     * prettyDateFormat
     *
     * @param  $numeric_value
     * @param  $singular_literal
     * @param  $plural_literal
     * @return void, mixed
     */
    function prettyDateFormat($numeric_value, $singular_literal, $plural_literal)
    {
        if ($numeric_value == 0) {
            return;
        }

        if ($numeric_value == 1) {
            return ', ' . $numeric_value . ' ' .
                strtolower(Services::Language()->translate($singular_literal));
        }

        return ', ' . $numeric_value . ' ' .
            strtolower(Services::Language()->translate($plural_literal));
    }

    /**
     * buildCalendar
     *
     * @param string $month
     * @param string $year
     * @param string $year
     * @return string CCYY-MM-DD
     *
     * $dateComponents = getdate();
     * $month = $dateComponents['mon'];
     * $year = $dateComponents['year'];
     * echo Services::Date()->buildCalendar ($month,$year,$dateArray);
     */
//todo: Amy - redo to generate a set of dates, combine with other data, pass to a view for rendering

    function buildCalendar($month, $year, $dateArray)
    {
        $daysOfWeek = array('S', 'M', 'T', 'W', 'T', 'F', 'S');
        $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
        $numberDays = date('t', $firstDayOfMonth);
        $dateComponents = getdate($firstDayOfMonth);
        $monthName = $dateComponents['month'];
        $dayOfWeek = $dateComponents['wday'];

        $calendar = "<table class='calendar'>";
        $calendar .= "<caption>$monthName $year</caption>";
        $calendar .= "<tr>";
        foreach ($daysOfWeek as $day) {
            $calendar .= "<th class='header'>$day</th>";
        }

        $currentDay = 1;
        $calendar .= "</tr><tr>";
        if ($dayOfWeek > 0) {
            $calendar .= "<td colspan='$dayOfWeek'>&nbsp;</td>";
        }

        $month = str_pad($month, 2, "0", STR_PAD_LEFT);
        while ($currentDay <= $numberDays) {
            if ($dayOfWeek == 7) {
                $dayOfWeek = 0;
                $calendar .= "</tr><tr>";
            }
            $currentDayRel = str_pad($currentDay, 2, "0", STR_PAD_LEFT);
            $date = "$year-$month-$currentDayRel";
            $calendar .= "<td class='day' rel='$date'>$currentDay</td>";
            $currentDay++;
            $dayOfWeek++;
        }

        if ($dayOfWeek != 7) {
            $remainingDays = 7 - $dayOfWeek;
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
     * @return string
     */
    function getUTCDate($input_date, $server_or_user_UTC = 'user')
    {
//todo: amy fix user

        $server_or_user_UTC = 'SERVER_UTC';
        $config = Services::Configuration();
        $user = Services::User();

        // If a known filter is given use it.
        switch (strtoupper((string)$server_or_user_UTC))
        {
            case 'SERVER_UTC':
                // Convert a date to UTC based on the server timezone.
                if (intval($input_date)) {
                    // Get a date object based on the correct timezone.
                    $date = $this->getDate($input_date, 'UTC');
                    $date->setTimezone(new DateTimeZone($config->get('offset')));

                    // Transform the date string.
                    return $date->toSql(true);
                }
                break;

            default:
                // Convert a date to UTC based on the user timezone.
                if (intval($input_date)) {
                    // Get a date object based on the correct timezone.
                    $date = $this->getDate($input_date, 'UTC');
                    $date->setTimezone(new DateTimeZone($user->get('timezone', $config->get('offset'))));

                    // Transform the date string.
                    return $date->toSql(true);
                }
                break;
        }
    }
}
