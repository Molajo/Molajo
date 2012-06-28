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
		$this->on = Services::Registry()->get('Configuration', 'Debug', 0);

		if ($this->on == 0) {
			Services::Registry()->set('DebugService', 'on', false);
			return $this;
		}

		Services::Registry()->set('DebugService', 'on', true);

		$this->setDebugOutputOptions();

		$this->debug_started_time = $this->getMicrotimeFloat();

		$results = $this->setDebugLogger();
		if ($results == false) {
			$this->on = Services::Registry()->get('Configuration', 'Debug', 0);
			return $this;
		}

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
	public function set($message, $output_type = '')
	{
		if ((int)$this->on == 0) {
			return true;
		}
	  echo $output_type.'<br />';
		if (in_array($output_type, $this->debug_output_options)
			|| $output_type == ''
		) {
		} else {
			return true;
		}

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
			Services::Log()->addEntry(
				sprintf('%.3f seconds (+%.3f); %0.2f MB (+%.3f) - %s',
					$elapsed,
					$elapsed - $this->previous_time,
					$memory,
					$memory_difference,
					$output_type . ':' . $message
				),
				LOG_TYPE_DEBUG,
				self::log_type,
				Services::Date()->getDate('now')
			);

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
		$outputOptions = array('Actions,Application','Authorisation','Queries',
			'Registry', 'Rendering','Routing','Services','Triggers');

		$temp = Services::Registry()->get('Configuration', 'debug_output');

		if ($temp == '' || $temp == null) {
			$temp = 'Actions,Application,Authorisation,Queries,
			Registry,Rendering,Routing,Services,Triggers';
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
