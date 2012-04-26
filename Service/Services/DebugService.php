<?php
/**
 * @package   Molajo
 * @copyright Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */

namespace Molajo\Service;

use Molajo\Services;

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
		$this->on = (int)Service::Registry()->get('Configuration', 'debug', 0);
		if ($this->on == 0)
		{
			return true;
		}

		/** Valid Logger Options */

		$loggerOptions = array();
		$loggerOptions[] = 'echo';
		$loggerOptions[] = 'formattedtext';
		$loggerOptions[] = 'database';
		$loggerOptions[] = 'email';
		$loggerOptions[] = 'chromephp';
		$loggerOptions[] = 'firephp';
		$loggerOptions[] = 'messages';

		/** @var $options */
		$options = array();

		/** Logger Type */
		$options['logger'] = Service::Registry()->get('Configuration', 'debug_logger', 'echo');

		$options['logger'] = 'firephp';

		if (!in_array($options['logger'], $loggerOptions)) {
			$options['logger'] = 'echo';
		}

		/** Email */
		if ($options['logger'] == 'email') {
			$options['mailer'] = Service::Mail();
			$options['reply_to'] = Service::Registry()->get('Configuration', 'mail_reply_to', '');
			$options['from'] = Service::Registry()->get('Configuration', 'mail_from', '');
			$options['subject'] = Service::Registry()->get('Configuration', 'debug_email_subject', '');
			$options['to'] = Service::Registry()->get('Configuration', 'debug_email_to', '');
		}

		/** Formatted Text */
		if ($options['logger'] == 'formattedtext') {
			$options['logger'] = 'formattedtext';
			$options['text_file']  = Service::Registry()->get('Configuration', 'debug_text_file', 'debug.php');
			$temp  = Service::Registry()->get('Configuration', 'debug_text_file_path', 'SITE_LOGS_FOLDER');
			if ($temp == 'SITE_LOGS_FOLDER') {
				$options['text_file_path'] = SITE_LOGS_FOLDER;
			} else {
				$options['text_file_path'] = $temp;
			}
			if (Service::Filesystem()->fileExists(SITE_LOGS_FOLDER . '/'. $options['text_file'])) {
				$options['text_file_no_php']
					= (int) Service::Registry()->get('Configuration', 'debug_text_file_no_php', false);
				$loggerSelected = true;
			} else {
				$options = array();
				$options['logger'] = 'echo';
			}
		}

		/** Database */
		if ($options['logger'] == 'database') {
			$options['dbo'] = Service::Database()->get('db');
			$options['db_table'] = Service::Registry()->get('Configuration', 'debug_database_table', '#__log');
			$loggerSelected = true;
		}

		/** Messages */
		if ($options['logger'] == 'messages') {
			$options['messages_namespace']
				= Service::Registry()->get('Configuration', 'debug_messages_namespace', 'debug');
			$loggerSelected = true;
		}

		/** Chrome Console */
		if ($options['logger'] == 'chromephp') {
		}

		/** Echo */
		if ($options['logger'] == 'echo') {
			$options['logger'] = 'echo';
			$options['line_separator'] = Service::Registry()->get('Configuration', 'debug_line_separator', '<br />');
		}

		/** Establish log for activated debug option */
		Service::Log()->setLog($options, LOG_TYPE_DEBUG, self::log_type);

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
			Service::Log()->addEntry($message, LOG_TYPE_DEBUG, self::log_type, Service::Date()->getDate('now'));
		}
		catch (\Exception $e) {
			throw new \RuntimeException('Unable to add Log Entry: ' . $message . ' ' . $e->getMessage());
		}

		return true;
	}
}
