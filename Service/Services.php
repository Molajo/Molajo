<?php
/**
 * Services
 *
 * @package      Molajo
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service;

use Molajo\Frontcontroller;

defined('MOLAJO') or die;

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
 *  4) Standarise API by removing vendor-specific namespacing/characteristics to establish a basic set
 *         of application utilities that provide basic functionality which can be supplied by different
 *         vendors without requiring change to the application itself
 *
 * @package      Molajo
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
    protected $frontcontroller_instance = null;

    /**
     * Controller Class Name
     *
     * @var     object
     * @since   1.0
     */
    protected $controller_class = null;

    /**
     * List of Properties
     *
     * @var    object
     * @since  1.0
     */
    protected $property_array = array(
        'configuration',
        'controller_class',
        'frontcontroller_instance',
        'class_array'
    );

    /**
     * Get the current value (or default) of the specified key
     *
     * @param   string  $key
     * @param   mixed   $default
     *
     * @return  mixed
     * @since   1.0
     * @throws  \OutOfRangeException
     */
    public function get($key = null, $default = null)
    {
        $key = strtolower($key);

        if (in_array($key, $this->property_array)) {
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
     * @throws  \OutOfRangeException
     */
    public function set($key, $value = null)
    {
        $key = strtolower($key);

        if (in_array($key, $this->property_array)) {
        } else {
            throw new \OutOfRangeException
            ('Services: is attempting to set value for unknown key: ' . $key);
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
     * @throws  \RuntimeException
     */
    public function startup()
    {
        $services = $this->connections['ConfigurationService']->getFile('Services', 'Services');

        if ($services === null) {
            throw new \RuntimeException
            ('Services: Cannot find Services File Model Type: Service Model Name: Services');
        }

        foreach ($services->service as $service) {

            $name  = (string)$service->attributes()->name;
            $class = (string)$service->attributes()->class;

            if ($class === null) {
                $class = '';
            }
            $this->start($name, $class, 1);
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
     * @param   string  $service_class_name
     * @param   string  $service_class_namespace
     * @param   bool    $read_registry
     *
     * @return  mixed|object
     * @since   1.0
     * @throws  \Exception
     */
    public function start($service_class_name, $service_class_namespace = '', $read_registry = 0)
    {
        $service = substr($service_class_name, 0, strlen($service_class_name) - 7);
        $service = ucfirst(strtolower($service));

        $service_class_name = $service . 'Service';
        $plugin_class_name  = $service_class_name . 'Plugin';

        if (isset($this->connections[$service_class_name])) {
            return $this->connections[$service_class_name];
        }

        if ($service_class_namespace == '') {
            $service_class_namespace = $this->frontcontroller_instance->get_class_array($service_class_name);
            $read_registry           = 1;
            echo 'Requested by application: ' . $service_class_namespace . '<br />';
        } else {
            echo 'Request for ' . $service . '<br />';

        }

        if ($service_class_namespace == '') {
            $service_class_namespace = 'Molajo\\Service\\Services\\' . $service . '\\';
        }

        $plugin_class_namespace  = $service_class_namespace . $plugin_class_name;
        $service_class_namespace = $service_class_namespace . $service_class_name;

        if ((int)$read_registry == 0) {
            $startup       = 1;
            $keep_instance = 1;

        } else {
            $controller = new $this->controller_class();
            $controller->getModelRegistry('Service', $service);

            $startup       = $this->connections['RegistryService']->get($service . 'Service', 'startup');
            $keep_instance = $this->connections['RegistryService']->get($service . 'Service', 'keep_instance');

            $startup       = 1;
            $keep_instance = 1;
        }

        $connection_succeeded = null;

        try {

            $service_class_instance =
                $this->getServiceClassInstance($service_class_namespace);

            if ($service_class_instance === false) {

                if ($keep_instance == 1) {
                    $this->setServiceClassInstance(
                        $service_class_name,
                        $service_class_instance = null,
                        $connection_succeeded = false
                    );
                }

                return false;
            }

            $plugin_instance =
                $this->getPluginClassInstance($plugin_class_namespace);

            if ($plugin_instance === false) {

            } else {

                $results = $this->scheduleEvent(
                    $plugin_class_name,
                    $plugin_class_namespace,
                    $plugin_instance,
                    $service_class_name,
                    $service_class_namespace,
                    $service_class_instance,
                    'onBeforeServiceInitialise'
                );

                if ($results === false) {
                } else {
                    $service_class_instance = $results;
                }
            }

            $service_class_instance =
                $this->runServiceInitialiseMethod($service_class_instance, $service_class_namespace);

            if ($plugin_instance === false) {
            } else {

                $results = $this->scheduleEvent(
                    $plugin_class_name,
                    $plugin_class_namespace,
                    $plugin_instance,
                    $service_class_name,
                    $service_class_namespace,
                    $service_class_instance,
                    'onAfterServiceInitialise'
                );

                if ($results === false) {
                } else {
                    $service_class_instance = $results;
                }
            }

            if ($keep_instance == 1) {
                $this->setServiceClassInstance(
                    $service_class_name,
                    $service_class_instance,
                    1
                );
            }

            if ($plugin_instance === false
                || (int)$keep_instance == 0
            ) {
            } else {
                $this->scheduleEvent(
                    $plugin_class_name,
                    $plugin_class_namespace,
                    $plugin_instance,
                    $service_class_name,
                    $service_class_namespace,
                    $service_class_instance,
                    'OnAfterSaveServiceInstance'
                );
            }

            if ($plugin_instance === false) {
            } else {
                unset($plugin_instance);
            }

            if ($service_class_name == 'EventService') {
                $service_class_instance = $this->onAfterSaveEventService($service_class_instance);
            }

        } catch (\Exception $e) {

            $trace  = debug_backtrace();
            $caller = array_shift($trace);

            $error_message = "Called by {$caller['function']}";

            if (isset($caller['class'])) {
                $error_message .= " in {$caller['class']}";
            }

            throw new \Exception
            ('Service: Connection for ' . $service_class_name . ' failed.' . $error_message);
        }
        echo '' . 'Done ' . $service_class_name . '<br /><br /><br />';

        return $service_class_instance;
    }

    /**
     * Retrieve Saved Service Class Instance or Instantiate New One
     *
     * @param   string   $service_class_namespace
     *
     * @return  mixed
     * @since   1.0
     * @throws  \Exception
     */
    protected function getServiceClassInstance($service_class_namespace)
    {
        if (class_exists($service_class_namespace)) {

        } else {
            throw new \Exception
            ('Services: Failure Instantiating Class ' . $service_class_namespace . ' does not exist.');
        }

        return new $service_class_namespace();
    }

    /**
     * Execute Service initialise Method
     *
     * @param   string  $service_class_instance
     * @param   string  $service_class_namespace
     *
     * @return  object
     * @since   1.0
     * @throws \Exception
     */
    protected function runServiceInitialiseMethod($service_class_instance, $service_class_namespace)
    {
        try {

            if (method_exists($service_class_namespace, 'initialise')) {

                $results = $service_class_instance->initialise();

                if (is_object($results)) {
                    $service_class_instance = $results;
                }
            }

            return $service_class_instance;

        } catch (\Exception $e) {

            $error = 'Service: ' . $service_class_namespace
                . ' Startup Method: initialise '
                . ' failed: ' . $e->getMessage();

            throw new \Exception($error);
        }
    }

    /**
     * Store service connection locally
     *
     * @param   string  $service_class_name
     * @param   null    $service_class_instance
     * @param   bool    $connection_succeeded
     *
     * @return  null
     * @since   1.0
     * @throws  \Exception
     */
    protected function setServiceClassInstance(
        $service_class_name,
        $service_class_instance = null,
        $connection_succeeded = true
    ) {
        $i = count($this->message);

        if ($service_class_instance == null || $connection_succeeded === false) {

            $this->message[$i] = ' ' . $service_class_name . ' FAILED' . $service_class_instance;
            if ($service_class_name == 'ConfigurationService' || $service_class_name == 'RegistryService') {
            } else {
                $this->connections['RegistryService']->set('Service', $service_class_name, false);
            }

        } else {

            $this->connections[$service_class_name] = $service_class_instance;
            $this->message[$i]                      = ' ' . $service_class_name . ' started successfully. ';
            if ($service_class_name == 'ConfigurationService' || $service_class_name == 'RegistryService') {
            } else {
                $this->connections['RegistryService']->set('Service', $service_class_name, true);
            }
        }

        return;
    }

    /**
     * Unset a Service Class
     *
     * @param   string  $service_class_name
     *
     * @return  mixed
     * @since   1.0
     */
    public function unsetServiceClassInstance($service_class_name)
    {
        $key = ucfirst(strtolower($service_class_name));

        if (isset($this->connections[$service_class_name])) {
            $temp = $this->connections[$service_class_name];
            unset($temp);
            unset($this->connections[$service_class_name]);
        }

        return;
    }

    /**
     * Get Plugin Class Instance
     *
     * @param   string   $plugin_class_namespace
     *
     * @return  bool
     * @since   1.0
     */
    protected function getPluginClassInstance($plugin_class_namespace)
    {
        if (class_exists($plugin_class_namespace)) {
        } else {

            /** Not an error as plugins are not required for Services */
            return false;
        }

        return new $plugin_class_namespace();
    }

    /**
     * TODO: Figure out sane way to make the following a plugin method
     */

    /**
     * Schedule On Before Start Event - prior to instantiation of Services Class
     *
     * @param  string  $plugin_class_name
     * @param  string  $plugin_class_namespace
     * @param  string  $plugin_instance
     * @param  string  $service_class_name
     * @param  string  $service_class_namespace
     * @param  string  $service_class_instance
     * @param  string  $event
     *
     * @return  bool
     * @since   1.0
     */
    protected function scheduleEvent(
        $plugin_class_name,
        $plugin_class_namespace,
        $plugin_instance,
        $service_class_name,
        $service_class_namespace,
        $service_class_instance,
        $event
    ) {
        if (method_exists($plugin_class_namespace, $event)) {
        } else {
            return false;
        }

        $reflectionMethod = new \ReflectionMethod(new $plugin_class_namespace, $event);
        $results          = $reflectionMethod->getDeclaringClass();

        if ($results->name == $plugin_class_namespace) {
        } else {
            return false;
        }

        if (isset($this->connections['DateService'])) {
            $current_date = $this->connections['DateService']->getDate();
        } else {
            $temp         = new \DateTime('now');
            $current_date = $temp->format('Y-m-d H:i:s');
        }

        $plugin_instance->set('current_date', $current_date);

        $plugin_instance->set('service_class_name', $service_class_name);
        $plugin_instance->set('service_class_namespace', $service_class_namespace);
        $plugin_instance->set('service_class_instance', $service_class_instance);

        $plugin_instance->set('plugin_class_name', $plugin_class_name);
        $plugin_instance->set('plugin_event', $event);

        $plugin_instance->set('frontcontroller_instance', $this->get('frontcontroller_instance'));

        $plugin_instance->$event();

        $service_class_instance = $plugin_instance->get('service_class_instance', $service_class_instance);

        return $service_class_instance;
    }

    /**
     * FOR NOW: After Event
     *
     * Follows the completion of the start method defined in the configuration
     *
     * @param   string  $service_class_instance
     *
     * @return  array|bool
     * @since   1.0
     */
    public function onAfterSaveEventService($service_class_instance)
    {
        if (defined('PROFILER_ON') && PROFILER_ON === true) {
            Services::Profiler()->set(
                'message',
                'Event Service: registerInstalledPlugins for Extension and Core',
                'Plugins',
                1
            );
        }

        $service_class_instance = $this->registerPlugins(
            PLATFORM_FOLDER . '/' . 'Plugin',
            'Molajo\\Plugin\\',
            $service_class_instance
        );

        $service_class_instance = $this->registerPlugins(
            EXTENSIONS . '/' . 'Plugin',
            'Extension\\Plugin\\',
            $service_class_instance
        );

        return $service_class_instance;
    }

    /**
     * onBeforeRegisterPlugin determines a set of plugins from a folder and namespace and then
     * returns a set of plugins, plugin class values, and namespaces for which the visitor is authorised
     *
     * @param   string  $folder
     * @param   string  $namespace
     * @param   string  $service_class_instance
     *
     * @return  array
     * @since   1.0
     * @throws  \Exception
     */
    public function registerPlugins($folder, $namespace, $service_class_instance)
    {
        if ($folder == '') {
            throw new \Exception ('Event Service: No folder sent into RegisterPlugins');
        }

        if ($namespace == '') {
            throw new \Exception ('Event Service: No namespace sent into RegisterPlugins');
        }

        $folders_and_files = scandir($folder);
        if (count($folders_and_files) == 0) {
            return array();
        }

        $plugin_folders = array();

        foreach ($folders_and_files as $key => $value) {
            if ($value == '.') {

            } elseif ($value == '..') {

            } elseif (is_dir($folder . '/' . $value)) {
                $plugin_folders[] = $value;
            }
        }

        if (count($plugin_folders) == 0 || $plugin_folders === false) {
            return array();
        }
// deal with authorisation - might have to deal with it when using the plugin due to timing issues
//$authorised_plugins = array();

//        $authorised = Services::User()->get('authorised_extension_titles');


//        if ($authorised === false) {
//            $authorised = array();
//        }
        $authorised = array();
        foreach ($plugin_folders as $plugin_folder) {

            $plugin_name = ucfirst(strtolower($plugin_folder)) . 'Plugin';

            if (substr(strtolower($plugin_folder), 0, 4) == 'hold') {

            } elseif (in_array($plugin_folder, $authorised) || count($authorised) == 0) {

                $this->connections['RegistryService']->deleteRegistry($plugin_name);
                $plugin_class_name = $namespace . $plugin_folder . '\\' . $plugin_name;

                $controllerClass = CONTROLLER_CLASS_NAMESPACE;
                $controller      = new $controllerClass();
                $controller->getModelRegistry('Plugin', ucfirst(strtolower($plugin_folder)), 0);

                $temp                    = new \stdClass();
                $temp->plugin_name       = $plugin_name;
                $temp->plugin_class_name = $plugin_class_name;

                $service_class_instance =
                    $this->connections['EventService']->registerPlugin($plugin_name, $plugin_class_name);

//$authorised_plugins[] = $temp;
            }
        }

        return $service_class_instance;
    }
}
