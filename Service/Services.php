<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Service;

use Molajo\Application;
use Molajo\Service\Services\Configuration\ConfigurationService;

defined('MOLAJO') or die;

//todo make it easy to tell if a service is running or not
//todo acl per service

/**
 * Service
 *
 * The Services Class serves as a facade and has been put in place for these reasons:
 *
 *  1) Simplify application interface for services for frontend developers
 *
 *  2) Guard against the impact of change by providing a cushioning layer
 *        where change can be compensated and backwards compatability better insured
 *
 *  3) Reduce interdependence between software within the application
 *
 *  4) Standarize API by removing vendor-specific namespacing/characteritics to establish a basic set
 *         of application utilities that provide basic functionality which can be supplied by different
 *         vendors without requiring change to the application itself
 *
 * @return boolean
 * @since   1.0
 */
Class Services
{
	/**
	 * error_reporting(0);
	ini_set('display_errors', 0);
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
	protected $static_connection;


	/**
	 * Service Connections
	 *
	 * @var   object
	 * @since 1.0
	 */
	protected $dynamic_connection;

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
	public function initiate()
	{
		$this->static_connection = array();
		$this->dynamic_connection = array();
		$this->message = array();

		$services = ConfigurationService::getFile('Service', 'Services');
		if ($services === false) {
			//throw error
			//error
			echo 'Cannot file Services File ';
			die;
		}

		foreach ($services->service as $service) {

			if ($service->attributes()->scope == 'Application') {

				$name = $service->attributes()->name;

				foreach ($service->parameter as $parameter) {
				}
				$connectionSucceeded = true;
				$connection = '';

				/** class name */
				$entry = (string)$name . 'Service';
				$folder = (string)$name;
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
				if ($connectionSucceeded === true) {
					try {
						$connection = $serviceClass::$serviceMethod();

					} catch (\Exception $e) {
						$connectionSucceeded = false;
						$connection = 'Fatal Error: ' . $e->getMessage();
					}
				}

				/** store connection or error message */
				if ($connectionSucceeded === false) {
					echo 'service failed for ' . $entry . '<br />';
				}
				$this->set($entry, $connection, $connectionSucceeded);

			} elseif ($service->attributes()->scope == 'Instance') {

				$this->dynamic_connection[$service->attributes()->name . 'Service']
					= $service->attributes()->name;

			}

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
		if (isset($this->static_connection[$key])) {
			return $this->static_connection[$key];

		} elseif (isset($this->dynamic_connection[$key])) {
			$entry = $key;
			$folder = $this->dynamic_connection[$key];

		} else {
			throw new \BadMethodCallException('Service ' . $key . ' is not available');
		}

		$connectionSucceeded = true;
		$connection = '';

		/** class name */
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
		if ($connectionSucceeded === true) {
			try {
				$connection = $serviceClass::$serviceMethod();

			} catch (\Exception $e) {
				$connectionSucceeded = false;
				$connection = 'Fatal Error: ' . $e->getMessage();
			}
		}

		return $connection;
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

		if ($value == null || $connectionSucceeded === false) {
			$this->message[$i] = ' ' . $key . ' FAILED' . $value;
			Services::Registry()->set('Service', $key, false);

		} else {
			$this->static_connection[$key] = $value;
			$this->message[$i] = ' ' . $key . ' started successfully. ';
			Services::Registry()->set('Service', $key, true);
		}
	}
}
