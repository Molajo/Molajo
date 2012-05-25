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
		$triggers = Services::Filesystem()->folderFolders(EXTENSIONS_TRIGGERS);

//		$triggerClass = 'Molajo\\Extension\\Trigger\\Trigger\\Trigger';
//		$method = 'getInstance';
//		$connection = $triggerClass::$method();
//		$this->set('Trigger', $connection, true);

//		$triggerClass = 'Molajo\\Extension\\Trigger\\Content\\ContentTrigger';
//		$method = 'getInstance';
//		$connection = $triggerClass::$method();

//		$this->set('ContentTrigger', $connection, true);

		foreach ($triggers as $folder) {

			/** class name */
			if ($folder == 'Trigger'
				|| $folder == 'Content'
				|| substr(strtolower($folder), 0, 4) == 'hold'
			) {

			} else {
				$this->process_events($folder);
			}
		}

		return $this;
	}

	/**
	 * Connect to the trigger and register it for events
	 *
	 * @param  $folder
	 *
	 * @return Triggers
	 * @since  1.0
	 */
	protected function process_events($folder)
	{
		$try = true;
		$connection = '';

		$trigger = $folder . 'Trigger';
		$triggerClass = 'Molajo\\Extension\\Trigger\\' . $folder . '\\' . $trigger;

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

		/** Retrieve all Event Methods in the Trigger */
		$events = get_class_methods($triggerClass);

		/** Registry the trigger for each event */
		if (count($events) > 0) {
			foreach ($events as $event) {
				if (substr($event, 0, 2) == 'on') {
					Services::Event()->register($event, $trigger);
				}
			}
		}

		/** store connection or error message */
		$this->set($trigger, $connection, $try);

		return $this;
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
	protected function set($key, $value = null, $try = true)
	{
		if ($key == 'Trigger' || $key == 'ContentTrigger') {

		} else if ($value == null || $try == false) {
			Services::Debug()->set('Trigger: ' . $key . ' FAILED' . $value);

		} else {
			$this->trigger_connection[$key] = $value;
			Services::Debug()->set('Trigger: ' . $key . ' started successfully. ');
		}

		return $this;
	}
}
