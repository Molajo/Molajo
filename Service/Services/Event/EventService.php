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
 * To list all Events:
 *      Services::Registry()->get('Events', '*');
 *
 * To see what Plugins fire for a specific event:
 *      Services::Registry()->get('onBeforeRead', '*');
 *
 * To Schedule an Event:
 *      Services::Event()->scheduleEvent('onAfterDelete', $arguments, $selections);
 *
 * To override a Plugin: copy the plugin folder into an extension (i.e., Resource, View, Theme, etc.),
 *      When that extension is current, Molajo will locate the override and register it with this command:
 *
 *      Services::Event()->registerPlugin('AliasPlugin', 'Extension\\Resource\\Articles\\AliasPlugin');
 *
 * @package     Molajo
 * @subpackage  Service
 * @since       1.0
 */
Class EventService
{
    /**
     * Initialise Event Service - Register installed Plugins for Events
     *
     * @return  boolean
     * @since   1.0
     */
    public function initialise()
    {
        Services::Registry()->createRegistry('Events');

        $this->registerInstalledPlugins();
    }

    /**
     * Instantiate the Plugin class, register it to listen to each event for which it has a method,
     *  and save the path and name for possible use later
     *
     * This method is used for registering normal events defined in core and extension plugins
     *
     * Can be used within extensions to schedule new custom events
     *
     * Usage:
     * Services::Event()->registerPlugin('AliasPlugin', 'Molajo\\Plugin\\Alias\\AliasPlugin');
     *
     * @param   string  $pluginName
     * @param   string  $pluginClass
     *
     * @return  object
     * @since   1.0
     */
    public function registerPlugin($pluginName, $pluginClass)
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
        Services::Profiler()->set('EventService->scheduleEvent ' . $event, LOG_OUTPUT_PLUGINS, VERBOSE);

        $registrations = Services::Registry()->getArray($event);

        if (count($registrations) == 0) {
            Services::Profiler()->set(
                'EventService->schedule Event ' . $event . ' has no registrations, exiting',
                LOG_OUTPUT_PLUGINS,
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
                    $temp = substr($key, 0, strlen($key) - strlen('Plugin'));
                    $selections[] = $temp;
                }
            }
        }

        foreach ($selections as $selection) {

            $pluginClass = strtolower($selection) . 'plugin';

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
                        LOG_OUTPUT_PLUGINS
                    );

                    return false;
                    //throw error
                }
            }
        }

        return $arguments;
    }

    /**
     * Instantiate the Plugin Class, use attributes from schedule request to establish initial property values
     * Execute each qualified plugin, one at a time, until a false is encountered or all plugins are processed.
     * Return attributes to requestor.
     *
     * @param   $class
     * @param   $event
     *
     * @return  array|bool
     * @since   1.0
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
                LOG_OUTPUT_PLUGINS
            );

            echo '<br />Could not Instantiate Plugin Class: ' . $pluginClass;
            die;
            //throw error
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
            LOG_OUTPUT_PLUGINS,
            VERBOSE
        );

        $results = $plugin->$event();

        if ($results === false) {

            Services::Profiler()->set(
                'EventService->schedule Event '
                    . $event . ' Plugin Class '
                    . $class
                    . ' Failed. ',
                LOG_OUTPUT_PLUGINS
            );

            echo '<br />Error from : ' . $pluginClass . ' for <br />';
            echo '<pre>';
            var_dump($arguments);
            echo '</pre>';
            //throw error
            die;

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
     * Automatically registers all Plugins in the Core, and then Extension (which can override Core), folders
     *
     * Extensions can override Plugins by including a like-named folder in a Plugin directory within the extension
     *
     * The application will find and register the overrides when the extension is used.
     *
     * @return  object
     * @since   1.0
     */
    protected function registerInstalledPlugins()
    {
        Services::Profiler()->set('EventService->registerInstalledPlugins ', LOG_OUTPUT_PLUGINS, VERBOSE);

        $plugins = Services::Filesystem()->folderFolders(PLATFORM_FOLDER . '/' . 'Plugin');

        $pluginClass = 'Molajo\\Plugin\\Plugin\\Plugin';

        $temp = new $pluginClass();

        foreach ($plugins as $folder) {
            if (substr(strtolower($folder), 0, 4) == 'hold') {

            } else {
                $pluginName = $folder . 'Plugin';
                $pluginClass = 'Molajo\\Plugin\\' . $folder . '\\' . $pluginName;
                $this->registerPlugin($pluginName, $pluginClass);
            }
        }

        $plugins = Services::Filesystem()->folderFolders(EXTENSIONS . '/' . 'Plugin');

        foreach ($plugins as $folder) {
            if (substr(strtolower($folder), 0, 4) == 'hold') {

            } else {
                $pluginName = $folder . 'Plugin';
                $pluginClass = 'Extension\\Plugin\\' . $folder . '\\' . $pluginName;
                $this->registerPlugin($pluginName, $pluginClass);
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
            LOG_OUTPUT_PLUGINS,
            VERBOSE
        );

        $exists = Services::Registry()->exists('Events', $event);

        if ($exists === true) {
            $count = Services::Registry()->get('Events', $event);
            $count++;

        } else {
            Services::Registry()->createRegistry($event);
            $count = 1;
        }

        Services::Registry()->set('Events', $event, $count);
        Services::Registry()->set($event, $pluginName, $pluginClass);

        $exists = Services::Registry()->exists('Plugins');
        if ($exists === true) {
        } else {
            Services::Registry()->createRegistry('Plugins');
        }

        Services::Registry()->set('Plugins', $pluginName, 1);

        return $this;
    }
}
