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

defined('NIAMBIE') or die;

/**
 * Event Service
 *
 * List All Events:
 *      $event_array = Services::Events()->get('Events');
 *
 * List All Plugins:
 *      $plugin_array = Services::Events()->get('Plugins');
 *
 * List Plugins for a Specific Event:
 *      $plugin_array = Services::Events()->get('Plugins', 'onBeforeRead');
 *
 * Schedule an Event:
 *      Services::Event()->scheduleEvent('onAfterDelete', $arguments, $selections);
 *
 * Override a Plugin:
 *      Copy the Plugin folder into an Extension (i.e., Plugin, Resource, View, Theme, etc.) and make changes,
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
     * Frontcontroller Instance
     *
     * @var    object
     * @since  1.0
     */
    protected $frontcontroller_instance;

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
        'frontcontroller_instance',
        'events',
        'event_array',
        'plugins',
        'plugin_array',
        'event_plugin_array'
    );

    /**
     * List of Plugin Property Array
     *
     * Must match Plugin Class $property_array Property
     *
     * @var    object
     * @since  1.0
     */
    protected $plugin_property_array = array(
        'model',
        'model_registry',
        'model_registry_name',
        'parameters',
        'parameter_property_array',
        'query_results',
        'row',
        'rendered_output',
        'view_path',
        'view_path_url',
        'plugins',
        'class_array',
        'include_parse_sequence',
        'include_parse_exclude_until_final'
    );

    /**
     * get property
     *
     * @param   string  $key
     * @param   string  $default
     *
     * @return  mixed
     * @since   1.0
     * @throws  \OutOfRangeException
     */
    public function get($key, $default = '')
    {
        $key = strtolower($key);

        if ($key == 'events') {
            $key = 'event_array';
        }

        if ($key == 'plugins') {
            $plugins = array();
            foreach ($this->event_plugin_array as $x) {
                if ($x->event == $default || $default == '') {
                    $plugin           = $this->plugin_array[$x->plugin];
                    $plugins[$plugin] = $x->plugin;
                }
            }

            return $plugins;
        }

        if (in_array($key, $this->property_array)) {
        } else {
            throw new \OutOfRangeException
            ('Event Service: attempting to set value for unknown key: ' . $key);
        }

        if (isset($this->$key)) {
        } else {
            $this->$key = $default;
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
     * @throws  \OutOfRangeException
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
     * @param   array   $arguments
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
                'Event Service: Initiated Scheduling of Event ' . $event,
                'Plugins',
                1
            );

        }
