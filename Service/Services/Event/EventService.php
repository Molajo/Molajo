<?php
/**
 * Event Service
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Services\Event;

use Molajo\Service\Services;
use Molajo\Service\Services\Event\EventServicePlugin;

defined('NIAMBIE') or die;

/**
 * Event Service
 *
 * List All Events:
 *      $event_array = Services::Registry()->get('Events', 'events');
 *
 * List Plugins for a Specific Event:
 *      $plugin_array = Services::Registry()->get('Events', 'onBeforeRead');
 *
 * Schedule an Event:
 *      Services::Event()->scheduleEvent('onAfterDelete', $arguments, $selections);
 *
 * Override a Plugin:
 *      Copy the Plugin folder into an Extension (i.e., Resource, View, Theme, etc.) and make changes,
 *      When that extension is in use, Molajo will locate the override and register it with this command:
 *
 *      Services::Event()->registerPlugin(PLATFORM_FOLDER . '/' . 'Plugin', 'Molajo\\Plugin\\');
 *      Services::Event()->registerPlugin('Extension', 'Extension\\Resource\\Articles\\AliasPlugin');
 *
 * @author     Amy Stephen
 * @license    MIT
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @since      1.0
 */

Class EventService
{
    /**
     * Indicator Event Service has been activated
     *
     * @var    array
     * @since  1.0
     */
    protected $on;

    /**
     * Events discovered within Plugins
     *
     * @var    array
     * @since  1.0
     */
    protected $event_array = array();

    /**
     * Plugins with Events
     *
     * @var    array
     * @since  1.0
     */
    protected $plugin_array = array();

    /**
     * Recordset for each Event/Plugin combination
     *
     * @var    array
     * @since  1.0
     */
    protected $event_plugin_array = array();

    /**
     * List of named Plugin Properties
     *
     * @var    object
     * @since  1.0
     */
    protected $property_array = array(
        'on',
        'event_array',
        'plugin_array',
        'event_plugin_array',
        'plugin_class',
        'plugin_event',
        'model',
        'model_registry',
        'model_registry_name',
        'parameters',
        'property_array',
        'query_results',
        'row',
        'rendered_output',
        'class_array',
        'include_parse_sequence',
        'include_parse_exclude_until_final',
        'service_class',

    );

    /**
     * Initialise Event Service - Register Core and Extension Plugins for Events
     *
     * @return  boolean
     * @since   1.0
     */
    public function initialise()
    {

    }

    /**
     * get property
     *
     * @param   $key
     *
     * @return  mixed
     * @since   1.0
     */
    public function get($key, $default)
    {
        $key = strtolower($key);

        if (in_array($key, $this->property_array)) {
        } else {
            throw new \OutOfRangeException
            ('Event Service: attempting to set value for unknown key: ' . $key);
        }


        if (isset($this->$key)) {
            return $this->$key;
        }

        return $this->$key;
    }

    /**
     * set property
     *
     * @param   string  $key
     * @param   string  $value
     *
     * @return  mixed
     * @since   1.0
     */
    public function set($key, $value)
    {
        $key = strtolower($key);

        if (in_array($key, $this->property_array)) {
        } else {
            throw new \OutOfRangeException
            ('Event Service: attempting to set value for unknown key: ' . $key);
        }

        $this->$key = $value;

        return;
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
        if (defined('PROFILER_ON') && PROFILER_ON === true) {
            Services::Profiler()->set(
                'message',
                'Event: Initiated Scheduling of Event ' . $event,
                'Plugins',
                1
            );
        }

        $event = strtolower($event);

        if (in_array($event, $this->event_array) || count($this->event_plugin_array) > 0) {
        } else {
            if (defined('PROFILER_ON') && PROFILER_ON === true) {
                Services::Profiler()->set(
                    'message',
                    'Event: ' . $event . ' has no registrations',
                    'Plugins',
                    1
                );
            }

            return $arguments;
        }

        $compareSelection = array();
        if (count($selections) > 0 && is_array($selections)) {
            foreach ($selections as $s) {
                $compareSelection[] = strtolower($s . 'Plugin');
            }
        }

        $scheduledEventPlugins = array();
        foreach ($this->event_plugin_array as $x) {

            if ($x->event == $event) {

                if (count($compareSelection) == 0
                    || in_array(strtolower($x->plugin), $compareSelection)
                ) {
                    $temp_row = $x;

                    $temp_row->plugin_class = $this->plugin_array[$x->plugin];
                    $temp_row->model_name   = $x->model_name;
                    $temp_row->model_type   = $x->model_type;

                    $scheduledEventPlugins[] = $temp_row;
                }
            }
        }

        if (count($scheduledEventPlugins) == 0) {

            if (defined('PROFILER_ON') && PROFILER_ON === true) {
                Services::Profiler()->set(
                    'message',
                    'EventService: ' . $event . ' has no registrations',
                    'Plugins',
                    1
                );
            }

            return $arguments;
        }

        foreach ($scheduledEventPlugins as $selection) {

            $plugin_class = $selection->plugin_class;
            $model_name   = $selection->model_name;
            $model_type   = $selection->model_type;

            if (method_exists($plugin_class, $event)) {

                $results = $this->processPluginClass($plugin_class, $event, $arguments, $model_name, $model_type);

                if ($results === false) {
                    return false;
                }

                $arguments = $results;
            }
        }

        if (defined('PROFILER_ON') && PROFILER_ON === true) {
            Services::Profiler()->set(
                'message',
                'Event: Finished EventSchedule for Event: ' . $event,
                'Plugins',
                1
            );
        }

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
    protected function processPluginClass($plugin_class, $event, $arguments = array(), $model_name, $model_type)
    {
        try {
            $plugin = new $plugin_class();

        } catch (\Exception $e) {
            throw new \Exception('Event: ' . $event . ' processPluginClass failure instantiating: ' . $plugin_class);
        }

        if (defined('PROFILER_ON') && PROFILER_ON === true) {

            Services::Profiler()->set(
                'message',
                'Event:' . $event . ' firing Plugin: ' . $plugin_class,
                'Plugins',
                1
            );
        }

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


        /** Option Test at the Event Name level to see if Plugin Class should run */
        $method = $event . 'Test';
        if (method_exists($plugin_class, $method)) {
            $results = $plugin->$method();
            if ($results === false) {
                return $arguments;
            }
        }

        $plugin->initialise();

        $results = $plugin->$event();

        if ($results === false) {
            // plugin will throw Exception if warranted, otherwise, a false means "don't update data"
        } else {

            if (count($arguments) > 0) {

                foreach ($arguments as $key => $value) {

                    if (in_array($key, $this->property_array)) {
                        $arguments[$key] = $plugin->get($key);

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
    public function registerPlugins($folder = '', $namespace = '', $core = 0)
    {
        if (defined('PROFILER_ON') && PROFILER_ON === true) {
            Services::Profiler()->set(
                'message',
                'Event: registerPlugins for Namespace' . $namespace,
                'Plugins',
                1
            );
        }

        if ($folder == '') {
            throw new \Exception ('Event: No folder sent into RegisterPlugins');
        }

        if ($namespace == '') {
            throw new \Exception ('Event: No namespace sent into RegisterPlugins');
        }

        $folder .= '/' . 'Plugin';
        $namespace .= '\\' . 'Plugin' . '\\';

        $connect = new EventServicePlugin();
        $plugins = $connect->registerPluginFolder($folder, $namespace, $core);

        if (count($plugins) == 0 || $plugins === false) {
            return true;
        }

        foreach ($plugins as $plugin) {

            $plugin_name  = $plugin[0];
            $plugin_class = $namespace . $plugin[1] . '\\' . $plugin[0];

            try {

                $this->registerPlugin($plugin_name, $plugin_class);

            } catch (\Exception $e) {

                throw new \Exception('Events: Registration Failed for Plugin '
                    . $plugin_name . ' and Class ' . $plugin_class);
            }
        }

        sort($this->event_array);
        ksort($this->plugin_array);
        sort($this->event_plugin_array);

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
                    $results          = $reflectionMethod->getDeclaringClass();

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
     * @param   string  $plugin_name
     * @param   string  $plugin_class
     * @param   string  $event
     *
     * @return  void
     * @since   1.0
     */
    protected function registerPluginEvent($plugin_name, $plugin_class, $event)
    {
        $event = strtolower($event);

        $this->plugin_array[$plugin_name] = $plugin_class;

        if (in_array($event, $this->event_array)) {
        } else {
            $this->event_array[] = $event;
        }

        $list                     = $this->event_plugin_array;
        $this->event_plugin_array = array();

        $found = false;
        if (count($list) > 0) {
            foreach ($list as $single) {
                if ($event == $single->event) {
                    if ($plugin_name == $single->plugin) {
                        $found = true;
                    }
                }
                $this->event_plugin_array[] = $single;
            }
        }

        if ($found === true) {
        } else {

            $temp_row = new \stdClass();

            $temp_row->event      = $event;
            $temp_row->plugin     = $plugin_name;
            $temp_row->model_name = strtolower(substr($plugin_name, 0, strlen($plugin_name) - strlen('Plugin')));
            $temp_row->model_type = 'Plugin';

            $this->event_plugin_array[] = $temp_row;
        }

        if (defined('PROFILER_ON') && PROFILER_ON === true) {
            Services::Profiler()->set(
                'message',
                'Event: Plugin ' . $plugin_name
                    . ' scheduled for Event: ' . $event
                    . ' will execute from namespace ' . $plugin_class,
                'Plugins',
                1
            );
        }

        return;
    }
}
