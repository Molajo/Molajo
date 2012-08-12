<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
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
 * To see what Plugins fire for a specific event:
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
     * Registered plugins
     *
     * @var   object
     * @since 1.0
     */
    protected $plugin_connection;

    /**
     * Arguments
     *
     * @var   array of objects and values
     * @since 1.0
     */
    protected $arguments;

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

        $this->registerInstalledPlugins();
    }

    /**
     * application and controller schedule events with the event manager
     *
     * the event manager then fires off plugins which have registered for the event
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
            . $event, LOG_OUTPUT_PLUGINS, VERBOSE);

        /** Does Event (with registrations) exist? */
        $exists = Services::Registry()->exists('Events', $event);

        if ($exists == false) {
            Services::Profiler()->set('EventService->schedule Event: '
                . $event . ' does not exist', LOG_OUTPUT_PLUGINS);

            return $arguments;
        }

        /** Retrieve Event Registrations */
        $registrations = Services::Registry()->getArray($event);

        if (count($registrations) == 0) {
            Services::Profiler()->set('EventService->schedule Event ' . $event
                . ' has no registrations, exiting', LOG_OUTPUT_PLUGINS);

            return $arguments;
        }

        /** Filter for specified plugins (Query plugins) or use all plugins registered for event */
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
                    $temp = substr($key, 0, strlen($key) - strlen('Plugin'));
                    $selections[] = $temp;
                }
            }
        }

        /** Arguments can be changed by Plugins */
        $this->arguments = $arguments;

        /** Process each selected plugin */
        foreach ($selections as $selection) {

            $pluginClass = strtolower($selection) . 'plugin';

            if (isset($registrations[$pluginClass])) {

                if (method_exists($registrations[$pluginClass], $event)) {

                    $results = $this->processPluginClass($registrations[$pluginClass], $event);

                    if ($results == false) {
                        return false;
                    }

                } else {

                    Services::Profiler()->set('EventService->schedule Event '
                            . $event . ' Class does not exist '
                            . $registrations[$pluginClass],
                        LOG_OUTPUT_PLUGINS);

                    return false;
                    //throw error
                }

            } else {
                Services::Profiler()->set('EventService->schedule Event '
                        . $event . ' No valid registrations for class '
                        . $pluginClass,
                    LOG_OUTPUT_PLUGINS,
                    VERBOSE
                );
            }
        }

        return $this->arguments;
    }

    /**
     * processPluginClass for Event given $this->arguments
     *
     * @param $class
     * @param $event
     *
     * @return array|bool
     * @since  1.0
     */
    protected function processPluginClass($class, $event)
    {
        /** 1. Instantiate Plugin Class */
        $pluginClass = $class;

        try {
            $connection = new $pluginClass();

        } catch (\Exception $e) {

            Services::Profiler()->set('EventService->schedule Event ' . $event
                . ' Instantiating Class ' . $pluginClass . ' Failed', LOG_OUTPUT_PLUGINS);

            echo '<br />Could not Instantiate Plugin Class: ' . $pluginClass;

            return true;
            //throw error
        }

        /** 2. Set Properties for Plugin Class */
        if (count($this->arguments) > 0) {

            foreach ($this->arguments as $propertyKey => $propertyValue) {
                $connection->set($propertyKey, $propertyValue);
            }
            $connection->setFields();
        }

        /** 3. Execute Plugin Class Method */
        Services::Profiler()->set('EventService->schedule Event ' . $event
            . ' calling ' . $pluginClass . ' ' . $event, LOG_OUTPUT_PLUGINS, VERBOSE);

        $results = $connection->$event();

        if ($results == false) {

            Services::Profiler()->set('EventService->schedule Event '
                    . $event . ' Plugin Class '
                    . $class
                    . ' Failed. ',
                LOG_OUTPUT_PLUGINS);

            return true;

        } else {

            /** Retrieve Properties from Plugin Class to send back to Controller */
            if (count($this->arguments) > 0) {
                foreach ($this->arguments as $propertyKey => $propertyValue) {
                    $this->arguments[$propertyKey] = $connection->get($propertyKey);
                }
            }
        }

        return true;
    }

    /**
     * Plugins register for events. When the event is scheduled, the plugin will be executed.
     *
     * Installed plugins are registered during Application startup process.
     * Other plugins can be created and dynamically registered using this method.
     * Plugins can be overridden by registering after the installed plugins.
     *
     * Usage:
     * Services::Event()->register(
     *   'AliasPlugin',
     *   'Molajo\\Extension\\Plugin\\Alias\\AliasPlugin',
     *   'OnBeforeUpdate'
     * );
     *
     * @return object
     * @since   1.0
     */
    public function register($plugin, $pluginPath, $event)
    {
        Services::Profiler()->set('EventService->register '
                . 'Plugin: ' . $plugin
                . ' Class: ' . $pluginPath
                . ' Event: ' . $event,
            LOG_OUTPUT_PLUGINS,
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
        Services::Registry()->set($event, $plugin, $pluginPath);

        /** Update Event Totals */
        Services::Registry()->set('Events', $event, $count);

        return $this;
    }

    /**
     * Automatically registers all Plugins in the Extension Plugin folder
     *
     * @return object
     * @since   1.0
     */
    protected function registerInstalledPlugins()
    {
        Services::Profiler()->set('EventService->registerInstalledPlugins ', LOG_OUTPUT_PLUGINS, VERBOSE);

        $plugins = Services::Filesystem()->folderFolders(EXTENSIONS_PLUGINS);

        /** Load Parent Classes first */
        $pluginClass = 'Molajo\\Extension\\Plugin\\Plugin\\Plugin';
        $temp = new $pluginClass ();

        $pluginClass = 'Molajo\\Extension\\Plugin\\Content\\ContentPlugin';
        $temp = new $pluginClass ();

        foreach ($plugins as $folder) {

            /** class name */
            if ($folder == 'Plugin'
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
     * Instantiate the plugin class, register it for event(s), and save the path and name
     *
     * @param  $folder location of the plugin
     *
     * @return object
     * @since  1.0
     */
    protected function process_events($folder)
    {
        $try = true;
        $connection = '';

        $plugin = $folder . 'Plugin';
        $pluginClass = 'Molajo\\Extension\\Plugin\\' . $folder . '\\' . $plugin;

        /** Retrieve all Event Methods in the Plugin */
        $events = get_class_methods($pluginClass);

        if (count($events) > 0) {
            foreach ($events as $event) {
                if (substr($event, 0, 2) == 'on') {
                    $reflectionMethod = new \ReflectionMethod(new $pluginClass, $event);
                    $results = $reflectionMethod->getDeclaringClass();
                    if ($results->name == $pluginClass) {
                        $this->register($plugin, $pluginClass, $event);
                    }
                }
            }
        }

        return $this;
    }
}
