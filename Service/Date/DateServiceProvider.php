<?php
/**
 * Date Service Provider
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Date;

use Molajo\IoC\AbstractServiceProvider;
use CommonApi\IoC\ServiceProviderInterface;
use CommonApi\Exception\RuntimeException;

/**
 * Date Controller Service Provider
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class DateServiceProvider extends AbstractServiceProvider implements ServiceProviderInterface
{
    /**
     * Constructor
     *
     * @param   $options
     *
     * @since   1.0
     */
    public function __construct(array $options = array())
    {
        $options['service_namespace']        = 'Molajo\\Controller\\DateController';
        $options['store_instance_indicator'] = true;
        $options['service_name']             = basename(__DIR__);

        parent::__construct($options);
    }

    /**
     * Identify Class Dependencies for Constructor Injection
     *
     * @return  array
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    public function setDependencies(array $reflection = null)
    {
        $reflection = null;

        $this->dependencies['User']        = array('if_exists' => true);
        $this->dependencies['Language']    = array('if_exists' => true);
        $this->dependencies['Runtimedata'] = array();

        return $this->dependencies;
    }

    /**
     * Instantiate Class
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException;
     */
    public function instantiateService()
    {
        $timezone = '';
        if (isset($this->options['timezone'])) {
            $timezone = $this->options['timezone'];
        }

        if ($timezone === '') {
            if (ini_get('date.timezone')) {
                $timezone = ini_get('date.timezone');
            }
        }

        if ($timezone == '') {
            $timezone = 'America/Chicago';
        }

        ini_set('date.timezone', $timezone);

        if (isset($this->options['timezone_user'])) {
            $timezone_user = $this->options['timezone_user'];
        } else {
            $timezone_user = $timezone;
        }

        if (isset($this->options['timezone_server'])) {
            $timezone_server = $this->options['timezone_server'];
        } else {
            $timezone_server = $timezone;
        }

        if (isset($this->options['date_translate_array'])) {
            $date_translate_array = $this->options['date_translate_array'];
        } else {
            $date_translate_array = $this->getDefaultDateTranslations();
        }

        $class = 'Molajo\\Controller\\DateController';

        $this->service_instance = new $class(
            $timezone_user,
            $timezone_server,
            $date_translate_array
        );

        return $this;
    }

    /**
     * Get Default Date Translations
     *
     * @return  array
     * @since   1.0
     */
    protected function getDefaultDateTranslations()
    {
        return array(
            'AGO'                  => 'ago',
            'DATE_MINUTE_SINGULAR' => 'minute',
            'DATE_MINUTE_PLURAL'   => 'minutes',
            'DATE_HOUR_SINGULAR'   => 'hour',
            'DATE_HOUR_PLURAL'     => 'hours',
            'DATE_DAY_SINGULAR'    => 'day',
            'DATE_DAY_PLURAL'      => 'days',
            'DATE_WEEK_SINGULAR'   => 'week',
            'DATE_WEEK_PLURAL'     => 'weeks',
            'DATE_MONTH_SINGULAR'  => 'month',
            'DATE_MONTH_PLURAL'    => 'months',
            'DATE_YEAR_SINGULAR'   => 'year',
            'DATE_YEAR_PLURAL'     => 'years',
            'DATE_MON'             => 'Mon',
            'DATE_MONDAY'          => 'Monday',
            'DATE_TUE'             => 'Tue',
            'DATE_TUESDAY'         => 'Tuesday',
            'DATE_WED'             => 'Wed',
            'DATE_WEDNESDAY'       => 'Wednesday',
            'DATE_THU'             => 'Thu',
            'DATE_THURSDAY'        => 'Thursday',
            'DATE_FRI'             => 'Fri',
            'DATE_FRIDAY'          => 'Friday',
            'DATE_SAT'             => 'Sat',
            'DATE_SATURDAY'        => 'Saturday',
            'DATE_SUN'             => 'Sun',
            'DATE_SUNDAY'          => 'Sunday',
            'DATE_JAN'             => 'Jan',
            'DATE_JANUARY'         => 'January',
            'DATE_FEB'             => 'Feb',
            'DATE_FEBRUARY'        => 'February',
            'DATE_MAR'             => 'Mar',
            'DATE_MARCH'           => 'March',
            'DATE_APR'             => 'Apr',
            'DATE_APRIL'           => 'April',
            'DATE_MAY'             => 'May',
            'DATE_JUN'             => 'Jun',
            'DATE_JUNE'            => 'June',
            'DATE_JUL'             => 'Jul',
            'DATE_JULY'            => 'July',
            'DATE_AUG'             => 'Aug',
            'DATE_AUGUST'          => 'August',
            'DATE_SEP'             => 'Sep',
            'DATE_SEPTEMBER'       => 'September',
            'DATE_OCT'             => 'Oct',
            'DATE_OCTOBER'         => 'October',
            'DATE_NOV'             => 'Nov',
            'DATE_NOVEMBER'        => 'November',
            'DATE_DEC'             => 'Dec',
            'DATE_DECEMBER'        => 'December',
            'YESTERDAY'            => 'Yesterday'
        );
    }
}
