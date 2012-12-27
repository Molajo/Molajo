<?php
/**
 * @package    Niambie
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    MIT
 */
namespace Molajo\Service\Services\Profiler;

use Molajo\Service\Services;

defined('NIAMBIE') or die;

/**
 * Profiler
 *
 * @package     Niambie
 * @subpackage  Services
 * @since       1.0
 */
Class ProfilerService
{
    /**
     * Service Connection
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

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
     * Options needed by the selected profiler logger
     *
     * @var    object
     * @since  1.0
     */
    protected $options;

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
    protected $profiler_start_with;

    /**
     * End profiling with this phase
     *
     * @var    object
     * @since  1.0
     */
    protected $profiler_end_with;

    /**
     * Verbose mode provides considerably more detail
     *
     * @var    object
     * @since  1.0
     */
    protected $verbose = array();

    /**
     * Current phase
     *
     * @var    object
     * @since  1.0
     */
    protected $current_phase = array();

    /**
     * Phase Array
     *
     * @var    array
     * @since  1.0
     */
    protected $phase_array = array(
        INITIALISE    => 1,
        ROUTING       => 2,
        AUTHORISATION => 3,
        EXECUTE       => 4,
        RESPONSE      => 5
    );

    /**
     * Phase List
     *
     * @var    array
     * @since  1.0
     */
    protected $phase_array_list = array(
        INITIALISE,
        ROUTING,
        AUTHORISATION,
        EXECUTE,
        RESPONSE
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
     * Hold profiler output until Dependency Date Service is started
     *
     * @var    object
     * @since  1.0
     */
    protected $hold_for_date_service_startup = array();

    /**
     * Log Type
     *
     * @var    string
     * @since  1.0
     */
    const log_type = PROFILER_LITERAL;

    /**
     * getInstance initiated by the Services Class
     *
     * @static
     * @return  bool|object
     * @since   1.0
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new ProfilerService();
        }

        return self::$instance;
    }

    /**
     * Class constructor.
     *
     * @return  boolean
     * @since   1.0
     */
    public function __construct()
    {
        $this->current_phase       = INITIALISE;
        $this->profiler_start_with = INITIALISE;
        $this->profiler_end_with   = RESPONSE;

        return $this;
    }

    /**
     * Sets profiler message that is routed to the selected logger
     *
     * @param   string  $message
     * @param   string  $output_type  Application,Permissions,Queries,Rendering,Routing,Services,Plugins
     *
     * @return  boolean
     * @since   1.0
     */
    public function set($message, $output_type = '', $verbose = 0)
    {
        if ((int)$this->on == 0) {
            return;
        }
        if (in_array($message, $this->phase_array_list)) {
            Services::Registry()->set(PROFILER_LITERAL, 'CurrentPhase', $message);
        }

        $current_phase = Services::Registry()->get(PROFILER_LITERAL, 'CurrentPhase');

        if (in_array($current_phase, $this->phase_array_list)) {
        } else {
            return true;
        }

        if ((int)$this->on == 0) {
            if ((int)$this->configuration_complete == 0) {
            } else {
                return true;
            }
        }

        if ((int)$this->verbose == 1) {
        } else {
            if ((int)$verbose == 1) {
                return true;
            }
        }

        if ($this->phase_array[$current_phase] >= $this->phase_array[$this->profiler_start_with]
            && $this->phase_array[$current_phase] <= $this->phase_array[$this->profiler_end_with]
        ) {
        } else {
            return true;
        }

        if (in_array($output_type, $this->profiler_output_options)
            || $output_type == ''
        ) {
        } else {
            return true;
        }

        /** LOG IT */
        $elapsed = $this->getMicrotimeFloat() - $this->profiler_started_time;

        if (function_exists('memory_get_usage')) {
            $memory = memory_get_usage(true) / 1048576;
        }

        if ($memory > $this->previous_memory) {
            $memory_difference = $memory - $this->previous_memory;
        } else {
            $memory_difference = 0;
        }

        try {

            if (Services::Registry()->get(CATALOG_TYPE_SERVICE_LITERAL, 'DateService') == 1) {

                if (count($this->hold_for_date_service_startup) > 0) {

                    foreach ($this->hold_for_date_service_startup as $entry) {
                        Services::Log()->addEntry(
                            $entry['message'],
                            $entry['log_level'],
                            $entry['log_type'],
                            $entry['entry_date']
                        );
                    }
                    $this->hold_for_date_service_startup = array();
                }

                Services::Log()->addEntry(
                    sprintf(
                        '%.3f seconds (+%.3f); %0.2f MB (+%.3f) - %s',
                        $elapsed,
                        $elapsed - $this->previous_time,
                        $memory,
                        $memory_difference,
                        $output_type . ': ' . trim($message)
                    ),
                    LOG_TYPE_PROFILER,
                    self::log_type,
                    Services::Date()->getDate('now')
                );

            } else {
                $this->holdEntries($elapsed, $memory, $memory_difference, $output_type . ': ' . trim($message));
            }

        } catch (\Exception $e) {
            throw new \RuntimeException('Unable to add Log Entry: ' . $message . ' ' . $e->getMessage());
        }

        $this->previous_time   = $elapsed;
        $this->previous_memory = $memory;

        return true;
    }

    /**
     * holdEntries until the Configuration, Log, and Date Services are running and all
     * information needed to process, or not process, profiler entries is known.
     *
     * @param   string  $elapsed
     * @param   string  $memory
     * @param   string  $memory_difference
     * @param   string  $message
     *
     * @return  void
     * @since   1.0
     */
    public function holdEntries($elapsed, $memory, $memory_difference, $message)
    {
        $i = count($this->hold_for_date_service_startup) + 1;

        $entry = array(
            'message'    => sprintf(
                '%.3f seconds (+%.3f); %0.2f MB (+%.3f) - %s',
                $elapsed,
                $elapsed - $this->previous_time,
                $memory,
                $memory_difference,
                $message
            ),
            'log_level'  => LOG_TYPE_PROFILER,
            'log_type'   => self::log_type,
            'entry_date' => date("Y-m-d") . ' ' . date("H:m:s")
        ); // will not be set to timezone

        $this->hold_for_date_service_startup[$i] = $entry;

        return;
    }

    /**
     * Configuration invokes this method to initialise the profiler service (on or off)
     *
     * @return  boolean
     * @since   1.0
     */
    public function initialise()
    {
        if ((int)Services::Registry()->get('Configuration', 'profiler_service') == 1) {
            $this->on = 1;
            define('PROFILER_ON', true);
            Services::Registry()->set(PROFILER_LITERAL, 'on', true);
        } else {
            $this->on = 0;
            define('PROFILER_ON', false);
            Services::Registry()->set(PROFILER_LITERAL, 'on', false);

            return $this;
        }

        $this->hold_for_date_service_startup = array();

        $this->setProfilerOutputOptions();

        $this->profiler_started_time = $this->getMicrotimeFloat();

        $results = $this->setProfilerLogger();

        if ($results === false) {
            $this->on = 0;
            define('PROFILER_ON', false);
            Services::Registry()->set(PROFILER_LITERAL, 'on', false);

            return $this;
        } else {
            define('PROFILER_ON', true);
        }

        $this->profiler_start_with = Services::Registry()->get(
            CONFIGURATION_LITERAL,
            'profiler_start_with',
            INITIALISE
        );
        if (in_array($this->profiler_start_with, $this->phase_array)) {
            $this->profiler_start_with = INITIALISE;
        }

        $this->profiler_end_with = Services::Registry()->get(
            CONFIGURATION_LITERAL,
            'profiler_end_with',
            RESPONSE
        );
        if (in_array($this->profiler_end_with, $this->phase_array)) {
            $this->profiler_end_with = RESPONSE;
        }

        $this->verbose = (int)Services::Registry()->get(CONFIGURATION_LITERAL, 'profiler_verbose', VERBOSE);
        if ($this->verbose == VERBOSE) {
        } else {
            $this->verbose = 0;
        }

        Services::Registry()->set(PROFILER_LITERAL, 'CurrentPhase', INITIALISE);

        $this->set(INITIALISE, PROFILER_APPLICATION);

        return true;
    }

    /**
     * set options for profiling output as specified in the system configuration
     *
     * @return  boolean
     * @since   1.0
     */
    protected function setProfilerOutputOptions()
    {
        $outputOptions = array(
            PROFILER_ACTIONS,
            PROFILER_APPLICATION,
            PROFILER_AUTHORISATION,
            PROFILER_QUERIES,
            PROFILER_REGISTRY,
            PROFILER_RENDERING,
            PROFILER_ROUTING,
            PROFILER_SERVICES,
            PROFILER_PLUGINS
        );

        $temp = Services::Registry()->get(CONFIGURATION_LITERAL, 'profiler_output');

        if ($temp == '' || $temp == null) {
            $temp = $outputOptions;
        }

        $this->profiler_output_options = array();
        $temp2                         = explode(',', $temp);
        foreach ($temp2 as $item) {
            if (in_array($item, $outputOptions)) {
                $this->profiler_output_options[] = $item;
            }
        }

        return true;
    }

    /**
     * setProfilerLogger - establish connection to the selected profiler logger and initiate Profiler
     *
     * @return  mixed
     * @since   1.0
     */
    public function setProfilerLogger()
    {
        $this->on = 1;

        $loggerOptions = array(
            LOG_ECHO_LOGGER,
            LOG_FORMATTEDTEXT_LOGGER,
            LOG_DATABASE_LOGGER,
            LOG_EMAIL_LOGGER,
            LOG_CONSOLE_LOGGER,
            LOG_MESSAGES_LOGGER
        );

        $this->profiler_options = array();

        $this->profiler_options['logger'] = Services::Registry()->get(
            CONFIGURATION_LITERAL,
            'profiler_log',
            LOG_CONSOLE_LOGGER
        );

        $results = false;
        if (in_array($this->profiler_options['logger'], $loggerOptions)) {
        } else {
            $this->profiler_options['logger'] = LOG_ECHO_LOGGER;
        }

        $results = $logMethod = 'set' . ucfirst(strtolower($this->profiler_options['logger'])) . 'Logger';
        $this->$logMethod();

        if ($results === false) {
            $this->profiler_options           = array();
            $this->profiler_options['logger'] = LOG_ECHO_LOGGER;
        }

        $results             = array();
        $results['options']  = $this->profiler_options;
        $results['priority'] = LOG_TYPE_PROFILER;
        $results['types']    = self::log_type;

        return $results;
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
     * setEmailLogger
     *
     * @return  bool
     * @since   1.0
     */
    protected function setEmailLogger()
    {
        $this->profiler_options['mailer'] = Services::Mail();

        $this->profiler_options['reply_to'] = Services::Registry()->get(
            CONFIGURATION_LITERAL,
            'mailer_mail_reply_to',
            ''
        );
        $this->profiler_options['from']     = Services::Registry()->get(CONFIGURATION_LITERAL, 'mailer_mail_from', '');

        $this->profiler_options['subject'] = Services::Registry()->get(
            CONFIGURATION_LITERAL,
            'profiler_email_subject',
            ''
        );
        $this->profiler_options['to']      = Services::Registry()->get(CONFIGURATION_LITERAL, 'profiler_email_to', '');

        return true;
    }

    /**
     * setFormattedtextLogger
     *
     * @return  bool
     * @since   1.0
     */
    protected function setFormattedtextLogger()
    {
        $this->profiler_options['text_file'] = Services::Registry()->get(
            CONFIGURATION_LITERAL,
            'profiler_text_file',
            'profiler.php'
        );

        $temp = Services::Registry()->get(CONFIGURATION_LITERAL, 'profiler_text_file_path', 'SITE_LOGS_FOLDER');

        if ($temp == 'SITE_LOGS_FOLDER' || $temp == '') {
            $this->profiler_options['text_file_path'] = SITE_LOGS_FOLDER;

        } else {
            $this->profiler_options['text_file_path'] = $temp;
        }

        if (Services::Filesystem()->fileExists(SITE_LOGS_FOLDER . '/' . $this->profiler_options['text_file'])) {
            $this->profiler_options['text_file_no_php']
                = (int)Services::Registry()->get(CONFIGURATION_LITERAL, 'profiler_text_file_no_php', false);
        } else {
            return false;
        }

        return true;
    }

    /**
     * setDatabaseLogger
     *
     * @return  bool
     * @since   1.0
     */
    protected function setDatabaseLogger()
    {
        $this->profiler_options['dbo'] = Services::Database()->get('db');

        $this->profiler_options['db_table'] = Services::Registry()
            ->get(CONFIGURATION_LITERAL, 'profiler_database_table', '#__log');

        return true;
    }

    /**
     * setMessagesLogger
     *
     * @return  bool
     * @since   1.0
     */
    protected function setMessagesLogger()
    {
        $this->profiler_options['messages_namespace'] = Services::Registry()
            ->get(CONFIGURATION_LITERAL, 'profiler_messages_namespace', 'profiler');

        return true;
    }

    /**
     * setFirephpLogger
     *
     * @return  bool
     * @since   1.0
     */
    protected function setFirephpLogger()
    {
        $this->profiler_options['messages_namespace'] = Services::Registry()
            ->get(CONFIGURATION_LITERAL, 'profiler_messages_namespace', 'profiler');

        return true;
    }

    /**
     * setEchoLogger
     *
     * @return  bool
     * @since   1.0
     */
    protected function setEchoLogger()
    {
        $this->profiler_options['line_separator'] = Services::Registry()
            ->get(CONFIGURATION_LITERAL, 'profiler_line_separator', '<br />');

        return true;
    }

    /**
     * Get the current time from: http://php.net/manual/en/function.microtime.php
     *
     * @return  float
     * @since   1.0
     */
    public static function getMicrotimeFloat()
    {
        list ($usec, $sec) = explode(' ', microtime());

        return ((float)$usec + (float)$sec);
    }

    /**
     * getProfiler
     *
     * @return  array
     * @since   1.0
     */
    public function getProfiler()
    {
        $query_results = array();

        $messages = $this->get();

        if (count($messages) == 0) {
            return array();
        }

        foreach ($messages as $message) {

            $temp_row = new \stdClass();

            $temp_row->date     = $message['date'];
            $temp_row->priority = $message['priority'];
            $temp_row->type     = $message['type'];
            $temp_row->message  = $message['message'];

            $query_results[] = $temp_row;
        }

        return $query_results;
    }

    /**
     * get console log
     *
     * @return  array  console log entries
     * @since   1.0
     */
    public function get($option = null)
    {
        if ($option == 'count') {
            return Services::Log()->get($option);

        } else {
            return Services::Log()->get();
        }
    }
}
