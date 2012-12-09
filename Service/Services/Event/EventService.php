<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
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
     * List of named Plugin Properties
     *
     * @var    object
     * @since  1.0
     */
    protected $property_array = array(
        'plugin_class',
        'plugin_event',
        'model',
        'model_registry',
        'parameters',
        'query_results',
        'row',
        'rendered_output',
        'include_parse_sequence',
        'include_parse_exclude_until_final'
    );

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

        Services::Registry()->set(EVENTS_LITERAL, 'on', true);

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
        Services::Profiler()->set('Event: Initiated Scheduling of Event ' . $event, PROFILER_PLUGINS);

        $event = strtolower($event);

        if (Services::Registry()->get(EVENTS_LITERAL, 'on') === true) {

            $eventList = Services::Registry()->get(EVENTS_LITERAL, 'Events');
            $registered = Services::Registry()->get(EVENTS_LITERAL, 'EventPlugins');
            $pluginList = Services::Registry()->get(EVENTS_LITERAL, 'Plugins');

        } else {

            //todo: provide startup parameters - remove hardcoding
            $selections = array();
            $eventList = array('onconnectdatabase');

            $row = new \stdClass();

            $row->event = 'onconnectdatabase';
            $row->plugin = 'dataobjectplugin';
            $row->model_name = 'dataobject';
            $row->model_type = 'Plugin';

            $this->eventPluginArray[] = $row;

            $registered = $this->eventPluginArray;
            $this->pluginArray['dataobjectplugin'] = 'Molajo\Plugin\Dataobject\DataobjectPlugin';
            $pluginList = $this->pluginArray;
        }

        if (in_array($event, $eventList) || count($registered) > 0) {
        } else {
            Services::Profiler()->set('Event: ' . $event . ' has no registrations', PROFILER_PLUGINS, VERBOSE);
            return $arguments;
        }

        $compareSelection = array();
        if (count($selections) > 0 && is_array($selections)) {
            foreach ($selections as $s) {
                $compareSelection[] = strtolower($s . PLUGIN_LITERAL);
            }
        }

        $scheduledEventPlugins = array();
        foreach ($registered as $x) {

            if ($x->event == $event) {
                if (count($compareSelection) == 0
                    || in_array(strtolower($x->plugin), $compareSelection)
                ) {
                    $row = $x;

                    $row->plugin_class = $pluginList[$x->plugin];
                    $row->model_name = $x->model_name;
                    $row->model_type = $x->model_type;

                    $scheduledEventPlugins[] = $row;
                }
            }
        }

        if (count($scheduledEventPlugins) == 0) {
            Services::Profiler()->set('EventService: ' . $event . ' has no registrations', PROFILER_PLUGINS, VERBOSE);
            return $arguments;
        }

        foreach ($scheduledEventPlugins as $selection) {

            $plugin_class = $selection->plugin_class;
            $model_name = $selection->model_name;
            $model_type = $selection->model_type;

            if (method_exists($plugin_class, $event)) {

                $results = $this->processplugin_class($plugin_class, $event, $arguments, $model_name, $model_type);

                if ($results === false) {
                    return false;
                }
                $arguments = $results;
            }
        }

        Services::Profiler()->set('Event: Finished EventSchedule for Event: ' . $event, PROFILER_PLUGINS, VERBOSE);

        return $arguments;
    }

    /**
     * Instantiate the Plugin Class.
     *
     * Establish initial property values given arguments passed in (could include changes other plugins made).
     * Load Fields for Model Registry, if in the arguments, for Plugin use.
     * Execute each qualified plugin, one at a time, until all have been processed.
     * Return arguments, which could contain changed data, to the calling class.
     *
     * @param   string  $plugin_class  includes namespace
     * @param   string  $event
     * @param   array   $arguments
     * @param   string  $model_name
     * @param   string  $model_type
     *
     * @return  array|bool
     * @since   1.0
     * @throws  \Exception
     */
    protected function processplugin_class($plugin_class, $event, $arguments = array(), $model_name, $model_type)
    {
        try {
            $plugin = new $plugin_class();

        } catch (\Exception $e) {
            throw new \Exception('Event: ' . $event . ' processplugin_class failure instantiating: ' . $plugin_class);
        }

        Services::Profiler()->set('Event:' . $event . ' firing Plugin: ' . $plugin_class, PROFILER_PLUGINS, VERBOSE);

        $plugin->set('plugin_class', $plugin_class);
        $plugin->set('plugin_event', $event);

        if (count($arguments) > 0) {

            foreach ($arguments as $key => $value) {

                if (in_array($key, $this->property_array)) {
                    $plugin->set($key, $value, '');

                } else {
                    throw new \OutOfRangeException('Event: ' . $event .
                        ' Plugin ' . $plugin_class .
                        ' attempting to set value for unknown property: ' . $key);
                }
            }
        }

echo '<br /> ' . $event . '       ' . $plugin_class . '<br /> ';

        $results = $plugin->$event();

        if ($results === false) {
            throw new \Exception ('False plugin' . $plugin_class . ' ' . $event);
        } else {

            if (count($arguments) > 0) {

                foreach ($arguments as $key => $value) {

                    if (in_array($key, $this->property_array)) {
                        $arguments[$key] = $plugin->get($key, '', '');

                    } else {
                        throw new \OutOfRangeException('Event: ' . $event .
                            ' Plugin ' . $plugin_class .
                            ' attempting to set value for unknown property: ' . $key);
                    }
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
        Services::Profiler()->set('Event: registerInstalledPlugins for Extension and Core', PROFILER_PLUGINS, VERBOSE);

        $this->registerPlugins(PLATFORM_FOLDER, 'Molajo');
        $this->registerPlugins(EXTENSIONS, 'Extension');

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
        Services::Profiler()->set('Event: registerPlugins for Namespace' . $namespace, PROFILER_PLUGINS, VERBOSE);

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

                $plugin_name = $folder . PLUGIN_LITERAL;
                $plugin_class = $namespace . $folder . '\\' . $plugin_name;

                try {
                    $this->registerPlugin($plugin_name, $plugin_class);

                } catch (\Exception $e) {

                    throw new \Exception('Events: Registration Failed for Plugin '
                        . $plugin_name . ' and Class ' . $plugin_class);
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
     * @param   string  $plugin_name
     * @param   string  $plugin_class
     *
     * @return  object
     * @since   1.0
     */
    protected function registerPlugin($plugin_name, $plugin_class)
    {
        $events = get_class_methods($plugin_class);

        if (count($events) > 0) {

            foreach ($events as $event) {

                if (substr($event, 0, 2) == 'on') {
                    $reflectionMethod = new \ReflectionMethod(new $plugin_class, $event);
                    $results = $reflectionMethod->getDeclaringClass();

                    if ($results->name == $plugin_class) {
                        $this->registerPluginEvent($plugin_name, $plugin_class, $event);
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
     * @param   $plugin_name
     * @param   $plugin_class
     * @param   $event
     *
     * @return  EventService
     * @since   1.0
     */
    protected function registerPluginEvent($plugin_name, $plugin_class, $event)
    {
        $plugin_name = strtolower($plugin_name);
        if (Services::Registry()->exists('AuthorisedExtensionsByInstanceTitle')) {
            $test = 1;
        } else {
            $test = 0;
        }

        $authorised = 0;
        if ($test > 0) {
            $authorised = Services::Registry()->get(
                'AuthorisedExtensionsByInstanceTitle',
                substr($plugin_name, 0, strlen($plugin_name) - strlen(PLUGIN_LITERAL)) . CATALOG_TYPE_PLUGIN
            );
        } else {
            $authorised = 1; // some plugin usage occurs before the authorisation table is ready
        }

        if ($authorised == 0) {
            throw new \Exception ('User not authorised for ' . $plugin_name);
        }

        $event = strtolower($event);

        $this->pluginArray[$plugin_name] = $plugin_class;

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
                    if ($plugin_name == $single->plugin) {
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
            $row->plugin = $plugin_name;
            $row->model_name = strtolower(substr($plugin_name, 0, strlen($plugin_name) - strlen(PLUGIN_LITERAL)));
            $row->model_type = 'Plugin';

            $model_registry = ucfirst(strtolower($row->model_name)) . ucfirst(strtolower($row->model_type));
            $row->model_registry = $model_registry;
            if (Services::Registry()->exists($model_registry)) {
                Services::Registry()->deleteRegistry($model_registry);
            }

            $controllerClass = CONTROLLER_CLASS;
            $controller = new $controllerClass();
            $controller->getModelRegistry($row->model_type, $row->model_name);

            $this->eventPluginArray[] = $row;
        }

        Services::Profiler()->set(
            'Event: Registered Plugin ' . $plugin_name . ' to listen for Event ' . $event
                . ' will execute at Namespace ' . $plugin_class,
            PROFILER_PLUGINS,
            VERBOSE
        );

        return $this;
    }
}
