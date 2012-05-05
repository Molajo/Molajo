<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension;

use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Triggers
 *
 * @package     Molajo
 * @subpackage  Triggers
 * @since       1.0
 */
Class Triggers
{
    /**
     * Static instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

	/**
	 * Trigger Connections
	 *
	 * @var   object
	 * @since 1.0
	 */
	protected $trigger_connection;

	/**
	 * Messages
	 *
	 * @var   object
	 * @since 1.0
	 */
	protected $message;

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
			self::$instance = new Triggers();
		}
		return self::$instance;
	}

	/**
	 * __construct
	 *
	 * @return null
	 * @since  1.0
	 */
	public function __construct()
	{
		$this->trigger_connection = array();
	}

	/**
	 * Retrieves trigger key value pair
	 *
	 * @param  string  $key
	 * @param  string  $default
	 *
	 * @return  mixed
	 *
	 * @since   1.0
	 */
	public function get($key, $default = null)
	{
		if (isset($this->trigger_connection[$key])) {
			return $this->trigger_connection[$key];
		} else {
			//error
		}
	}

	/**
	 * Used to connect to triggers
	 *
	 * @static
	 * @param  $name
	 * @param  $arguments
	 */
	public static function __callStatic($name, $arguments)
	{
		return Application::Triggers()->get($name . 'Trigger');
	}

	/**
	 * loads all triggers in the triggers folder
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	public function connect()
	{
		$triggers = Services::Filesystem()->folderFiles(EXTENSIONS_TRIGGERS);

		$triggerClass = 'Molajo\\Extension\\Trigger\\Trigger';
		$method = 'getInstance';
		$connection = $triggerClass::$method();
		$this->set('Trigger', $connection, true);

		$triggerClass = 'Molajo\\Extension\\Trigger\\ContentTrigger';
		$method = 'getInstance';
		$connection = $triggerClass::$method();

		$this->set('ContentTrigger', $connection, true);

		foreach ($triggers as $filename) {

			$try = true;
			$connection = '';

			/** class name */
			if ($filename == 'Trigger'
				&& $filename == 'ContentTrigger'
				&& substr($filename, 0, 4) == 'hold'
			) {
				break;
			}
			$entry = substr($filename, 0, strlen($filename) - 4);
			$triggerClass = 'Molajo\\Extension\\Trigger\\' . $entry;

			/** method name */
			$method = 'getInstance';

			/** trap errors for missing class or method */
			if (class_exists($triggerClass)) {
				if (method_exists($triggerClass, $method)) {
				} else {
					$try = false;
					$connection = $triggerClass . '::' . $method . ' Class does not exist';
				}
			} else {
				$try = false;
				$connection = $triggerClass . ' Class does not exist';
			}

			/** make helper connection */
			if ($try === true) {
				try {
					$connection = $triggerClass::$method();

				} catch (\Exception $e) {
					$connection = 'Fatal Error: ' . $e->getMessage();
				}
			}

			/** store connection or error message */
			$this->set($entry, $connection, $try);
		}

		foreach ($this->message as $message) {
			Services::Debug()->set($message);
		}

		return true;
	}

	/**
	 * set
	 *
	 * Stores the helper connection
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 *
	 * @return  mixed
	 * @since   1.0
	 */
	private function set($key, $value = null, $try = true)
	{
		$i = count($this->message);

		if ($value == null || $try == false) {
			$this->message[$i] = 'Trigger: ' . $key . ' FAILED' . $value;

		} else {
			$this->trigger_connection[$key] = $value;
			$this->message[$i] = 'Trigger: ' . $key . ' started successfully. ';
		}
	}
}
