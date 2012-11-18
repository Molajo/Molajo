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
		return Application::Services()->get($name . CATALOG_TYPE_SERVICE_VIEW_LITERAL);
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

		$services = ConfigurationService::getFile(CATALOG_TYPE_SERVICE_VIEW_LITERAL, 'Services');

		if ($services === false) {
			//throw error
			//error
			echo 'Cannot file Services File ';
			die;
		}

		foreach ($services->service as $service) {

            $static_indicator = (int) $service->attributes()->static;
            $name = (string) $service->attributes()->name;
            $startup = (string) $service->attributes()->startup;

//todo: overrides for service
            $serviceClass = 'Molajo\\Service\\Services\\' . $name . '\\' . $name . CATALOG_TYPE_SERVICE_VIEW_LITERAL;

            foreach ($service->parameter as $parameter) {
            }

			if ($static_indicator == '1') {

                $connection = $this->getClassInstance($serviceClass);
                $connectionSucceeded = $this->runStartupMethod($connection, $name . CATALOG_TYPE_SERVICE_VIEW_LITERAL, $startup);

				$this->set($name . CATALOG_TYPE_SERVICE_VIEW_LITERAL, $connection, $connectionSucceeded);

			} else {

                $this->dynamic_connection[$service->attributes()->name . CATALOG_TYPE_SERVICE_VIEW_LITERAL]
                    = $service->attributes()->name;

                if (trim($startup) == '') {
                } else {
                    $connection = $this->getClassInstance($serviceClass);
                    $connectionSucceeded = $this->runStartupMethod($connection, $name . CATALOG_TYPE_SERVICE_VIEW_LITERAL, $startup);
                }
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
//todo: overrides for service
            return $this->getClassInstance('Molajo\\Service\\Services\\'
                    . substr($key, 0, (strlen($key) - 7))
                    . '\\' . $key);
		}

		throw new \BadMethodCallException('Service ' . $key . ' is not available');
	}

    /**
     * Get Class Instance
     *
     * @param   string  $entry
     * @param   $folder $entry
     *
     * @return  mixed
     * @since   1.0
     */
    private function getClassInstance($serviceClass) {

        if (class_exists($serviceClass)) {
        } else {
            $connectionSucceeded = false;
            $connection = $serviceClass . ' Class does not exist';
            //throw error
        }

        return new $serviceClass();
    }

    /**
     * Execute Startup method
     *
     * @param   $connection
     * @param   $serviceClass
     * @param   $serviceMethod
     *
     * @return  mixed
     * @since   1.0
     */
    private function runStartupMethod($connection, $serviceClass, $serviceMethod)
    {
        try {
            return $connection->$serviceMethod();

        } catch (\Exception $e) {
            $connectionSucceeded = false;
            $error = 'Fatal Error: ' . $e->getMessage();
            echo $error;
            die;
        }
    }

    /**
     * Stores static service connections
     *
     * Oh, get off your high horse. The minor use of static connections here is perfectly
     * valid, makes it easier for less technical people to use the application resources
     * and it support a rich environment for integrating resource data.
     *
     * If you have specific ideas on services that would be better implemented as dynamic
     * connections your pull request will get serious consideration.
     *
     * @param   string $key
     * @param   null   $value
     * @param   bool   $connectionSucceeded
     *
     * @return  mixed
     * @since   1.0
     */
    private function set($key, $value = null, $connectionSucceeded = true)
    {
        $i = count($this->message);

        if ($value == null || $connectionSucceeded === false) {
            $this->message[$i] = ' ' . $key . ' FAILED' . $value;
            Services::Registry()->set(CATALOG_TYPE_SERVICE_VIEW_LITERAL, $key, false);

        } else {
            $this->static_connection[$key] = $value;
            $this->message[$i] = ' ' . $key . ' started successfully. ';
            Services::Registry()->set(CATALOG_TYPE_SERVICE_VIEW_LITERAL, $key, true);
        }
    }
}
