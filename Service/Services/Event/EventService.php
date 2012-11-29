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
 *      Services::Event()->registerPlugin(PLATFORM_FOLDER . '/' . PLUGIN_LITERAL, 'Molajo\\Plugin\\');
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
     * Plugins with Events
     *
     * @var    array
     * @since  1.0
     */
    protected $pluginArray;

    /**
     * Recordset for each Event/Plugin combination
     *
     * @var    array
     * @since  1.0
     */
    protected $eventPluginArray;

    /**
     * Initialise Event Service - Register Core and Extension Plugins for Events
     *
     * @return  boolean
     * @since   1.0
     */
    public function initialise()
    {
        Services::Registry()->createRegistry(EVENTS_LITERAL);

        Services::Registry()->set(EVENTS_LITERAL, 'Events', array());
        Services::Registry()->set(EVENTS_LITERAL, 'Plugins', array());
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
        Services::Profiler()->set('Event: ' . $event, PROFILER_PLUGINS);

        $event = strtolower($event);

        $eventList = Services::Registry()->get(EVENTS_LITERAL, 'Events', array());
        if (in_array($event, $eventList)) {
        } else {
            Services::Profiler()->set('EventService: ' . $event . ' has no registrations', PROFILER_PLUGINS, VERBOSE);
            return $arguments;
        }

        $registered = Services::Registry()->get(EVENTS_LITERAL, 'EventPlugins', array());
        if (count($registered) > 0) {
        } else {
            Services::Profiler()->set('EventService: ' . $event . ' has no registrations', PROFILER_PLUGINS, VERBOSE);
            return $arguments;
        }

        $compareSelection = array();
        if (count($selections) > 0 && is_array($selections)) {
            foreach ($selections as $s) {
                $compareSelection[] = strtolower($s . PLUGIN_LITERAL);
            }
        }

        $scheduledEventPlugins = array();
        $pluginList = Services::Registry()->get(EVENTS_LITERAL, 'Plugins');

        foreach ($registered as $x) {
            if ($x->event == $event) {
                if (count($compareSelection) == 0
                    || in_array(strtolower($x->plugin), $compareSelection)
                ) {
                    $row = $x;
                    $row->pluginClass = $pluginList[$x->plugin];
                    $scheduledEventPlugins[] = $row;
                }
            }
        }

        if (count($scheduledEventPlugins) == 0) {
            Services::Profiler()->set('EventService: ' . $event . ' has no registrations', PROFILER_PLUGINS, VERBOSE);
            return $arguments;
        }

        foreach ($scheduledEventPlugins as $selection) {

            $pluginClass = $selection->pluginClass;

            if (method_exists($pluginClass, $event)) {
                Services::Profiler()->set('Event: ' . $event . ' Plugin: ' . $pluginClass, PROFILER_PLUGINS);
                $results = $this->processPluginClass($pluginClass, $event, $arguments);
                if ($results === false) {
                    return false;
                }
                $arguments = $results;
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
     * Registers all Plugins to listen for Events from the Core and Extensions Folders
     *
     * @return  object
     * @since   1.0
     */
    protected function registerInstalledPlugins()
    {
        $this->registerPlugins(EXTENSIONS, 'Extension');
        $this->registerPlugins(PLATFORM_FOLDER, 'Molajo');

        sort($this->eventArray);
        sort($this->pluginArray);
        sort($this->eventPluginArray);

        return $this;
    }

    /**
     * Registers all Plugins in the folder
     *
     * Extensions can override Plugins by including a like-named folder in a Plugin directory within the extension
     *
     * The application will find and register overrides at the point in time the extension is used in rendering.
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
        $namespace .= '\\' . PLUGIN_LITERAL . '\\';

        $this->eventArray = Services::Registry()->get(EVENTS_LITERAL, 'Events');
        $this->pluginArray = Services::Registry()->get(EVENTS_LITERAL, 'Plugins');
        $this->eventPluginArray = Services::Registry()->get(EVENTS_LITERAL, 'EventPlugins');

        $plugins = Services::Filesystem()->folderFolders($folder);

        if (count($plugins) == 0 || $plugins === false) {
            return true;
        }

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

        Services::Registry()->set(EVENTS_LITERAL, 'Events', $this->eventArray);
        Services::Registry()->set(EVENTS_LITERAL, 'Plugins', $this->pluginArray);
        Services::Registry()->set(EVENTS_LITERAL, 'EventPlugins', $this->eventPluginArray);

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

        $pluginName = strtolower($pluginName);
        $event = strtolower($event);

        $this->pluginArray[$pluginName] = $pluginClass;

        if (in_array($event, $this->eventArray)) {
        } else {
            $this->eventArray[] = $event;
        }

        $list = $this->eventPluginArray;
        $this->eventPluginArray = array();

        $found = false;
        if (count($list) > 0) {
            foreach ($list as $single) {
                if ($event == $single->event) {
                    if ($pluginName == $single->plugin) {
                        $found = true;
                    }
                }
                $this->eventPluginArray[] = $single;
            }
        }

        if ($found === true) {
        } else {
            $row = new \stdClass();
            $row->event = $event;
            $row->plugin = $pluginName;
            $this->eventPluginArray[] = $row;
        }

        return $this;
    }
}
