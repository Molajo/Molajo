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

		/** Options based on Logger Type */
		$options['logger'] = Services::Registry()->get('Configuration\\debug_log', 'echo');

		if ($options['logger'] == 'email') {

		} elseif ($options['logger'] == 'text') {
			$options['logger'] = 'formattedtext';
			$options['text_file'] = 'debug.php';
			$options['text_file_path'] = SITE_LOGS_FOLDER;
			$options['text_file_no_php'] = false;

		} elseif ($options['logger'] == 'database') {
			$options['dbo'] = Services::Database()->get('db');
			$options['db_table'] = '#__log_entries';

		} elseif ($options['logger'] == 'messages') {

		} elseif ($options['logger'] == 'phpconsole') {

		} else {
			$options['logger'] = 'echo';
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
