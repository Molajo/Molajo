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
    protected $message = array();

    /**
     * Service Connections
     *
     * @var     object
     * @since   1.0
     */
    protected $connections = array();

    /**
     * Front Controller
     *
     * @var     object
     * @since   1.0
     */
    protected $frontcontroller_class = null;

    /**
     * Controller Class Name
     *
     * @var     object
     * @since   1.0
     */
    protected $controller_class = null;

    /**
     * Stores an array of key/value Parameters settings from Route
     *
     * @var    array
     * @since  1.0
     */
    protected $parameters = array();

    /**
     * List of Properties
     *
     * @var    object
     * @since  1.0
     */
    protected $properties_array = array(
        'configuration',
        'controller_class',
        'frontcontroller_class'
    );

    /**
     * Get the current value (or default) of the specified key
     *
     * @param   string  $key
     * @param   mixed   $default
     *
     * @return  mixed
     * @since   1.0
     */
    public function get($key = null, $default = null)
    {
        $key = strtolower($key);

        if (in_array($key, $this->properties_array)) {
        } else {
            throw new \OutOfRangeException('Services: is attempting to get value for unknown key: ' . $key);
        }

        if ($this->$key === null) {
            $this->$key = $default;
        }

        return $this->$key;
    }

    /**
     * Set the value of a specified key
     *
     * @param   string  $key
     * @param   mixed   $value
     *
     * @return  mixed
     * @since   1.0
     */
    public function set($key, $value = null)
    {
        $key = strtolower($key);

        if (in_array($key, $this->properties_array)) {
        } else {
            throw new \OutOfRangeException('Services: is attempting to set value for unknown key: ' . $key);
        }

        $this->$key = $value;

        return $this->$key;
    }

    /**
     * Entry point for services called outside of the Services Class
     *
     * Note: The Services Class is a static connection to the FrontController. The Services, themselves,
     *  are new or saved instances. The purpose of the static call is to creates a entry point and a facade
     *  to simplify frontend developer access and to provide a single point of entry for all services calls.
     *  This entry point will make easier the job of managing backwards compatible support.
     *
     * @static
     *
     * @param   string  $name
     * @param   array   $arguments
     *
     * @return  object
     * @since   1.0
     */
    public static function __callStatic($name, $arguments)
    {
        return Frontcontroller::Services()->start($name . 'Service');
    }

    /**
     * Prior to startup, the Front Controller starts the Registry Service, followed by the Configuration Service
     * Next, startup is invoked to instantiate service classes defined in the services.xml file
     *
     * @return  boolean
     * @since   1.0
     */
    public function startup()
    {
        $services = $this->connections['ConfigurationService']->getFile('Services', 'Services');

        if ($services === null) {
            throw new \RuntimeException
                ('Cannot find Services File Model Type: Service Model Name: Services');
        }

        foreach ($services->service as $service) {

            $name  = (string)$service->attributes()->name;
            $class = (string)$service->attributes()->class . $name . '\\';

            if ($class === null) {
                $class = 'Molajo\\Service\\Services\\';
            }
            echo 'Startup ' . $name . ' ' . $class . '<br />';
            $this->start($name, $class, true);
        }

        foreach ($this->message as $message) {
            Services::Profiler()->set('message', $message, 'Services', 1);
        }

        return true;
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
     * @return  null
     * @since   1.0
     *
     * @throws  \BadMethodCallException
     */
    public function start($key, $class = '', $registry = false)
    {

        if ($class == '') {
            $class = 'Molajo\\Service\\Services\\' . substr($key, 0, (strlen($key) - 7)) . '\\';
        }

//        echo '<pre>Here is $this->connections';
//        var_dump($this->connections);
//echo '</pre>';
        if (isset($this->connections[$key])) {
            return $this->connections[$key];
        }

//        if (isset($this->connections['ConfigurationService'])
//            && isset($this->connections['RegistryService'])) {
//            $registry = true;
//        }

        if ($registry == false) {

            $keep_instance  = 1;
            $name           = $key;
            $startup        = 1;
            $startup_method = 'initialise';
            $class          = $class;
            $keep_instance  = 1;

        } else {

            $controller      = new $this->controller_class();
            $controller->getModelRegistry('Service', $key);

            $this->connections['RegistryService']->get($key . 'Service', '*');
            die;

            $name           = (string)$service->attributes()->name;
            $startup        = (string)$service->attributes()->startup;
            $startup_method = (string)$service->attributes()->startup_method;
            $class          = (string)$service->attributes()->class;
            $keep_instance  = (int)$service->attributes()->keep_instance;
        }

        $serviceClass = $class . $name;
        $pluginClass  = $class . $name . 'Plugin';

        $connectionSucceeded = null;

        try {

            if (isset($this->connections[$key])) {
                return $this->connections[$key];
            }

            $pluginInstance =
                $this->getPluginClassInstance($pluginClass);

            $serviceInstance =
                $this->getServiceClassInstance($serviceClass);

            $pluginInstance->set('service_class', $serviceClass);

            $pluginInstance->set('frontcontroller_class', $this->get('frontcontroller_class'));

            $serviceInstance =
                $this->scheduleOnBeforeStartEvent($pluginInstance, $pluginClass, $serviceInstance);

            if (trim($startup_method) == '') {

            } else {
                $connectionSucceeded
                    = $this->runStartupMethod($serviceInstance, $serviceClass, $startup_method);
            }

            $serviceInstance
                = $this->scheduleOnAfterStartEvent($pluginInstance, $pluginClass, $serviceInstance);

            if ($keep_instance == 1) {
                $this->saveServiceClassInstance($name, $serviceInstance, $connectionSucceeded);
            }

        } catch (\Exception $e) {

            $trace  = debug_backtrace();
            $caller = array_shift($trace);

            $error_message = "Called by {$caller['function']}";

            if (isset($caller['class'])) {
                $error_message .= " in {$caller['class']}";
            }

            throw new \Exception
            ('Service: Connection for ' . $name . ' failed.' . $e->getMessage(), $e->getCode());
        }

        return;
    }

    /**
     * Retrieve Saved Service Class Instance or Instantiate New One
     *
     * @param   string   $serviceClass
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
     * @param   string   $pluginClass
     * @param   string   $folder  $entry
     *
     * @return  mixed
     * @since   1.0
     */
    protected function getPluginClassInstance($pluginClass)
    {
        if (class_exists($pluginClass)) {
        } else {

            /** Not an error as plugins are not required for Services */
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
        if (method_exists($pluginClass, 'onBeforeServiceStartup')) {
        } else {
            return $serviceInstance;
        }

        $pluginInstance->set('service_class', $serviceInstance);
        $pluginInstance->onBeforeServiceStartup();
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
            if (method_exists($serviceClass, $serviceMethod)) {
                return $serviceInstance->$serviceMethod();

            } else {
                $error = 'Service: ' . $serviceClass
                    . ' Startup Method: ' . $serviceMethod
                    . ' does not exist.';

                throw new \Exception($error);
            }

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
        if (method_exists($pluginClass, 'onAfterServiceStartup')) {
        } else {
            return $serviceInstance;
        }

        $pluginInstance->set('service_class', $serviceInstance);
        $pluginInstance->onAfterServiceStartup();
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
     * @return  null
     * @since   1.0
     * @throws  \Exception
     */
    protected function saveServiceClassInstance($key, $value = null, $connectionSucceeded = true)
    {
        $i = count($this->message);

        if ($value == null || $connectionSucceeded === false) {
            $this->message[$i] = ' ' . $key . ' FAILED' . $value;
            if ($key == 'ConfigurationService' || $key == 'RegistryService') {
            } else {
                $this->connections['RegistryService']->set('Service', $key, false);
            }

        } else {
            $this->connections[$key] = $value;
            $this->message[$i]       = ' ' . $key . ' started successfully. ';
            if ($key == 'ConfigurationService' || $key == 'RegistryService') {
            } else {
                echo $key . '<br />';
                $this->connections['RegistryService']->set('Service', $key, true);
            }
        }

        return;
    }
}
