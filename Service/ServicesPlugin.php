<?php
/**
 * ServicesPlugin
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service;

defined('NIAMBIE') or die;

/**
 * ServicesPlugin Base Class
 *
 * @author     Amy Stephen
 * @license    MIT
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @since      1.0
 */
class ServicesPlugin
{
    /**
     * Front controller Instance
     *
     * @var    object
     * @since  1.0
     */
    protected $frontcontroller_instance;

    /**
     * Service Class
     *
     * @var    object
     * @since  1.0
     */
    protected $service_class_name;

    /**
     * Services Class Name
     *
     * @var    string
     * @since  1.0
     */
    protected $service_class_namespace = null;

    /**
     * Services Class Name
     *
     * @var    string
     * @since  1.0
     */
    protected $service_class_instance = null;

    /**
     * Plugin currently activated
     *
     * @var    string
     * @since  1.0
     */
    protected $plugin_class_name;

    /**
     * Event current scheduled
     *
     * @var    string
     * @since  1.0
     */
    protected $plugin_event;

    /**
     * List of Plugin Property Array
     *
     * Must match Plugin Class $property_array Property
     *
     * @var    object
     * @since  1.0
     */
    protected $plugin_property_array = array(
        'frontcontroller_instance',
        'service_class_name',
        'service_class_namespace',
        'service_class_instance',
        'plugin_class_name',
        'plugin_event'
    );

    /**
     * Initialise Plugin Resources
     *
     * @return  void
     * @since   1.0
     */
    public function initialise()
    {
        return;
    }

    /**
     * Get the current value (or default) of the specified property
     *
     * @param   string  $key
     * @param   mixed   $default
     * @param   string  $property
     *
     * @return  mixed
     * @since   1.0
     * @throws  \OutOfRangeException
     */
    public function get($key, $default = null, $property = '')
    {
        $value = null;

        if (in_array($key, $this->plugin_property_array) && $property == '') {
            $value = $this->$key;

            return $value;
        }

        throw new \OutOfRangeException('ServicesPlugin: ' . $this->plugin_class_name .
            ' Event ' . $this->plugin_event .
            ' attempting to get value for unknown key: ' . $key);
    }

    /**
     * Set the value of a property
     *
     * Initially, the setter is used by the plugin_event processPluginClass method
     *  to establish initial property values sent in by the scheduling method
     *
     * Changes to data will be used collected and used by the MVC
     *
     * @param   string  $key
     * @param   string  $value
     * @param   string  $property
     *
     * @return  mixed
     * @since   1.0
     * @throws  \OutOfRangeException
     */
    public function set($key, $value = null, $property = '')
    {
        if (in_array($key, $this->plugin_property_array) && $property == '') {
            $this->$key = $value;

            return $this->$key;
        }

        throw new \OutOfRangeException('Plugin: ' . $this->plugin_class_name .
            ' ServicesEvent ' . $this->plugin_event .
            ' attempting to set value for unknown property: ' . $key);
    }

    /**
     * After Plugin class is instantiated but before the Service Initialisation Method Runs
     *
     * @return  bool
     * @since   1.0
     */
    public function onBeforeServiceStart()
    {
        return;
    }

    /**
     * After Service Initialisation Method Runs
     *
     * @return  bool
     * @since   1.0
     */
    public function onAfterServiceStart()
    {
        return true;
    }

    /**
     * After Save Service Instance
     *
     * @return  bool
     * @since   1.0
     */
    public function OnAfterSaveServiceInstance()
    {
        return true;
    }
}
