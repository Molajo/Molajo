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
 *        where backwards compatibility better insured
 *
 *  3) Reduce interdependence between software within the application
 *
 *  4) Standarise API by removing vendor-specific namespacing/characteristics to establish a basic set
 *         of application utilities that provide basic functionality which can be supplied by different
 *         vendors without requiring change to the application itself
 *
 * @return  boolean
 * @since   1.0
 */
Class Services
{

    /**
     * Stores messages locally until the Profiler Service has been activated
     *
     * @var     object
     * @since   1.0
     */
    protected $message;

    /**
     * Service Connections
     *
     * @var     object
     * @since   1.0
     */
    protected $connections;

    /**
     * Registry
     *
     * @var     object
     * @since   1.0
     */
    protected $registry;

    /**
     * Used to connect to service either dynamically or reuse of an existing connection
     *
     * @static
     * @param   string  $name
     * @param   array   $arguments
     *
     * @return  object
     * @since   1.0
     */
    public static function __callStatic($name, $arguments)
    {
        return Application::Services()->get($name . 'Service');
    }

    /**
     * Retrieves Service Connection or Connects Service
     *
     * @param   string $key
     *
     * @return  mixed
     * @since   1.0
     *
     * @throws  \BadMethodCallException
     */
    protected function get($key)
    {
        try {

            if (isset($this->connections[$key])) {
                return $this->connections[$key];
            }

            $serviceClass = 'Molajo\\Service\\Services\\'
                . substr($key, 0, strlen($key) - strlen('service'))
                . '\\'
                . $key;

            return $this->getClassInstance($serviceClass);

        } catch (\Exception $e) {

            $trace = debug_backtrace();
            $caller = array_shift($trace);

            $error_message = "Called by {$caller['function']}";

            if (isset($caller['class'])) {
                $error_message .= " in {$caller['class']}";
            }

            throw new \Exception($error_message);
        }
    }

    /**
     * loads all services defined in the services.xml file
     *
     * @return  boolean
     * @since   1.0
     */
    public function initiate()
    {
        $this->connections = array();
        $this->message = array();

        $services = ConfigurationService::getFile('Service', 'Services');

        if ($services === false) {
            //throw error
            //error
            echo 'Cannot file Services File ';
            die;
        }

        foreach ($services->service as $service) {

            $static_indicator = (int)$service->attributes()->static;
            $name = (string)$service->attributes()->name;
            $startup = (string)$service->attributes()->startup;
            $store_connection = (int)$service->attributes()->store_connection;

            $serviceClass = 'Molajo\\Service\\Services\\' . $name . '\\' . $name . 'Service';

            $connectionSucceeded = null;
            echo $serviceClass . '<br />';
            try {
                $connection = $this->getClassInstance($serviceClass);

                if (trim($startup) == '') {
                } else {
                    $connectionSucceeded = $this->runStartupMethod($connection, $name . 'Service', $startup);
                }

                if ($store_connection == 1 || $static_indicator == 1) {
                    $this->set($name . 'Service', $connection, $connectionSucceeded);
                }

            } catch (\Exception $e) {
                throw new \Exception('Service Connection for ' . $name . ' failed.' . $e->getMessage(), $e->getCode());
            }
        }

        foreach ($this->message as $message) {
            Services::Profiler()->set($message, PROFILER_SERVICES, VERBOSE);
        }

        return true;
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
    private function getClassInstance($serviceClass)
    {
        if (class_exists($serviceClass)) {
        } else {
            throw new \Exception('Service Class ' . $serviceClass . ' does not exist.');
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

            $error = 'Service: ' . $serviceClass
                . ' Startup Method: ' . $serviceMethod
                . ' failed: ' . $e->getMessage();

            throw new \Exception($error);
        }
    }

    /**
     * Store service connection locally
     *
     * Set indicator of Service availability in Registry
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
            Services::Registry()->set('Service', $key, false);

        } else {
            $this->connections[$key] = $value;
            $this->message[$i] = ' ' . $key . ' started successfully. ';
            Services::Registry()->set('Service', $key, true);
        }
    }
}
