<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Service\Services\Event;

use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Event
 *
 * @package     Molajo
 * @subpackage  Service
 * @since       1.0
 */
Class EventService
{
	/**
	 * @static
	 * @var    object
	 * @since  1.0
	 */
	protected static $instance;

	/**
	 * Registered triggers
	 *
	 * @var   object
	 * @since 1.0
	 */
	protected $trigger_connection;

	/**
	 * @static
	 * @return bool|object
	 * @since   1.0
	 */
	public static function getInstance()
	{
		if (empty(self::$instance)) {
			self::$instance = new EventService();
		}

		return self::$instance;
	}

	/**
	 * Class constructor.
	 *
	 * @return boolean
	 * @since  1.0
	 */
	public function __construct()
	{
		Services::Registry()->createRegistry('Events');

		$this->registerInstalledTriggers();
	}

	/**
	 * application and controller schedule events with the event manager
	 *
	 * the event manager then fires off triggers which have registered for the event
	 *
	 * Usage:
	 * Services::Event()->schedule('onAfterDelete', $parameters, $selections);
	 *
	 * @param string $event
	 * @param array  $parameters
	 * @param array  $selections
	 *
	 * @return boolean
	 *
	 * @since   1.0
	 */
	public function schedule($event, $arguments = array(), $selections = array())
	{
		/** Does Event (with registration) exist? */
		$exists = Services::Registry()->exists('Events', $event);
		if ($exists == false) {
			return false;
		}

		/** Retrieve Event Registrations */
		$registrations = Services::Registry()->get($event);
		if (count($registrations) == 0) {
			return $arguments;
		}

		/** Filter for specified triggers (Query triggers) or use all triggers registered for event */
		if (is_array($selections)) {

		} else {
			if (trim($selections) == '') {
				$selections = array();
			} else {
				$temp = trim($selections);
				$selections = array();
				$selections[] = $temp;
			}
		}

		if (count($selections) > 0) {

		} else {
			/** default to all events */
			$selections = array();
			if (count($registrations) > 0) {
				foreach ($registrations as $key => $value) {
					$temp = substr($key, 0, strlen($key) - strlen('Trigger'));
					$selections[] = $temp;
				}
			}
		}

		/** Process each trigger */
		foreach ($selections as $selection) {

			$key = strtolower($selection) . 'trigger';

			if (isset($registrations[$key])) {

				if (method_exists($registrations[$key], $event)) {

					/** Retrieve Connection Information for the Trigger */
					$triggerClass = $registrations[$key];
					$method = 'getInstance';

					try {
						$connection = new $triggerClass();

					} catch (\Exception $e) {
						$connection = 'Could not Instantiate Trigger Class: ' . $triggerClass . $e->getMessage();
					}

					/** Set Properties for Trigger Class */
					if (count($arguments) > 0) {
						foreach ($arguments as $key => $value) {
							$connection->set($key, $value);
						}
						$connection->setFields();
					}
					/** Execute the Trigger Method */

					$results = $connection->$event();

					if ($results == false) {

					} else {

						/** Retrieve Properties from Trigger Class */
						if (count($arguments) > 0) {
							foreach ($arguments as $key2 => $value2) {
								$arguments[$key2] = $connection->get($key2, $value2);
							}
						}
					}
				} else {
					echo 'Event does not exist ' . $registrations[$key] . ' ' . $event . '<br />';
					die;
				}
			}
		}

		return $arguments;
	}

	/**
	 * Triggers register for events. When the event is scheduled, the trigger will be executed.
	 *
	 * Installed triggers are registered during Application startup process.
	 * Other triggers can be created and dynamically registered using this method.
	 * Triggers can be overridden by registering after the installed triggers.
	 *
	 * Usage:
	 * Services::Event()->register(
	 *   'AliasTrigger',
	 *   'Molajo\\Extension\\Trigger\\Alias\\AliasTrigger',
	 *   'OnBeforeUpdate'
	 * );
	 *
	 * @return object
	 * @since   1.0
	 */
	public function register($trigger, $triggerPath, $event)
	{
		/** Register Event (if not already registered) */
		$exists = Services::Registry()->exists('Events', $event);

		/** Retrieve number of registrations or register new event*/
		if ($exists == true) {
			$count = Services::Registry()->get('Events', $event, 0);
			$count++;

		} else {
			$exists = Services::Registry()->set('Events', $event, 0);
			Services::Registry()->createRegistry($event);
			$count = 1;
		}

		/** Register the event (can be used to override installed events) */
		Services::Registry()->set($event, $trigger, $triggerPath);

		/** Update Event Totals */
		Services::Registry()->set('Events', $event, $count);

		return $this;
	}

	/**
	 * Automatically registers all Triggers in the Extension Trigger folder
	 *
	 * @return object
	 * @since   1.0
	 */
	protected function registerInstalledTriggers()
	{
		$triggers = Services::Filesystem()->folderFolders(EXTENSIONS_TRIGGERS);

		/** Load Parent Classes first */
		$triggerClass = 'Molajo\\Extension\\Trigger\\Trigger\\Trigger';
		$method = 'getInstance';
		$triggerClass::$method();

		$triggerClass = 'Molajo\\Extension\\Trigger\\Content\\ContentTrigger';
		$method = 'getInstance';
		$triggerClass::$method();

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
	 * Instantiate the trigger class, register it for event(s), and save the path and name
	 *
	 * @param  $folder location of the trigger
	 *
	 * @return object
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
				//error
			}
		} else {
			$try = false;
			$connection = $triggerClass . ' Class does not exist';
			//error
		}

		/** make helper connection */
		if ($try === false) {
			return false;
		}

		try {
			$connection = $triggerClass::$method();

		} catch (\Exception $e) {
			$connection = 'Fatal Error: ' . $e->getMessage();
		}

		/** Save connection */
		$this->trigger_connection[strtolower($trigger)] = $triggerClass;

		/** Retrieve all Event Methods in the Trigger */
		$events = get_class_methods($triggerClass);

		if (count($events) > 0) {
			foreach ($events as $event) {
				if (substr($event, 0, 2) == 'on') {
					$reflectionMethod = new \ReflectionMethod(new $triggerClass, $event);
					$results = $reflectionMethod->getDeclaringClass();
					if ($results->name == $triggerClass) {
						$this->register($trigger, $triggerClass, $event);
					}
				}
			}
		}

		return $this;
	}
}
