<?php
/**
 * Profiler Service
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Services\Profiler;

defined('NIAMBIE') or die;

/**
 * Profiler Services
 *
 * @author     Amy Stephen
 * @license    MIT
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @since      1.0
 *
 * Usage:
 *
 *  To retrieve Configuration data for the Application:
 *
 *  Services::Application()->get($key);
 *
 *  Services::Application()->set($key, $value);
 *
 *  System Class, not a Frontend Developer Resource
 */
Class ProfilerService
{
    /**
     * $on Switch
     *
     * @var    boolean
     * @since  1.0
     */
    public $on;

    /**
     * Time the Profiler Service was started, used to calculate time between operations
     *
     * @var    float
     * @since  1.0
     */
    protected $profiler_started_time = 0.0;

    /**
     * Used to compare to current time to determine elapsed time between operations
     *
     * @var    float
     * @since  1.0
     */
    protected $previous_time = 0.0;

    /**
     * Used to compare to current memory settings to determine if additional allocation was required
     *
     * @var    float
     * @since  1.0
     */
    protected $previous_memory = 0.0;

    /**
     * Types of profiler output desired
     *
     * @var    object
     * @since  1.0
     */
    protected $profiler_output_options = array();

    /**
     * Begin profiling with this phase
     *
     * @var    object
     * @since  1.0
     */
    protected $profiler_start_with = 'Initialise';

    /**
     * End profiling with this phase
     *
     * @var    object
     * @since  1.0
     */
    protected $profiler_end_with = 'Response';

    /**
     * Verbose mode provides considerably more detail
     *
     * @var    object
     * @since  1.0
     */
    protected $verbose = 0;

    /**
     * Current phase
     *
     * @var    string
     * @since  1.0
     */
    protected $current_phase = 'Initialise';

    /**
     * Phase Array
     *
     * @var    array
     * @since  1.0
     */
    protected $phase_array = array(
        'Initialise'    => 1,
        'Routing'       => 2,
        'Authorisation' => 3,
        'Execute'       => 4,
        'Response'      => 5
    );

    /**
     * Phase List
     *
     * @var    array
     * @since  1.0
     */
    protected $phase_array_list = array(
        'Initialise',
        'Routing',
        'Authorisation',
        'Execute',
        'Response'
    );

    /**
     * Profiler is started as a service, collecting entries internally until the Configuration and Log Services
     *   have been activated, and have interacted with this service, completing the configuration process.
     *   Before that process is complete, profiler entries are held to see if profiler is activated, and
     *   if so, what log should be used. Once that information is available, the class then operates normally.
     *
     * @var    array
     * @since  1.0
     */
    protected $configuration_complete = false;

    /**
     * Profiler Messages
     *
     * @var    object
     * @since  1.0
     */
    protected $messages = array();

    /**
     * Log Type
     *
     * @var    string
     * @since  1.0
     */
    const log_type = 'Profiler';

    /**
     * List of Properties
     *
     * @var    object
     * @since  1.0
     */
    protected $property_array = array(
        'on',
        'profiler_started_time',
        'previous_time',
        'previous_memory',
        'profiler_output_options',
        'profiler_start_with',
        'profiler_end_with',
        'verbose',
        'current_phase',
        'phase_array',
        'phase_array_list',
        'configuration_complete',
        'hold_for_date_service_startup',
        'log_type'
    );

    /**
     * Class constructor.
     *
     * @return  boolean
     * @since   1.0
     */
    public function __construct()
    {
        $this->current_phase       = 'Initialise';
        $this->profiler_start_with = 'Initialise';
        $this->profiler_end_with   = 'Response';

        return $this;
    }

    /**
     * Configuration invokes this method to initialise the profiler service (on or off)
     *
     * @return  boolean
     * @since   1.0
     */
    public function initialise()
    {
        $this->messages = array();

        $this->profiler_started_time = $this->getMicrotimeFloat();

        $this->current_phase = 'Initialise';

        return true;
    }

    /**
     * Get the current value (or default) of the specified key
     *
     * @param   string  $key
     * @param   mixed   $default
     *
     * @return  mixed
     * @since   1.0
     */
    public function get($key, $default = null)
    {
        $key = strtolower($key);

        if (in_array($key, $this->property_array)) {

        } else {
            throw new \OutOfRangeException
            ('Profiler Service: attempting to get value for unknown property: ' . $key);
        }

        if (isset($this->$key)) {
        } else {
            $this->$key = $default;
        }

        return $this->$key;
    }

    /**
     * Sets profiler message
     *
     * @param   string  $key
     * @param   string  $value
     * @param   string  $output_type  Application,Permissions,Queries,Rendering,Routing,Services,Plugins
     * @param   int     $verbose
     *
     * @since   1.0
     * @return  void
     * @throws  \OutOfRangeException
     */
    public function set($key, $value, $output_type = '', $verbose = 0)
    {
        $key = strtolower($key);

        /** Settings */
        if (in_array($key, $this->property_array)) {
            $this->$key = $value;

            return;
        }

        if ((int)$this->on == 0) {
            return;
        }

        /** Set the Current Phase */
        if ($key == 'phase') {
            if (in_array($value, $this->phase_array_list)) {
                $this->current_phase = $value;
            } else {
                throw new \OutOfRangeException
                ('Profiler Service: invalid phase: ' . $value);
            }
        }

        /** Message Criteria */
        if ($key == 'message') {
        } else {
            throw new \OutOfRangeException
            ('Profiler Service: invalid key: ' . $key);
        }

        if ((int)$this->verbose == 1) {
        } else {
            if ((int)$verbose == 1) {
                return;
            }
        }

        if ($this->phase_array[$this->current_phase] >= $this->phase_array[$this->profiler_start_with]
            && $this->phase_array[$this->current_phase] <= $this->phase_array[$this->profiler_end_with]
        ) {
        } else {
            return;
        }

        /** Format and save Message */
        $elapsed = $this->getMicrotimeFloat() - $this->profiler_started_time;

        $memory = 0;
        if (function_exists('memory_get_usage')) {
            $memory = memory_get_usage(true) / 1048576;
        }

        if ($memory > $this->previous_memory) {
            $memory_difference = $memory - $this->previous_memory;
        } else {
            $memory_difference = 0;
        }

        $temp_row = new \stdClass();

        $query_results[] = $temp_row;

        $temp_row->formatted_message       = sprintf(
            '%.3f seconds (+%.3f); %0.2f MB (+%.3f) - %s',
            $elapsed,
            $elapsed - $this->previous_time,
            $memory,
            $memory_difference,
            $value
        );
        $temp_row->message                 = $value;
        $temp_row->total_elapsed_time      = $elapsed;
        $temp_row->additional_elapsed_time = $elapsed - $this->previous_time;
        $temp_row->total_memory            = $memory;
        $temp_row->additional_memory       = $memory_difference;
        $temp_row->entry_date              = date("Y-m-d") . ' ' . date("H:m:s"); // not be set to timezone

        $this->previous_time   = $elapsed;
        $this->previous_memory = $memory;

        return;
    }

    /**
     * The Log Service invokes this method to mark the configuration process complete.
     *     All that remains to be initiated is the Date Service. Once that is complete, the class
     *     switches over to normal logging (if it is so configured).
     *
     * @return  boolean
     * @since   1.0
     */
    public function setConfigurationComplete()
    {
        $this->configuration_complete = 1;

        return $this;
    }

    /**
     * Get the current time from: http://php.net/manual/en/function.microtime.php
     *
     * @return  float
     * @since   1.0
     */
    public function getMicrotimeFloat()
    {
        list ($usec, $sec) = explode(' ', microtime());

        return ((float)$usec + (float)$sec);
    }
}
