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
 * Event Service
 *
 * List All Events:
 *      $eventArray = Services::Registry()->get(EVENTS_LITERAL, 'events');
 *
 * List Plugins for a Specific Event:
 *      $pluginArray = Services::Registry()->get(EVENTS_LITERAL, 'onBeforeRead');
 *
 * Schedule an Event:
 *      Services::Event()->scheduleEvent('onAfterDelete', $arguments, $selections);
 *
 * Override a Plugin:
 *      Copy the Plugin folder into an Extension (i.e., Resource, View, Theme, etc.) and make changes,
 *      When that extension is in use, Molajo will locate the override and register it with this command:
 *
 *      Services::Event()->registerPlugins(PLATFORM_FOLDER . '/' . PLUGIN_LITERAL, 'Molajo\\Plugin\\');
 *      Services::Event()->registerPlugin('Extension', 'Extension\\Resource\\Articles\\AliasPlugin');
 *
 * @package     Molajo
 * @subpackage  Service
 * @since       1.0
 */

Class EventService
{
    /**
     * Events discovered within Plugins
     *
     * @var    array
     * @since  1.0
     */
    protected $eventArray;

    /**
     * Events and an array of Plugins for each Event
     *
     * @var    array
     * @since  1.0
     */
    protected $event_pluginArray;

    /**
     * List of Plugins
     *
     * @var    array
     * @since  1.0
     */
    protected $pluginArray;

    /**
     * List of Events for each Plugin
     *
     * @var    array
     * @since  1.0
     */
    protected $plugin_eventArray;

    /**
     * Initialise Event Service - Register installed Plugins for Events
     *
     * @return  boolean
     * @since   1.0
     */
    public function initialise()
    {
        Services::Registry()->createRegistry(EVENTS_LITERAL);

        Services::Registry()->set(EVENTS_LITERAL, 'Plugins', array());
        Services::Registry()->set(EVENTS_LITERAL, 'PluginEvents', array());
        Services::Registry()->set(EVENTS_LITERAL, 'Events', array());
        Services::Registry()->set(EVENTS_LITERAL, 'EventPlugins', array());

        $this->registerInstalledPlugins();

        return $this;
    }

    /**
     * The application schedules events at various points within the system.
     *
     * Usage:
     * Services::Event()->scheduleEvent('onAfterDelete', $arguments, $selections);
     *
     * As a result of the schedule request, the Event Service fires off plugins
     *  meeting this criteria:
     *
     * - published (or archived)
     * - registered for the scheduled event
     * - associated with the current extension
     * - authorised for use by the user
     *
     * @param   string  $event
     * @param   array   $parameters
     * @param   array   $selections
     *
     * @return  boolean
     *
     * @since   1.0
     */
    public function scheduleEvent($event, $arguments = array(), $selections = array())
    {
        Services::Profiler()->set('Event: scheduleEvent ' . $event, PROFILER_PLUGINS, VERBOSE);

        $registrations = Services::Registry()->get(EVENTS_LITERAL, 'EventPlugins');

        if (count($registrations) == 0) {
            Services::Profiler()->set(
                'EventService->schedule Event ' . $event . ' has no registrations, exiting',
                PROFILER_PLUGINS,
                VERBOSE
            );

            return $arguments;
        }

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
            $selections = array();
            if (count($registrations) > 0) {
                foreach ($registrations as $key => $value) {
                    $temp = substr($key, 0, strlen($key) - strlen(PLUGIN_LITERAL));
                    $selections[] = $temp;
                }
            }
        }

        foreach ($selections as $selection) {

            $pluginClass = strtolower($selection) . PLUGIN_LITERAL;

            if (isset($registrations[$pluginClass])) {

                if (method_exists($registrations[$pluginClass], $event)) {

                    $results = $this->processPluginClass($registrations[$pluginClass], $event, $arguments);
                    if ($results === false) {
                        return false;
                    }

                    $arguments = $results;

                } else {

                    Services::Profiler()->set(
                        'EventService->schedule Event '
                            . $event . ' Class does not exist '
                            . $registrations[$pluginClass],
                        PROFILER_PLUGINS
                    );

                    throw new \Exception('Events: scheduleEvent identified Class ' . $pluginClass
                        . ' that does not exist');
                }
            }
        }

        return $arguments;
    }

    /**
     * Instantiate the Plugin Class, use attributes from schedule request to establish initial property values
     * Execute each qualified plugin, one at a time, until a false is encountered or all plugins are processed.
     * Return attributes to requester.
     *
     * @param   string  $class
     * @param   string  $event
     *
     * @return  array|bool
     * @since   1.0
     * @throws  \Exception
     */
    protected function processPluginClass($class, $event, $arguments)
    {
        $pluginClass = $class;

        try {
            $plugin = new $pluginClass();

        } catch (\Exception $e) {

            Services::Profiler()->set(
                'EventService->schedule Event ' . $event
                    . ' Instantiating Class ' . $pluginClass . ' Failed',
                PROFILER_PLUGINS
            );

            throw new \Exception('Event: processPluginClass could not Instantiate Plugin Class: ' . $pluginClass);
        }

        if (count($arguments) > 0) {

            foreach ($arguments as $propertyKey => $propertyValue) {
                $plugin->set($propertyKey, $propertyValue);
            }

            $plugin->setFields();
        }

        Services::Profiler()->set(
            'EventService->schedule Event ' . $event
                . ' calling ' . $pluginClass . ' ' . $event,
            PROFILER_PLUGINS,
            VERBOSE
        );

        $results = $plugin->$event();

        if ($results === false) {

            Services::Profiler()->set(
                'EventService->schedule Event '
                    . $event . ' Plugin Class '
                    . $class
                    . ' Failed. ',
                PROFILER_PLUGINS
            );

            throw new \Exception('Event: processPluginClass failed for Plugin Class: '
                . $pluginClass . ' Event ' . $event);

        } else {

            if (count($arguments) > 0) {
                foreach ($arguments as $propertyKey => $propertyValue) {
                    $arguments[$propertyKey] = $plugin->get($propertyKey);
                }
            }
        }

        return $arguments;
    }

    /**
     * Registers all Plugins in the Core and Extensions Folders
     *
     * @return  object
     * @since   1.0
     */
    protected function registerInstalledPlugins()
    {
        $this->eventArray = array();
        $this->event_pluginArray = array();
        $this->pluginArray = array();
        $this->plugin_eventArray = array();

        $this->registerPlugins(PLATFORM_FOLDER . '/' . PLUGIN_LITERAL, 'Molajo\\Plugin\\');
        $this->registerPlugins(PLATFORM_FOLDER . '/' . PLUGIN_LITERAL, 'Molajo\\Plugin\\');

        return $this;

    }

    /**
     * Registers all Plugins in the folder
     *
     * Extensions can override Plugins by including a like-named folder in a Plugin directory within the extension
     *
     * The application will find and register the overrides when the extension is used.
     *
     * Usage:
     * Services::Event()->registerPlugin('Molajo\\Plugin');
     *
     * @return  object
     * @since   1.0
     */
    public function registerPlugins($folder = '', $namespace = '')
    {
        Services::Profiler()->set('EventService->registerPlugins for: ' . $folder, PROFILER_PLUGINS, VERBOSE);

        if ($folder == '') {
            throw new \Exception ('Event: No folder sent into RegisterPlugins');
        }
        if ($namespace == '') {
            throw new \Exception ('Event: No namespace sent into RegisterPlugins');
        }
        $folder .= '/' . PLUGIN_LITERAL;
        $namespace .= '\\Plugin\\';

        $this->pluginArray = Services::Registry()->get(EVENTS_LITERAL, 'Plugins');
        $this->plugin_eventArray = Services::Registry()->get(EVENTS_LITERAL, 'PluginEvents');
        $this->eventArray = Services::Registry()->get(EVENTS_LITERAL, 'Events');
        $this->event_pluginArray = Services::Registry()->get(EVENTS_LITERAL, 'EventPlugins');

        $plugins = Services::Filesystem()->folderFolders($folder);

        foreach ($plugins as $folder) {

            if (substr(strtolower($folder), 0, 4) == 'hold') {

            } else {

                $pluginName = $folder . PLUGIN_LITERAL;
                $pluginClass = $namespace . $folder . '\\' . $pluginName;

                try {

                    $this->registerPlugin($pluginName, $pluginClass);

                } catch (\Exception $e) {

                    throw new \Exception('Events: Registration Failed for Plugin '
                        . $pluginName . ' and Class ' . $pluginClass);
                }

            }
        }

        Services::Registry()->set(EVENTS_LITERAL, 'Plugins', $this->pluginArray);
        Services::Registry()->set(EVENTS_LITERAL, 'PluginEvents', $this->plugin_eventArray);
        Services::Registry()->set(EVENTS_LITERAL, 'Events', $this->eventArray);
        Services::Registry()->set(EVENTS_LITERAL, 'EventPlugins', $this->event_pluginArray);

        return $this;
    }

    /**
     * Instantiate the Plugin class, register it to listen to each event for which it has a method,
     *  and save the path and name for possible use later
     *
     * @param   string  $pluginName
     * @param   string  $pluginClass
     *
     * @return  object
     * @since   1.0
     */
    protected function registerPlugin($pluginName, $pluginClass)
    {
        $events = get_class_methods($pluginClass);

        if (count($events) > 0) {
            foreach ($events as $event) {
                if (substr($event, 0, 2) == 'on') {
                    $reflectionMethod = new \ReflectionMethod(new $pluginClass, $event);
                    $results = $reflectionMethod->getDeclaringClass();
                    if ($results->name == $pluginClass) {
                        $this->registerPluginEvent($pluginName, $pluginClass, $event);
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Plugins register for events. When the event is scheduled, the plugin will be executed.
     *
     * The last plugin to register is the one that will be invoked.
     *
     * Installed plugins are registered during Application startup process.
     * Other plugins can be created and dynamically registered using this method.
     * Plugins can be overridden by registering after the installed plugins.
     *
     * @param   $pluginName
     * @param   $pluginClass
     * @param   $event
     *
     * @return  EventService
     * @since   1.0
     */
    protected function registerPluginEvent($pluginName, $pluginClass, $event)
    {
        Services::Profiler()->set(
            'EventService->register '
                . 'Plugin: ' . $pluginName
                . ' Class: ' . $pluginClass
                . ' Event: ' . $event,
            PROFILER_PLUGINS,
            VERBOSE
        );

        /** Plugins */
        $this->pluginArray[$pluginName] = $pluginClass;

        /** Plugin Events */
        $this->plugin_eventArray = array();
        if (isset($this->plugin_eventArray[$pluginName])) {
        } else {
            $this->plugin_eventArray[$pluginName] = array();
        }

        $existing = $this->plugin_eventArray[$pluginName];
        if (is_array($existing)) {
            array_merge($existing, array($pluginName));
        }

        /** Events */
        if (in_array($event, $this->eventArray)) {
        } else {
            $this->eventArray[] = $event;
        }

        /** Event Plugins */
        if (isset($this->event_pluginArray[$event])) {
        } else {
            $this->event_pluginArray[$event] = array();
        }

        $existing = $this->event_pluginArray[$event];
        if (is_array($existing)) {
            array_merge($existing, array($pluginName));
        }

        return $this;
    }
}
