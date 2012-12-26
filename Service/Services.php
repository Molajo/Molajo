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
     * instantiates services defined in the services.xml file and runs onBefore and onAfterStart Events for each
     *
     * @return  boolean
     * @since   1.0
     */
    public function startup()
    {
        $this->connections = array();
        $this->message = array();

        $services = ConfigurationService::getFile('Service', 'Services');

        if ($services === false) {
            throw new \RuntimeException
            ('Cannot find Services File Model Type: Service Model Name: Services');
        }

        foreach ($services->service as $service) {

            $name = (string)$service->attributes()->name;
            $startup = (string)$service->attributes()->startup;
            $class = (string)$service->attributes()->class;

            if ($class === null) {
                $class = 'Molajo\\Service\\Services\\';
            }

            if ((int) $startup == 0) {
            } else {
                $this->get($name, $class);
            }
        }

        foreach ($this->message as $message) {
            Services::Profiler()->set($message, PROFILER_SERVICES, VERBOSE);
        }

        return true;
    }

    /**
     * Entry point for services called outside of the Services Class
     *
     * Note: The Services Class is a static connection to the FrontController. The Services, themselves,
     *  are rarely static. The purpose of the static call is to creates a Facade in order to simplify frontend
     *  developer access and to provide a single point of entry for all services calls. This single entry
     *  point should make it easier to manage backwards compatible support.
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
        /** Registry is accessed through the Front Controller */
        if ($name == 'Registry') {
            return Frontcontroller::registry();
        }

        /** All other Services route back to the Services->get() method */
        return Frontcontroller::Services()->get($name . 'Service');
    }

    /**
     * Retrieves Service Connection or Connects Service
     *
     * Method used in two ways:
     * 1. Services::Name()-> Call routes static through __callStatic then in through the Frontcontroller
     * 2. Services Instantiation processes startup Services using this Method once for each
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
        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();
        $controller->getModelRegistry('Services', $key);

        Services::Registry()->get($key . 'Services', '*');
        die;

        $static_indicator = (int)$service->attributes()->static;
        $name = (string)$service->attributes()->name;
        $startup = (string)$service->attributes()->startup;
        $startup_method = (string)$service->attributes()->startup_method;
        $class = (string)$service->attributes()->class;

        if ($class === null) {
            $class = 'Molajo\\Service\\Services\\';
        }

        $serviceClass = $class . $name . '\\' . $name . 'Service';
        $pluginClass = $class . $name . '\\' . $name . 'ServicePlugin';

        $connectionSucceeded = null;

        try {
            $pluginInstance =
                $this->getPluginClassInstance($pluginClass);

            $serviceInstance =
                $this->getServiceClassInstance($serviceClass);

            $serviceInstance =
                $this->scheduleOnBeforeStartEvent($pluginInstance, $pluginClass, $serviceInstance);

            if (trim($startup_method) == ''
                || ($static == 1 && $startup_method == 'getInstance')) {

            } else {
                $connectionSucceeded =
                    $this->runStartupMethod($serviceInstance, $name . 'Service', $startup_method);
            }

            $serviceInstance
                = $this->scheduleOnAfterStartEvent($pluginInstance, $pluginClass, $serviceInstance);

            if ($static == 1) {
                $this->set($name . 'Service', $serviceInstance, $connectionSucceeded);
            }

        } catch (\Exception $e) {
            throw new \Exception
            ('Service: Connection for ' . $name . ' failed.' . $e->getMessage(), $e->getCode());
        }

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
     * Get Service Class Instance
     *
     * @param   string   $entry
     * @param   $folder  $entry
     *
     * @return  mixed
     * @since   1.0
     */
    protected function getServiceClassInstance($serviceClass)
    {
        if (class_exists($serviceClass)) {
        } else {
            throw new \Exception
            ('Services: Class ' . $serviceClass . ' does not exist.');
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

            /** Plugins are not required for Services */
            return;
        }

        return new $pluginClass();
    }

    /**
     * Schedule On Before Start Event - prior to instantiation of Services Class
     *
     * @param   string  $pluginInstance
     * @param   string  $pluginClass
     * @param   string  $serviceInstance
     *
     * @return  mixed
     * @since   1.0
     */
    protected function scheduleOnBeforeStartEvent($pluginInstance, $pluginClass, $serviceInstance)
    {
        if (method_exists($pluginClass, 'onBeforeStart')) {
        } else {
            return $serviceInstance;
        }

        $pluginInstance->set('service_class', $serviceInstance);
        $pluginInstance->onBeforeStart();
        $serviceInstance = $pluginInstance->get('service_class', $serviceInstance);

        return $serviceInstance;
    }

    /**
     * Execute Startup method
     *
     * @param   string  $serviceInstance
     * @param   string  $serviceClass
     * @param   string  $serviceMethod
     *
     * @return  mixed
     * @since   1.0
     */
    protected function runStartupMethod($serviceInstance, $serviceClass, $serviceMethod)
    {
        try {
            return $serviceInstance->$serviceMethod();

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
     * @param   string  $pluginInstance
     * @param   string  $pluginClass
     * @param   string  $serviceInstance
     *
     * @return  mixed
     * @since   1.0
     */
    protected function scheduleOnAfterStartEvent($pluginInstance, $pluginClass, $serviceInstance)
    {
        if (method_exists($pluginClass, 'onAfterStart')) {
        } else {
            return $serviceInstance;
        }

        $pluginInstance->set('service_class', $serviceInstance);
        $pluginInstance->onAfterStart();
        $serviceInstance = $pluginInstance->get('service_class', $serviceInstance);

        return $serviceInstance;
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
