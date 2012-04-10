<?php
namespace Joomla\utilities;
use DateTime;

/**
 * @package     Joomla.Platform
 * @subpackage  Utilities
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * JDate is a class that stores a date and provides logic to manipulate
 * and render that date in a variety of formats.
 *
 * @package     Joomla.Platform
 * @subpackage  Utilities
 * @since       11.1
 */
class JDate extends DateTime
{
    const DAY_ABBR = "\x021\x03";
    const DAY_NAME = "\x022\x03";
    const MONTH_ABBR = "\x023\x03";
    const MONTH_NAME = "\x024\x03";

    /**
     * The format string to be applied when using the __toString() magic method.
     *
     * @var    string
     * @since  11.1
     */
    public static $format = 'Y-m-d H:i:s';

    /**
     * Placeholder for a DateTimeZone object with GMT as the time zone.
     *
     * @var    object
     * @since  11.1
     */
    protected static $gmt;

    /**
     * Placeholder for a DateTimeZone object with the default server
     * time zone as the time zone.
     *
     * @var    object
     * @since  11.1
     */
    protected static $stz;

    /**
     * The DateTimeZone object for usage in rending dates as strings.
     *
     * @var    object
     * @since  11.1
     */
    protected $_tz;

    /**
     * Constructor.
     *
     * @param   string  $date  String in a format accepted by strtotime(), defaults to "now".
     * @param   mixed   $tz    Time zone to be used for the date.
     *
     * @since   1.0
     */
    public function __construct($date = 'now', $tz = null)
    {
        // Create the base GMT and server time zone objects.
        if (empty(self::$gmt) || empty(self::$stz)) {
            self::$gmt = new \DateTimeZone('GMT');
            self::$stz = new \DateTimeZone(@date_default_timezone_get());
        }

        // If the time zone object is not set, attempt to build it.
        if (!($tz instanceof \DateTimeZone)) {
            if ($tz === null) {
                $tz = self::$gmt;
            }
            else
            {
                $tz = new \DateTimeZone($tz);
            }
        }

        // If the date is numeric assume a unix timestamp and convert it.
        date_default_timezone_set('UTC');
        $date = is_numeric($date) ? date('c', $date) : $date;

        // Call the DateTime constructor.
        parent::__construct($date, $tz);

        // reset the timezone for 3rd party libraries/extension that does not use JDate
        date_default_timezone_set(self::$stz->getName());

        // Set the timezone object for access later.
        $this->_tz = $tz;
    }

    /**
     * Magic method to access properties of the date given by class to the format method.
     *
     * @param   string  $name  The name of the property.
     *
     * @return  mixed   A value if the property name is valid, null otherwise.
     *
     * @since   1.0
     */
    public function __get($name)
    {
        $value = null;

        switch ($name)
        {
            case 'daysinmonth':
                $value = $this->format('t', true);
                break;

            case 'dayofweek':
                $value = $this->format('N', true);
                break;

            case 'dayofyear':
                $value = $this->format('z', true);
                break;

            case 'isleapyear':
                $value = (boolean)$this->format('L', true);
                break;

            case 'day':
                $value = $this->format('d', true);
                break;

            case 'hour':
                $value = $this->format('H', true);
                break;

            case 'minute':
                $value = $this->format('i', true);
                break;

            case 'second':
                $value = $this->format('s', true);
                break;

            case 'month':
                $value = $this->format('m', true);
                break;

            case 'ordinal':
                $value = $this->format('S', true);
                break;

            case 'week':
                $value = $this->format('W', true);
                break;

            case 'year':
                $value = $this->format('Y', true);
                break;

            default:
                $trace = debug_backtrace();
                trigger_error(
                    'Undefined property via __get(): ' . $name . ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'],
                    E_USER_NOTICE
                );
        }

        return $value;
    }

    /**
     * Magic method to render the date object in the format specified in the public
     * static member JDate::$format.
     *
     * @return  string  The date as a formatted string.
     *
     * @since   1.0
     */
    public function __toString()
    {
        return (string)parent::format(self::$format);
    }

    /**
     * Proxy for new JDate().
     *
     * @param   string  $date  String in a format accepted by strtotime(), defaults to "now".
     * @param   mixed   $tz    Time zone to be used for the date.
     *
     * @return  JDate
     *
     * @since   11.3
     * @throws  JException
     */
    public static function getInstance($date = 'now', $tz = null)
    {
        return new JDate($date, $tz);
    }

