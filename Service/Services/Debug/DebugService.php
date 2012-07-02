<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see license folder
 */
namespace Molajo\Service\Services\Debug;

use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Debug
 *
 * @package     Molajo
 * @subpackage  Services
 * @since       1.0
 */
Class DebugService
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
	protected $on;

	/**
	 * Time the Debug Service was started
	 *     used to calculate time between operations
	 *
	 * @var    float
	 * @since  1.0
	 */
	protected $debug_started_time = 0.0;

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
	 * Options needed by the selected debug logger
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected $options;

	/**
	 * Types of debug output desired
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected $debug_output_options = array();

	/**
	 * Begin debugging with this phase
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected $debug_start_with;

	/**
	 * End debugging with this phase
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected $debug_end_with;

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
	 * @var array
	 * @since  1.0
	 */
	protected $phase_array = array(
		START_INITIALISE => 1,
		START_ROUTING => 2,
		START_AUTHORISATION => 3,
		START_EXECUTE => 4,
		START_RESPONSE => 5
	);

	/**
	 * Phase List
	 *
	 * @var array
	 * @since  1.0
	 */
	protected $phase_array_list = array(
		START_INITIALISE,
		START_ROUTING,
		START_AUTHORISATION,
		START_EXECUTE,
		START_RESPONSE
	);

	/**
	 * Debug is started as a service, collecting entries internally until the Configuration and Log Services
	 *   have been activated, and have interacted with this service, completing the configuration process.
	 *   Before that process is complete, debug entries are held to see if debug is activated, and
	 *   if so, what log should be used. Once that information is available, the class then operates normally.
	 *
	 * @var array
	 * @since  1.0
	 */
	protected $configuration_complete = false;

	/**
	 * Hold debug output until Dependency Date Service is started
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected $hold_for_date_service_startup = array();

	/**
	 * Log Type
	 *
	 * @var   string
	 * @since 1.0
	 */
	const log_type = 'debugservice';

	/**
	 * getInstance initiated by the Services Class
	 *
	 * @static
	 * @return bool|object
	 * @since  1.0
	 */
	public static function getInstance()
	{
		if (empty(self::$instance)) {
			self::$instance = new DebugService();
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
		$this->current_phase = START_INITIALISE;
		$this->debug_start_with = START_INITIALISE;
		$this->debug_end_with = START_RESPONSE;

		return $this;
	}

	/**
	 * Sets debug message that is routed to the selected logger
	 *
	 * @param string  $message
	 * @param string  $output_type Application,Authorisation,Queries,Rendering,Routing,Services,Triggers
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	public function set($message, $output_type = '', $verbose = 0)
	{
		/** Exit if current phase not yet defined */
		if (in_array($message, $this->phase_array_list)) {
			Services::Registry()->set('DebugService', 'CurrentPhase', $message);
		}

		$current_phase = Services::Registry()->get('DebugService', 'CurrentPhase');

		if (in_array($current_phase, $this->phase_array_list)) {
		} else {
			return true;
		}

		/** 1. Is Debug on? */
		if ((int)$this->on == 0) {
			if ((int)$this->configuration_complete == 0) {
			} else {
				return true;
			}
		}

		/** 2. Verbose Mode for Verbose Detail? */
		if ((int)$this->verbose == 1) {
		} else {
			if ((int)$verbose == 1) {
				return true;
			}
		}

		/** 3. Is there a start and end phase? And, does the current phase fall within the range? */
		if ($this->phase_array[$current_phase] >= $this->phase_array[$this->debug_start_with]
			&& $this->phase_array[$current_phase] <= $this->phase_array[$this->debug_end_with]
		) {
		} else {
			return true;
		}

		/** 4. Do the Debug Output Types specified match the current Debug Output Type  */
		if (in_array($output_type, $this->debug_output_options)
			|| $output_type == ''
		) {
		} else {
			return true;
		}

		/** LOG IT */
		$elapsed = $this->getMicrotimeFloat() - $this->debug_started_time;

		if (function_exists('memory_get_usage')) {
			$memory = memory_get_usage(true) / 1048576;
		}

		$memory = $this->getMicrotimeFloat() - $this->debug_started_time;

		if ($memory > $this->previous_memory) {
			$memory_difference = $memory - $this->previous_memory;
		} else {
			$memory_difference = 0;
		}

		$this->previous_time = $elapsed;
		$this->previous_memory = $memory;

		try {

			if (Services::Registry()->get('Service', 'DateService') == 1) {

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
					sprintf('%.3f seconds (+%.3f); %0.2f MB (+%.3f) - %s',
						$elapsed,
						$elapsed - $this->previous_time,
						$memory,
						$memory_difference,
						$output_type . ': ' . trim($message)
					),
					LOG_TYPE_DEBUG,
					self::log_type,
					Services::Date()->getDate('now')
				);

			} else {

				$this->holdEntries($elapsed, $memory, $memory_difference, $output_type . ': ' . trim($message));
			}

		} catch (\Exception $e) {
			throw new \RuntimeException('Unable to add Log Entry: ' . $message . ' ' . $e->getMessage());
		}

		return true;
	}

	/**
	 * holdEntries until the Configuration, Log, and Date Services are running and all
	 * information needed to process, or not process, debug entries is known.
	 *
	 * @param  $elapsed
	 * @param  $memory
	 * @param  $memory_difference
	 * @param  $message
	 *
	 * @return  void
	 * @since   1.0
	 */
	public function holdEntries($elapsed, $memory, $memory_difference, $message)
	{
		$i = count($this->hold_for_date_service_startup) + 1;

		$entry = array(
			'message' => sprintf('%.3f seconds (+%.3f); %0.2f MB (+%.3f) - %s',
				$elapsed,
				$elapsed - $this->previous_time,
				$memory,
				$memory_difference,
				$message
			),
			'log_level' => LOG_TYPE_DEBUG,
			'log_type' => self::log_type,
			'entry_date' => date("Y-m-d") . ' ' . date("H:m:s")); // will not be set to timezone

		$this->hold_for_date_service_startup[$i] = $entry;

		return;
	}

	/**
	 * Configuration invokes this method to initiate the debug service if so configured
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	public function initiate()
	{
		$this->hold_for_date_service_startup = array();

		$this->setDebugOutputOptions();

		$this->debug_started_time = $this->getMicrotimeFloat();

		$results = $this->setDebugLogger();

		if ($results == false) {
			$this->on = 0;
			return $this;
		}

		$this->debug_start_with = Services::Registry()->get('Configuration', 'debug_start_with', START_INITIALISE);
		if (in_array($this->debug_start_with, $this->phase_array)) {
			$this->debug_start_with = START_INITIALISE;
		}

		$this->debug_end_with = Services::Registry()->get('Configuration', 'debug_end_with', START_RESPONSE);
		if (in_array($this->debug_end_with, $this->phase_array)) {
			$this->debug_end_with = START_RESPONSE;
		}

		$this->verbose = (int)Services::Registry()->get('Configuration', 'debug_verbose', VERBOSE);
		if ($this->verbose == VERBOSE) {
		} else {
			$this->verbose = 0;
		}

		Services::Registry()->set('DebugService', 'CurrentPhase', START_INITIALISE);

		$this->set(START_INITIALISE, LOG_OUTPUT_APPLICATION);

		return true;
	}

	/**
	 * setDebugOutputOptions - set options for debugging output specified in the configuration
	 *
	 * @return  Boolean
	 * @since   1.0
	 */
	protected function setDebugOutputOptions()
	{
		$outputOptions = array(
			LOG_OUTPUT_ACTIONS,
			LOG_OUTPUT_APPLICATION,
			LOG_OUTPUT_AUTHORISATION,
			LOG_OUTPUT_QUERIES,
			LOG_OUTPUT_REGISTRY,
			LOG_OUTPUT_RENDERING,
			LOG_OUTPUT_ROUTING,
			LOG_OUTPUT_SERVICES,
			LOG_OUTPUT_TRIGGERS
		);

		$temp = Services::Registry()->get('Configuration', 'debug_output');

		if ($temp == '' || $temp == null) {
			$temp = $outputOptions;
		}

		$this->debug_output_options = array();
		$temp2 = explode(',', $temp);
		foreach ($temp2 as $item) {
			if (in_array($item, $outputOptions)) {
				$this->debug_output_options[] = $item;
			}
		}

		return true;
	}

	/**
	 * setDebugLogger - establish connection to the selected debug logger and initiate Debugging
	 *
	 * @return  mixed
	 * @since   1.0
	 */
	public function setDebugLogger()
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

		$this->debug_options = array();
		$this->debug_options['logger'] = Services::Registry()->get('Configuration', 'debug_log', LOG_ECHO_LOGGER);

		$results = false;
		if (in_array($this->debug_options['logger'], $loggerOptions)) {
		} else {
			$this->debug_options['logger'] = LOG_ECHO_LOGGER;
		}

		$results = $logMethod = 'set' . ucfirst(strtolower($this->debug_options['logger'])) . 'Logger';
		$this->$logMethod();

		if ($results == false) {
			$this->debug_options = array();
			$this->debug_options['logger'] = LOG_ECHO_LOGGER;
		}

		/** Establish log for activated debug option */
		$results = array();
		$results['options'] = $this->debug_options;
		$results['priority'] = LOG_TYPE_DEBUG;
		$results['types'] = self::log_type;

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
	 * @return bool
	 */
	protected function setEmailLogger()
	{
		$this->debug_options['mailer'] = Services::Mail();

		$this->debug_options['reply_to'] = Services::Registry()->get('Configuration', 'mail_reply_to', '');
		$this->debug_options['from'] = Services::Registry()->get('Configuration', 'mail_from', '');
		$this->debug_options['subject'] = Services::Registry()->get('Configuration', 'debug_email_subject', '');
		$this->debug_options['to'] = Services::Registry()->get('Configuration', 'debug_email_to', '');

		return true;
	}

	/**
	 * setFormattedtextLogger
	 *
	 * @return bool
	 */
	protected function setFormattedtextLogger()
	{
		$this->debug_options['text_file'] = Services::Registry()->get('Configuration', 'debug_text_file', 'debug.php');

		$temp = Services::Registry()->get('Configuration', 'debug_text_file_path', 'SITE_LOGS_FOLDER');
		if ($temp == 'SITE_LOGS_FOLDER' || $temp == '') {
			$this->debug_options['text_file_path'] = SITE_LOGS_FOLDER;

		} else {
			$this->debug_options['text_file_path'] = $temp;
		}

		if (Services::Filesystem()->fileExists(SITE_LOGS_FOLDER . '/' . $this->debug_options['text_file'])) {
			$this->debug_options['text_file_no_php']
				= (int)Services::Registry()->get('Configuration', 'debug_text_file_no_php', false);

		} else {
			return false;
		}

		return true;
	}

	/**
	 * setDatabaseLogger
	 *
	 * @return bool
	 */
	protected function setDatabaseLogger()
	{
		$this->debug_options['dbo'] = Services::JDatabase()->get('db');
		$this->debug_options['db_table'] = Services::Registry()
			->get('Configuration', 'debug_database_table', '#__log');

		return true;
	}

	/**
	 * setMessagesLogger
	 *
	 * @return bool
	 */
	protected function setMessagesLogger()
	{
		$this->debug_options['messages_namespace'] = Services::Registry()
			->get('Configuration', 'debug_messages_namespace', 'debug');

		return true;
	}

	/**
	 * setFirephpLogger
	 *
	 * @return bool
	 */
	protected function setFirephpLogger()
	{
		$this->debug_options['messages_namespace'] = Services::Registry()
			->get('Configuration', 'debug_messages_namespace', 'debug');

		return true;
	}

	/**
	 * setEchoLogger
	 *
	 * @return bool
	 */
	protected function setEchoLogger()
	{
		$this->debug_options['line_separator'] = Services::Registry()
			->get('Configuration', 'debug_line_separator', '<br />');

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
}
