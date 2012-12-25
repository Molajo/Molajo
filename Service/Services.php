<?php
namespace Molajo\Service;

use Molajo\Frontcontroller;
use Molajo\Service\Services\Configuration\ConfigurationService;

defined('NIAMBIE') or die;

//@todo make it easy to tell if a service is running or not
//@todo acl per service

/**
 * The Services Class serves as a facade and has been put in place for these reasons:
 *
 *  1) Simplify application interface for services for frontend developers
 *
 *  2) Guard against the impact of change by providing a cushioning layer
 *        where backwards compatibility better insured
 *
 *  3) Reduce interdependence between software within the application
 *
 *  4) Standarize API by removing vendor-specific namespacing/characteristics to establish a basic set
 *         of application utilities that provide basic functionality which can be supplied by different
 *         vendors without requiring change to the application itself
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 * @since        1.0
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
        if ($name == 'Registry') {
            return Frontcontroller::registry();
        }
        return Frontcontroller::Services()->get($name . 'Service');
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
    protected function get($key, $class='Molajo\\Service\\Services\\')
    {
        try {

            if (isset($this->connections[$key])) {
                return $this->connections[$key];
            }

            if ($class === null) {
                $class = 'Molajo\\Service\\Services\\';
            }

            $serviceClass = $class
                . substr($key, 0, strlen($key) - strlen('service'))
                . '\\'
                . $key;

            return $this->getServiceClassInstance($serviceClass);

        } catch (\Exception $e) {

            $trace = debug_backtrace();
            $caller = array_shift($trace);

            $error_message = "Called by {$caller['function']}";

            if (isset($caller['class'])) {
                $error_message .= " in {$caller['class']}";
            }

            throw new \BadMethodCallException($error_message);
        }
    }

    /**
     * instantiates services defined in the services.xml file and runs onBefore and onAfterStart Events for each
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
            throw new \RuntimeException('Cannot find Services File ');
        }

        foreach ($services->service as $service) {

            $static_indicator = (int)$service->attributes()->static;
            $name = (string)$service->attributes()->name;
            $startup = (string)$service->attributes()->startup;
            $class = (string)$service->attributes()->class;

            if ($class === null) {
                $class = 'Molajo\\Service\\Services\\';
            }

            $serviceClass = $class . $name . '\\' . $name . 'Service';
            $pluginClass = $class . $name . '\\' . $name . 'ServicePlugin';

            $connectionSucceeded = null;

            try {
                $pcConnection = $this->getPluginClassInstance($pluginClass);

                $scConnection = $this->getServiceClassInstance($serviceClass);

                $scConnection = $this->scheduleOnBeforeStartEvent($pcConnection, $pluginClass, $scConnection);

                if (trim($startup) == '' || ($static_indicator == 1 && $startup == 'getInstance')) {
                } else {
                    $connectionSucceeded = $this->runStartupMethod($scConnection, $name . 'Service', $startup);
                }

                $scConnection = $this->scheduleOnAfterStartEvent($pcConnection, $pluginClass, $scConnection);

                if ($static_indicator == 1) {
                    $this->set($name . 'Service', $scConnection, $connectionSucceeded);
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
     * Get Service Class Instance
     *
     * @param   string   $entry
     * @param   $folder  $entry
     *
     * @return  mixed
     * @since   1.0
     */
    private function getServiceClassInstance($serviceClass)
    {
        if (class_exists($serviceClass)) {
        } else {
            throw new \Exception('Service Class ' . $serviceClass . ' does not exist.');
        }

        return new $serviceClass();
    }

    /**
     * Get Plugin Class Instance
     *
     * @param   string   $entry
     * @param   $folder  $entry
     *
     * @return  mixed
     * @since   1.0
     */
    protected function getPluginClassInstance($pluginClass)
    {
        if (class_exists($pluginClass)) {
        } else {
            return;
        }

        return new $pluginClass();
    }

    /**
     * Schedule On Before Start Event - prior to instantiation of Services Class
     *
     * @param   string  $pcConnection
     * @param   string  $pluginClass
     * @param   string  $scConnection
     *
     * @return  mixed
     * @since   1.0
     */
    private function scheduleOnBeforeStartEvent($pcConnection, $pluginClass, $scConnection)
    {
        if (method_exists($pluginClass, 'onBeforeStart')) {
        } else {
            return $scConnection;
        }

        $pcConnection->set('service_class', $scConnection);
        $pcConnection->onBeforeStart();
        $scConnection = $pcConnection->get('service_class', $scConnection);

        return $scConnection;
    }

    /**
     * Execute Startup method
     *
     * @param   $scConnection
     * @param   $serviceClass
     * @param   $serviceMethod
     *
     * @return  mixed
     * @since   1.0
     */
    protected function runStartupMethod($scConnection, $serviceClass, $serviceMethod)
    {
        try {
            return $scConnection->$serviceMethod();

        } catch (\Exception $e) {

            $error = 'Service: ' . $serviceClass
                . ' Startup Method: ' . $serviceMethod
                . ' failed: ' . $e->getMessage();

            throw new \Exception($error);
        }
    }

    /**
     * Schedule On After Start Event - after instantiation of Services Class
     *
     * @param   string  $pcConnection
     * @param   string  $pluginClass
     * @param   string  $scConnection
     *
     * @return  mixed
     * @since   1.0
     */
    protected function scheduleOnAfterStartEvent($pcConnection, $pluginClass, $scConnection)
    {
        if (method_exists($pluginClass, 'onAfterStart')) {
        } else {
            return $scConnection;
        }

        $pcConnection->set('service_class', $scConnection);
        $pcConnection->onAfterStart();
        $scConnection = $pcConnection->get('service_class', $scConnection);

        return $scConnection;
    }

    /**
     * Store service connection locally
     *
     * Set indicator of Service availability in Registry
     *
     * @param   string  $key
     * @param   null    $value
     * @param   bool    $connectionSucceeded
     *
     * @return  mixed
     * @since   1.0
     * @throws  \Exception
     */
    protected function set($key, $value = null, $connectionSucceeded = true)
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