    /**
     * Gets the date as a formatted string in a local calendar.
     *
     * @param   string   $format     The date format specification string (see {@link PHP_MANUAL#date})
     * @param   boolean  $local      True to return the date string in the local time zone, false to return it in GMT.
     * @param   boolean  $translate  True to translate localised strings
     *
     * @return  string   The date string in the specified format format.
     *
     * @since   1.0
     */
    public function calendar($format, $local = false, $translate = true)
    {
        return $this->format($format, $local, $translate);
    }

    /**
     * Gets the date as a formatted string.
     *
     * @param   string   $format     The date format specification string (see {@link PHP_MANUAL#date})
     * @param   boolean  $local      True to return the date string in the local time zone, false to return it in GMT.
     * @param   boolean  $translate  True to translate localised strings
     *
     * @return  string   The date string in the specified format format.
     *
     * @since   1.0
     */
    public function format($format, $local = false, $translate = true)
    {
        if ($translate) {
            // Do string replacements for date format options that can be translated.
            $format = preg_replace('/(^|[^\\\])D/', "\\1" . self::DAY_ABBR, $format);
            $format = preg_replace('/(^|[^\\\])l/', "\\1" . self::DAY_NAME, $format);
            $format = preg_replace('/(^|[^\\\])M/', "\\1" . self::MONTH_ABBR, $format);
            $format = preg_replace('/(^|[^\\\])F/', "\\1" . self::MONTH_NAME, $format);
        }

        // If the returned time should not be local use GMT.
        if ($local == false) {
            parent::setTimezone(self::$gmt);
        }

        // Format the date.
        $return = parent::format($format);

        if ($local == false) {
            parent::setTimezone($this->_tz);
        }

        return $return;
    }

    /**
     * Get the time offset from GMT in hours or seconds.
     *
     * @param   boolean  $hours  True to return the value in hours.
     *
     * @return  float  The time offset from GMT either in hours or in seconds.
     *
     * @since   1.0
     */
    public function getOffsetFromGMT($hours = false)
    {
        return (float)$hours ? ($this->_tz->getOffset($this) / 3600) : $this->_tz->getOffset($this);
    }

    /**
     * Method to wrap the setTimezone() function and set the internal
     * time zone object.
     *
     * @param   object  $tz  The new DateTimeZone object.
     *
     * @return  DateTimeZone  The old DateTimeZone object.
     *
     * @since   1.0
     */
    public function setTimezone($tz)
    {
        $this->_tz = $tz;
        return parent::setTimezone($tz);
    }

    /**
     * Gets the date as an ISO 8601 string.  IETF RFC 3339 defines the ISO 8601 format
     * and it can be found at the IETF Web site.
     *
     * @param   boolean  $local  True to return the date string in the local time zone, false to return it in GMT.
     *
     * @return  string  The date string in ISO 8601 format.
     *
     * @link    http://www.ietf.org/rfc/rfc3339.txt
     * @since   1.0
     */
    public function toISO8601($local = false)
    {
        return $this->format(\DateTime::RFC3339, $local, false);
    }

    /**
     * Gets the date as an MySQL datetime string.
     *
     * @param   boolean  $local  True to return the date string in the local time zone, false to return it in GMT.
     *
     * @return  string   The date string in MySQL datetime format.
     *
     * @link http://dev.mysql.com/doc/refman/5.0/en/datetime.html
     * @since   1.0
     */
    public function toSql($local = false)
    {
        return $this->format('Y-m-d H:i:s', $local, false);
    }

    /**
     * Gets the date as an RFC 822 string.  IETF RFC 2822 supercedes RFC 822 and its definition
     * can be found at the IETF Web site.
     *
     * @param   boolean  $local  True to return the date string in the local time zone, false to return it in GMT.
     *
     * @return  string   The date string in RFC 822 format.
     *
     * @link    http://www.ietf.org/rfc/rfc2822.txt
     * @since   1.0
     */
    public function toRFC822($local = false)
    {
        return $this->format(\DateTime::RFC2822, $local, false);
    }

    /**
     * Gets the date as UNIX time stamp.
     *
     * @return  integer  The date as a UNIX timestamp.
     *
     * @since   1.0
     */
    public function toUnix()
    {
        return (int)parent::format('U');
    }
}
