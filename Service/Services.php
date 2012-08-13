<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Service;

use Molajo\Application;

defined('MOLAJO') or die;

//todo make it easy to tell if a service is running or not
//todo acl per service

/**
 * Service
 *
 * @package     Molajo
 * @subpackage  Services
 * @since       1.0
 */
Class Services
{
	/**
	 * Static instance
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected static $instance;

	/**
	 * Stores messages locally until the Profiler Service has been activated
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected $message;

	/**
	 * Service Connections
	 *
	 * @var   object
	 * @since 1.0
	 */
	protected $service_connection;

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
			self::$instance = new Services();
		}

		return self::$instance;
	}

	/**
	 * Used to connect to services
	 *
	 * @static
	 * @param  $name
	 * @param  $arguments
	 *
	 * @return object
	 * @since  1.0
	 */
	public static function __callStatic($name, $arguments)
	{
		return Application::Services()->get($name . 'Service');
	}

	/**
	 * loads all services defined in the services.xml file
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function startServices()
	{
		/** store service connections for use, as needed, by the application */
		$this->service_connection = array();

		/** store connection messages */
		$this->message = array();

		/** start services in this sequence */
		$services = simplexml_load_file(CONFIGURATION_FOLDER . '/Application/services.xml');

		foreach ($services->service as $item) {
			$connectionSucceeded = true;
			$connection = '';

			/** class name */
			$entry = (string)$item . 'Service';
			$folder = (string)$item;
			$serviceClass = 'Molajo\\Service\\Services\\' . $folder . '\\' . $entry;

			/** method name */
			$serviceMethod = 'getInstance';

			/** trap errors for missing class or method */
			if (class_exists($serviceClass)) {
				if (method_exists($serviceClass, $serviceMethod)) {

				} else {
					$connectionSucceeded = false;
					$connection = $serviceClass . '::' . $serviceMethod . ' Class Method does not exist';
				}
			} else {
				$connectionSucceeded = false;
				$connection = $serviceClass . ' Class does not exist';
			}

			/** make service connection */
			if ($connectionSucceeded == true) {
				try {
					$connection = $serviceClass::$serviceMethod();

				} catch (\Exception $e) {
					$connectionSucceeded = false;
					$connection = 'Fatal Error: ' . $e->getMessage();
				}
			}

			/** store connection or error message */
			if ($connectionSucceeded == false) {
				echo 'service failed for ' . $entry . '<br />';
			}
			$this->set($entry, $connection, $connectionSucceeded);

		}

		foreach ($this->message as $message) {
			Services::Profiler()->set($message, LOG_OUTPUT_SERVICES, VERBOSE);
		}

		return true;
	}

	/**
	 * Retrieves service key value pair
	 *
	 * @param string $key
	 *
	 * @return mixed
	 * @since   1.0
	 *
	 * @throws \BadMethodCallException
	 */
	protected function get($key)
	{
		if (isset($this->service_connection[$key])) {
			return $this->service_connection[$key];
		}

		throw new \BadMethodCallException('Service ' . $key . ' is not available');
	}

	/**
	 * Stores the service connection
	 *
	 * @param $key
	 * @param null $value
	 * @param bool $connectionSucceeded
	 *
	 * @return mixed
	 * @since   1.0
	 */
	private function set($key, $value = null, $connectionSucceeded = true)
	{
		$i = count($this->message);

		if ($value == null || $connectionSucceeded == false) {
			$this->message[$i] = ' ' . $key . ' FAILED' . $value;
			Services::Registry()->set('Service', $key, false);

		} else {
			$this->service_connection[$key] = $value;
			$this->message[$i] = ' ' . $key . ' started successfully. ';
			Services::Registry()->set('Service', $key, true);
		}

		echo $this->message[$i] . '<br />';

	}
}
