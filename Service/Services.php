<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Service;

use Molajo\Application;

defined('MOLAJO') or die;

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
	 * __construct
	 *
	 * @return null
	 * @since  1.0
	 */
	public function __construct()
	{
		$this->service_connection = array();
	}

	/**
	 * Retrieves service key value pair
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
		if (isset($this->service_connection[$key])) {
			return $this->service_connection[$key];
		} else {
			//error
		}
	}

	/**
	 * Used to connect to services
	 *
	 * @static
	 * @param  $name
	 * @param  $arguments
	 */
	public static function __callStatic($name, $arguments)
	{
		return Application::Services()->get($name . 'Service');
	}

	/**
	 * loads all services defined in the services.xml file
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	public function startServices()
	{
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
					echo $connection;
				}
			} else {
				$connectionSucceeded = false;
				$connection = $serviceClass . ' Class does not exist';
				echo $connection;
			}

			/** make service connection */
			if ($connectionSucceeded == true) {
				try {
//echo $serviceClass.' '.$serviceMethod.'<br />';
					$connection = $serviceClass::$serviceMethod();

				} catch (\Exception $e) {
					$connectionSucceeded = false;
					$connection = 'Fatal Error: ' . $e->getMessage();
				}
			}

			/** store connection or error message */
			if ($connectionSucceeded == false) {
				echo 'service failed for '. $entry.'<br />';
			}
			$this->set($entry, $connection, $connectionSucceeded);

		}

		foreach ($this->message as $message) {
			Services::Debug()->set($message);
		}

		return true;
	}

	/**
	 * set
	 *
	 * Stores the service connection
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 *
	 * @return  mixed
	 * @since   1.0
	 */
	private function set($key, $value = null, $connectionSucceeded = true)
	{
		$i = count($this->message);

		if ($value == null || $connectionSucceeded == false) {
			$this->message[$i] = 'Service: ' . $key . ' FAILED' . $value;

		} else {
			$this->service_connection[$key] = $value;
			$this->message[$i] = 'Service: ' . $key . ' started successfully. ';
		}
	}
}