echo ' Event Sechedule: ' . $event  . '<br />';
        if (in_array(strtolower($event), $this->event_array) || count($this->event_plugin_array) > 0) {
        } else {

            if (defined('PROFILER_ON') && PROFILER_ON === true) {
                Services::Profiler()->set(
                    'message',
                    'Event Service: ' . $event . ' has no registrations',
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

            if ($x->event == strtolower($event)) {

                if (count($compareSelection) == 0
                    || in_array(strtolower($x->plugin), $compareSelection)
                ) {
                    $temp_row = $x;

                    $temp_row->plugin_class_name = $this->plugin_array[$x->plugin];
                    $temp_row->model_name        = $x->model_name;
                    $temp_row->model_type        = $x->model_type;

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

            $plugin_class_name = $selection->plugin_class_name;

            if (method_exists($plugin_class_name, $event)) {

                $results = $this->processPluginClass($plugin_class_name, $event, $arguments);

                if ($results === false) {
                    return false;
                }

                $arguments = $results;
            }
        }

        if (defined('PROFILER_ON') && PROFILER_ON === true) {

            Services::Profiler()->set(
                'message',
                'Event Service: Finished EventSchedule for Event: ' . $event,
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
     * @param   string  $plugin_class_name  includes namespace
     * @param   string  $event
     * @param   array   $arguments
     *
     * @return  array|bool
     * @since   1.0
     * @throws  \Exception
     * @throws  \OutOfRangeException
     */
    protected function processPluginClass($plugin_class_name, $event, $arguments = array())
    {
        try {
            $plugin = new $plugin_class_name();

        } catch (\Exception $e) {
            throw new \Exception('Event Service: ' . $event
                . ' processPluginClass failure instantiating: ' . $plugin_class_name);
        }

        if (defined('PROFILER_ON') && PROFILER_ON === true) {

            Services::Profiler()->set(
                'message',
                'Event:' . $event . ' firing Plugin: ' . $plugin_class_name,
                'Plugins',
                1
            );
        }

        $plugin->set('frontcontroller_instance', $this->frontcontroller_instance);

        $plugin->set('plugin_class_name', $plugin_class_name);

        $plugin->set('plugin_event', $event);

        if (count($arguments) > 0) {

            foreach ($arguments as $key => $value) {

                if (in_array($key, $this->plugin_property_array)) {
                    $plugin->set($key, $value, '');

                } else {
                    throw new \OutOfRangeException('Event Service: ' . $event .
                        ' Plugin ' . $plugin_class_name .
                        ' attempting to set value for unknown property: ' . $key);
                }
            }
        }

        $plugin->initialise();

        $results = $plugin->$event();

        if ($results === false) {
            // plugin will throw Exception if warranted, otherwise, a false means "don't update data"
        } else {

            if (count($arguments) > 0) {

                foreach ($arguments as $key => $value) {

                    if (in_array($key, $this->plugin_property_array)) {
                        $arguments[$key] = $plugin->get($key);

                    } else {
                        throw new \OutOfRangeException('Event Service: ' . $event .
                            ' Plugin ' . $plugin_class_name .
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
     * @param   string  $folder
     * @param   string  $namespace
     *
     * @return  bool|EventService
     * @throws  \Exception
     */
    public function registerPlugin($plugin_name = '', $plugin_class_name = '')
    {
        $events = get_class_methods($plugin_class_name);

        if (count($events) > 0) {

            foreach ($events as $event) {

                if (substr($event, 0, 2) == 'on') {
                    $reflectionMethod = new \ReflectionMethod(new $plugin_class_name, $event);
                    $results          = $reflectionMethod->getDeclaringClass();

                    if ($results->name == $plugin_class_name) {
                        $this->registerPluginEvent($plugin_name, $plugin_class_name, $event);
                    }
                }
            }
        }

        sort($this->event_array);
        ksort($this->plugin_array);
        sort($this->event_plugin_array);

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
     * @param   string  $plugin_class_name
     * @param   string  $event
     *
     * @return  void
     * @since   1.0
     */
    protected function registerPluginEvent($plugin_name, $plugin_class_name, $event)
    {
        $event = strtolower($event);

        // $this->plugin_array['AssetServicePlugin'] = 'Molajo//Service//Services//Asset';
        $this->plugin_array[$plugin_name] = $plugin_class_name;

        // $this->event_array = 'onBeforeRegisterPlugins';
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

            // $this->event_plugin_array = array (
            //      event => 'onBeforeRegisterPlugin',
            //      plugin => 'EventServicePlugin',
            //      model_name => 'EventService',
            //      model_type => 'Plugin'
            //  )

            $temp_row->event      = $event;
            $temp_row->plugin     = $plugin_name;
            $temp_row->model_name = strtolower(substr($plugin_name, 0, strlen($plugin_name) - strlen('Plugin')));
            $temp_row->model_type = 'Plugin';

            $this->event_plugin_array[] = $temp_row;
        }

        if (defined('PROFILER_ON') && PROFILER_ON === true) {
            Services::Profiler()->set(
                'message',
                'Event Service: Plugin ' . $plugin_name
                    . ' scheduled for Event: ' . $event
                    . ' will execute from namespace ' . $plugin_class_name,
                'Plugins',
                1
            );
        }

        return;
    }
}
