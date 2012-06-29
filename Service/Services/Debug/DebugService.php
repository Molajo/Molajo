<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
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
	 * Hold debug output until Dependency Date Service is started
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected $hold_for_date_service_startup = array();

	/**
	 * Begin debugging with this phase
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected $debug_start_with = array();

	/**
	 * End debugging with this phase
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected $debug_end_with = array();

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
		'Initialise' => 1,
		'Route' => 2,
		'Authorise' => 3,
		'Execute' => 4,
		'Response' => 5
	);

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
		if (Services::Registry()->get('Configuration', 'Debug', 0) == 0) {
			$this->on = 0;
			return $this;
		}

		$this->on = 1;

		$this->hold_for_date_service_startup = array();

		$this->setDebugOutputOptions();

		$this->debug_started_time = $this->getMicrotimeFloat();

		$results = $this->setDebugLogger();

		if ($results == false) {
			$this->on = 0;
			return $this;
		}

		$this->debug_start_with = Services::Registry()->get('Configuration', 'debug_start_with', 'Initialise');
		if ($this->debug_start_with == '') {
			$this->debug_start_with = 'Initialise';
		}

		$this->debug_end_with = Services::Registry()->get('Configuration', 'debug_end_with', 'Response');
		if ($this->debug_end_with == '') {
			$this->debug_end_with = 'Response';
		}

		$this->verbose = (int)Services::Registry()->get('Configuration', 'verbose', '0');
		if ($this->verbose == 1) {
		} else {
			$this->verbose = 0;
		}

		$this->current_phase = 'Initialise';

		$this->set(START_INITIALISE, LOG_OUTPUT_APPLICATION);

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
		/** 1. Is Debug on? */
		if ((int)$this->on == 0) {
			return true;
		}

		/** 2. Verbose Mode for Verbose Detail? */
		if ((int)$this->verbose == 1) {
		} else {
			if ((int)$verbose == 1) {
				return true;
			}
		}

		/** 3. Is there a start and end phase? And, does the current phase fall within the range? */
		$phases = array(
			START_INITIALISE,
			START_ROUTE,
			START_AUTHORISE,
			START_EXECUTE,
			START_RESPONSE
		);

		if (in_array($message, $phases)) {
			$this->current_phase = $message;
		}

		if ($this->phase_array[$this->current_phase] >= $this->phase_array[$this->debug_start_with]
			&& $this->phase_array[$this->current_phase] <= $this->phase_array[$this->debug_end_with]
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

			/** Debug entries are held until the Date Service has been started and then spooled out when available */

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
				$i = count($this->hold_for_date_service_startup) + 1;

				$entry = array(
					'message' => sprintf('%.3f seconds (+%.3f); %0.2f MB (+%.3f) - %s',
						$elapsed,
						$elapsed - $this->previous_time,
						$memory,
						$memory_difference,
						$output_type . ': ' . trim($message)
					),
					'log_level' => LOG_TYPE_DEBUG,
					'log_type' => self::log_type,
					'entry_date' => date("Y-m-d") . ' ' . date("H:m:s")); // will not be set to timezone

				$this->hold_for_date_service_startup[$i] = $entry;
			}

		} catch (\Exception $e) {
			throw new \RuntimeException('Unable to add Log Entry: ' . $message . ' ' . $e->getMessage());
		}

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
	 * setDebugLogger - establish connection to the selected debug logger
	 *
	 * @return  mixed
	 * @since   1.0
	 */
	protected function setDebugLogger()
	{
		$loggerOptions = array('echo,formattedtext,database,email,firephp,messages');

		$this->debug_options = array();
		$this->debug_options['logger'] = Services::Registry()->get('Configuration', 'debug_log', 'echo');

		$results = false;
		if (in_array($this->debug_options['logger'], $loggerOptions)) {
		} else {
			$this->debug_options['logger'] = 'echo';
		}

		$results = $logMethod = 'set' . ucfirst(strtolower($this->debug_options['logger'])) . 'Logger';
		$this->$logMethod();

		if ($results == false) {
			$this->debug_options = array();
			$this->debug_options['logger'] = 'echo';
		}

		/** Establish log for activated debug option */
		Services::Log()->setLog($this->debug_options, LOG_TYPE_DEBUG, self::log_type);

		return true;
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
