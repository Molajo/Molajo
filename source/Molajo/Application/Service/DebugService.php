<?php
/**
 * @package   Molajo
 * @copyright Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */

namespace Molajo\Application\Service;

use Molajo\Application\Services;

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
	 * Static instance
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected static $instance;

	/**
	 * $on Switch
	 *
	 * @var    object
	 * @since  1.0
	 */
	public $on;

	/**
	 * Log Type
	 *
	 * @var   string
	 * @since 1.0
	 */
	const log_type = 'debugservice';

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
		/** Set debugging on or off */
		$this->on = (int)Services::Registry()->get('Configuration\\debug', 0);
		if ($this->on == 0) {
			return true;
		}

		/** $options array */
		$options = array();

		/** Logger Type */
		$options['logger'] = Services::Registry()->get('Configuration\\debug_logger', 'echo');
		$loggerOptions = array();
		$loggerOptions[] = 'echo';
		$loggerOptions[] = 'formattedtext';
		$loggerOptions[] = 'database';
		/** Molajo-specific */
		$loggerOptions[] = 'email';
		$loggerOptions[] = 'console';
		$loggerOptions[] = 'messages';

		if (in_array($options['logger'], $loggerOptions)) {
		} else {
			$options['logger'] = 'echo';
		}

		$loggerSelected = false;

		if ($options['logger'] == 'email') {
			echo 'in here';
			$options['mailer'] == Services::Registry()->get('Configuration\\mailer', 'mailer');
			$options['mode'] == Services::Registry()->get('Configuration\\mailer', 'mail_mode');
			$options['reply_to'] == Services::Registry()->get('Configuration\\mailer', 'mail_reply_to');
			$options['from'] == Services::Registry()->get('Configuration\\mailer', 'mail_from');
			$options['subject'] == Services::Registry()->get('Configuration\\mailer', 'debug_email_subject');
			$options['to'] == Services::Registry()->get('Configuration\\mailer', 'debug_email_to');
		}
var_dump($options);
		die;
		if ($options['logger'] == 'formattedtext') {
			$options['logger'] = 'formattedtext';
			$options['text_file']  = Services::Registry()->get('Configuration\\debug_text_file', 'debug.php');
			$temp  = Services::Registry()->get('Configuration\\debug_text_file_path', 'SITE_LOGS_FOLDER');
			if ($temp == 'SITE_LOGS_FOLDER') {
				$options['text_file_path'] = SITE_LOGS_FOLDER;
			} else {
				$options['text_file_path'] = $temp;
			}
			if (Services::Filesystem()->fileExists(SITE_LOGS_FOLDER . '/'. $options['text_file'])) {
				$options['text_file_no_php'] = (int) Services::Registry()->get('Configuration\\debug_text_file_no_php', false);
				$loggerSelected = true;
			} else {
				$options = array();
				$options['logger'] = 'echo';
			}
		}

		if ($options['logger'] == 'database') {
			$options['dbo'] = Services::Database()->get('db');
			$options['db_table'] = Services::Registry()->get('Configuration\\debug_database_table', '#__log');
			$loggerSelected = true;
		}

		if ($options['logger'] == 'messages') {
			$options['messages_namespace'] = Services::Registry()->get('Configuration\\debug_messages_namespace', 'debug');
			$loggerSelected = true;
		}

		if ($options['logger'] == 'console') {
			//$loggerSelected = true;
		}

		if ($loggerSelected == false) {
			$options['logger'] = 'echo';
			$options['line_separator'] = Services::Registry()->get('Configuration\\debug_line_separator', '<br />');
		}

		/** Establish log for activated debug option */
		Services::Log()->setLog($options, LOG_TYPE_DEBUG, self::log_type);

		return $this;
	}

	/**
	 * Modifies a property of the Request Parameter object
	 *
	 * @param   string  $message
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	public function set($message)
	{
		if ((int)$this->on == 0) {
			return true;
		}

		try {
			Services::Log()->addEntry($message, LOG_TYPE_DEBUG, self::log_type, Services::Date()->getDate('now'));
		}
		catch (\Exception $e) {
			throw new \RuntimeException('Unable to add Log Entry: ' . $message . ' ' . $e->getMessage());
		}

		return true;
	}
}
