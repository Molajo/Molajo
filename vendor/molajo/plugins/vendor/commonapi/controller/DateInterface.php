<?php
/**
 * Date Interface
 *
 * @package    Controller
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace CommonApi\Controller;

use DateTime;

/**
 * Date Interface
 *
 * @package    Controller
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @since      1.0
 */
interface DateInterface
{
    /**
     * Prepares Date object
     *
     * @param   string $time
     * @param   null   $timezone
     * @param   string $server_or_user_timezone
     * @param   string $date_format
     *
     * @return  DateTime
     * @since   1.0.0
     */
    public function getDate(
        $time = 'now',
        $timezone = null,
        $server_or_user_timezone = 'user',
        $date_format = 'Y-m-d H:i:s'
    );

    /**
     * Converts standard MYSQL date (ex. 2011-11-11 11:11:11) to CCYY-MM-DD format (ex. 2011-11-11)
     *
     * @param   string $date
     *
     * @return  string CCYY-MM-DD
     * @since   1.0.0
     */
    public function convertCCYYMMDD($date = null);

    /**
     * Get the number of days between two dates
     *
     * @param   string $date1 expressed as CCYY-MM-DD
     * @param   string $date2 expressed as CCYY-MM-DD
     *
     * @since   1.0.0
     * @return  integer
     */
    public function getNumberofDaysAgo($date1, $date2 = null);

    /**
     * Get Pretty Date
     *
     * @param   string $date
     * @param   string $compare_to_date
     *
     * @return  string formatted pretty date
     * @since   1.0.0
     */
    public function getPrettyDate($date, $compare_to_date = null);

    /**
     * Provides translated name of day in abbreviated or full format, given day number
     *
     * @param string $day_number
     * @param bool   $abbreviation
     *
     * @return string
     * @since   1.0.0
     */
    public function getDayName($day_number, $abbreviation = false);

    /**
     * Provides translated name of month in abbreviated or full format, given month number
     *
     * @param   string $month_number
     * @param   bool   $abbreviation
     *
     * @return  string
     * @since   1.0.0
     */
    public function getMonthName($month_number, $abbreviation = false);

    /**
     * buildCalendar
     *
     * $d = getDate();
     * $month = $d['mon'];
     * $year = $d['year'];
     *
     * $calendar = $this->date_controller->buildCalendar ($month, $year);
     *
     * @param   string $month
     * @param   string $year
     *
     * @return  string CCYY-MM-DD
     * @since   1.0.0
     */
    public function buildCalendar($month, $year);
}
