<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension;

use Molajo\Service\Services;

use Molajo\Application;

defined('MOLAJO') or die;

/**
 * Helpers
 *
 * @package     Molajo
 * @subpackage  Helpers
 * @since       1.0
 */
Class Helpers
{
	/**
	 * Static instance
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected static $instance;

	/**
	 * Service Connections
	 *
	 * @var   object
	 * @since 1.0
	 */
	protected $helper_connection;

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
			self::$instance = new Helpers();
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
		$this->helper_connection = array();
	}

	/**
	 * Retrieves helper key value pair
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
		if (isset($this->helper_connection[$key])) {
			return $this->helper_connection[$key];
		} else {
			//error
		}
	}

	/**
	 * Used to connect to helpers
	 *
	 * @static
	 * @param  $name
	 * @param  $arguments
	 */
	public static function __callStatic($name, $arguments)
	{
		return Application::Helpers()->get($name . 'Helper');
	}


	/**
	 * loads all helpers in the helpers folder
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	public function connect()
	{
		$helpers = Services::Filesystem()->folderFiles(EXTENSIONS_HELPERS);

		foreach ($helpers as $filename) {
			$try = true;
			$connection = '';

			/** class name */
			if (substr($filename, 0, 4) == 'hold') {
				break;
			}
			$entry = substr($filename, 0, strlen($filename) - 4);
			$helperClass = 'Molajo\\Extension\\Helper\\' . $entry;

			/** method name */
			$helperMethod = 'getInstance';

			/** trap errors for missing class or method */
			if (class_exists($helperClass)) {
				if (method_exists($helperClass, $helperMethod)) {
				} else {
					$try = false;
					$connection = $helperClass . '::' . $helperMethod . ' Class does not exist';
				}
			} else {
				$try = false;
				$connection = $helperClass . ' Class does not exist';
			}

			/** make helper connection */
			if ($try === true) {
				try {
					$connection = $helperClass::$helperMethod();

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
			$this->helper_connection[$key] = $value;
			$this->message[$i] = 'Trigger: ' . $key . ' started successfully. ';
		}
	}
}
