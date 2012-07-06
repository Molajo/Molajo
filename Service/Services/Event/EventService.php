<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Service\Services\Event;

use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Event
 *
 * To list all Events:
 * Services::Registry()->get('Events', '*');
 *
 * To see what Triggers fire for a specific event:
 * Services::Registry()->get('onbeforeread', '*');
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
	 * Services::Event()->schedule('onAfterDelete', $arguments, $selections);
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
		Services::Profiler()->set('EventService->schedule Initiated Event '
			. $event, LOG_OUTPUT_TRIGGERS, VERBOSE);

		/** Does Event (with registration) exist? */
		$exists = Services::Registry()->exists('Events', $event);
		if ($exists == false) {
			Services::Profiler()->set('EventService->schedule Event: '
				. $event . ' does not exist', LOG_OUTPUT_TRIGGERS);
			return $arguments;
		}

		/** Retrieve Event Registrations */
		$registrations = Services::Registry()->get($event);
		if (count($registrations) == 0) {
			Services::Profiler()->set('EventService->schedule Event ' . $event
				. ' has no registrations, exiting', LOG_OUTPUT_TRIGGERS);
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

		/** Process each selected trigger */
		foreach ($selections as $selection) {

			$triggerClass = strtolower($selection) . 'trigger';

			if (isset($registrations[$triggerClass])) {

				if (method_exists($registrations[$triggerClass], $event)) {

					$results = $this->processTriggerClass(
						$registrations[$triggerClass], $event, $arguments);

					if ($results == false) {
						return false;
					}

				} else {

					Services::Profiler()->set('EventService->schedule Event '
							. $event . ' Class does not exist '
							. $registrations[$triggerClass],
						LOG_OUTPUT_TRIGGERS);

					return false;
					//throw error
				}

			} else {
				Services::Profiler()->set('EventService->schedule Event '
						. $event . ' No valid registrations for class '
						. $triggerClass,
					LOG_OUTPUT_TRIGGERS,
					VERBOSE
				);

				return $arguments;
			}
		}

		return $arguments;
	}

	/**
	 * processTriggerClass for Event given passed in arguments
	 *
	 * @param $class
	 * @param $event
	 * @param array $arguments
	 *
	 * @return array|bool
	 * @since  1.0
	 */
	protected function processTriggerClass($class, $event, $arguments = array())
	{
		/** 1. Instantiate Trigger Class */
		$triggerClass = $class;

		try {
			$connection = new $triggerClass();

		} catch (\Exception $e) {

			Services::Profiler()->set('EventService->schedule Event ' . $event
				. ' Instantiating Class ' . $triggerClass . ' Failed', LOG_OUTPUT_TRIGGERS);

			echo '<br />Could not Instantiate Trigger Class: ' . $triggerClass;

			return array('success' => true, $arguments);
			//throw error
		}

		/** 2. Set Properties for Trigger Class */
		if (count($arguments) > 0) {

			foreach ($arguments as $propertyKey => $propertyValue) {
				$connection->set($propertyKey, $propertyValue);
			}
			$connection->setFields();
		}

		/** 3. Execute Trigger Class Method */
		Services::Profiler()->set('EventService->schedule Event ' . $event
			. ' calling ' . $triggerClass . ' ' . $event, LOG_OUTPUT_TRIGGERS, VERBOSE);

		$results = $connection->$event();

		if ($results == false) {

			Services::Profiler()->set('EventService->schedule Event '
					. $event . ' Trigger Class '
					. $class
					. ' Failed. ',
				LOG_OUTPUT_TRIGGERS);

			return array('success' => true, $arguments);

		} else {

			/** Retrieve Properties from Trigger Class to send back to Controller */
			if (count($arguments) > 0) {
				foreach ($arguments as $propertyKey => $propertyValue) {
					$arguments[$propertyKey] = $connection->get($propertyKey, $propertyValue);
				}
			}
		}

		return array('success' => true, $arguments);
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
		Services::Profiler()->set('EventService->register '
				. 'Trigger: ' . $trigger
				. ' Class: ' . $triggerPath
				. ' Event: ' . $event,
			LOG_OUTPUT_TRIGGERS,
			VERBOSE
		);

		/** Register Event (if not already registered) */
		$exists = Services::Registry()->exists('Events', $event);

		/** Retrieve number of registrations or register new event */
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
		Services::Profiler()->set('EventService->registerInstalledTriggers ', LOG_OUTPUT_TRIGGERS, VERBOSE);

		$triggers = Services::Filesystem()->folderFolders(EXTENSIONS_TRIGGERS);

		/** Load Parent Classes first */
		$triggerClass = 'Molajo\\Extension\\Trigger\\Trigger\\Trigger';
		$temp = new $triggerClass ();

		$triggerClass = 'Molajo\\Extension\\Trigger\\Content\\ContentTrigger';
		$temp = new $triggerClass ();

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
