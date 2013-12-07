<?php
/**
 * Abstract Plugin
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Plugin;

use stdClass;
use CommonApi\Controller\DateInterface;
use CommonApi\Controller\UrlInterface;
use CommonApi\Language\LanguageInterface;
use CommonApi\Model\FieldhandlerInterface;
use CommonApi\Authorisation\AuthorisationInterface;
use Exception\Plugin\PluginException;

/**
 * Abstract Plugin
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @since      1.0
 */
abstract class AbstractPlugin
{
    /**
     * Plugin Name
     *
     * @var    string
     * @since  1.0
     */
    protected $plugin_name = null;

    /**
     * Event Name
     *
     * @var    string
     * @since  1.0
     */
    protected $event_name = null;

    /**
     * Resources
     *
     * @var    object
     * @since  1.0
     */
    protected $resources = null;

    /**
     * Fieldhandler
     *
     * @var    object  CommonApi\Model\FieldhandlerInterface
     * @since  1.0
     */
    protected $fieldhandler = null;

    /**
     * Date Controller
     *
     * @var    object  CommonApi\Controller\DateInterface
     * @since  1.0
     */
    protected $date_controller = null;

    /**
     * Url Controller
     *
     * @var    object  CommonApi\Controller\UrlInterface
     * @since  1.0
     */
    protected $url_controller = null;

    /**
     * Language Instance
     *
     * @var    object CommonApi\Language\LanguageInterface
     * @since  1.0
     */
    protected $language_controller;

    /**
     * Authorisation Controller
     *
     * @var    object  CommonApi\Authorisation\AuthorisationInterface
     * @since  1.0
     */
    protected $authorisation_controller;

    /**
     * Runtime Data
     *
     * @var    array
     * @since  1.0
     */
    protected $runtime_data = array();

    /**
     * Parameters
     *
     * @var    object
     * @since  1.0
     */
    protected $parameters = null;

    /**
     * Query
     *
     * @var    object
     * @since  1.0
     */
    protected $query = null;

    /**
     * Model Registry
     *
     * @var    object
     * @since  1.0
     */
    protected $model_registry = null;

    /**
     * Query Results
     *
     * @var    object
     * @since  1.0
     */
    protected $query_results = null;

    /**
     * View Rendered Output
     *
     * @var    string
     * @since  1.0
     */
    protected $rendered_view = null;

    /**
     * Page Rendered Output
     *
     * @var    string
     * @since  1.0
     */
    protected $rendered_page = null;

    /**
     * Constructor
     *
     * @param   null                   $plugin_name
     * @param   null                   $event_name
     * @param   null                   $resources
     * @param   FieldhandlerInterface  $fieldhandler
     * @param   DateInterface          $date_controller
     * @param   UrlInterface           $url_controller
     * @param   LanguageInterface      $language_controller
     * @param   AuthorisationInterface $authorisation_controller
     * @param   null                   $runtime_data
     * @param   null                   $parameters
     * @param   null                   $query
     * @param   null                   $model_registry
     * @param   null                   $query_results
     * @param   null                   $rendered_view
     * @param   null                   $rendered_page
     *
     * @since  1.0
     */
    public function __construct(
        $plugin_name = null,
        $event_name = null,
        $resources = null,
        FieldhandlerInterface $fieldhandler = null,
        DateInterface $date_controller = null,
        UrlInterface $url_controller = null,
        LanguageInterface $language_controller = null,
        AuthorisationInterface $authorisation_controller = null,
        $runtime_data = null,
        $parameters = null,
        $query = null,
        $model_registry = null,
        $query_results = null,
        $rendered_view = null,
        $rendered_page = null
    ) {
        $this->plugin_name              = $plugin_name;
        $this->event_name               = $event_name;
        $this->resources                = $resources;
        $this->fieldhandler             = $fieldhandler;
        $this->date_controller          = $date_controller;
        $this->url_controller           = $url_controller;
        $this->language_controller      = $language_controller;
        $this->authorisation_controller = $authorisation_controller;
        $this->runtime_data             = $runtime_data;
        $this->parameters               = $parameters;
        $this->query                    = $query;
        $this->model_registry           = $model_registry;
        $this->query_results            = $query_results;
        $this->rendered_view            = $rendered_view;
        $this->rendered_page            = $rendered_page;
    }

    /**
     * Get the current value (or default) of the specified property
     *
     * @param   string $key
     *
     * @return  mixed
     * @since   1.0
     */
    public function get($key)
    {
        if (is_array($key)) {
            echo '<br/>';
            echo 'In Abstract Plugin dumping $key';
            echo '<pre>';
            var_dump($key);
            die;
        }
        if (isset($this->$key)) {
            return $this->$key;
        }

        $results = array();

        $results['event_name']    = $this->event_name;
        $results['data']          = $this->query_results;
        $results['parameters']    = $this->parameters;
        $results['rendered_page'] = $this->rendered_page;
        $results['rendered_view'] = $this->rendered_view;

        return $results;
    }

    /**
     * Get Field Definition for specific Field Name
     *
     * @param   string     $name
     * @param   null|mixed $default
     *
     * @return  mixed
     * @since   1.0
     */
    public function getField($name, $default = null)
    {
        if (isset($this->model_registry->field->$name)) {
        } else {
            $this->model_registry->field->$name = $default;
        }

        return $this->query_results->$name;
    }

    /**
     * getFieldValue retrieves the actual field value from the 'normal' or special field
     *
     * @param   object $field
     *
     * @return  null|mixed
     * @since   1.0
     */
    public function getFieldValue($field)
    {
        if (isset($field['as_name'])) {
            if ($field['as_name'] == '') {
                $name = $field['name'];
            } else {
                $name = $field['as_name'];
            }
        } else {
            $name = $field['name'];
        }

        if (isset($this->query_results->$name)) {
            return $this->query_results->$name;

        } elseif (isset($field['default'])) {
            return $field['default'];
        }

        return null;
    }

    /**
     * Retrieve Fields for a specified Data Type
     *
     * @param   string $type
     *
     * @return  array
     * @since   1.0
     */
    public function getFieldsByType($type)
    {
        $results = array();

        if (isset($this->model_registry['fields'])) {
        } else {
            return array();
        }

        foreach ($this->model_registry['fields'] as $field) {
            if ($field['type'] == $type) {
                $results[] = $field;
            }
        }

        return $results;
    }

    /**
     * Set the value of a property
     *
     * Initially, the setter is used by the plugin_event processPluginclass method
     *  to establish initial property values sent in by the scheduling method
     *
     * Changes to data will be used collected and used by the Mvc
     *
     * @param   string $key
     * @param   string $value
     * @param   string $property
     *
     * @return  mixed
     * @since   1.0
     */
    public function set($key, $value = null, $property = '')
    {
        if (in_array($key, $this->property_array) && $property == '') {
            $this->$key = $value;

            if ($key == 'model_registry') {
                if (isset($this->model_registry['model_registry_name'])) {
                    $this->set('model_registry_name', $this->model_registry['model_registry_name']);
                }
            }

            return $this->$key;
        }

        if ($property == 'model_registry') {
            $this->model_registry->$key = $value;

            return $this->model_registry->$key;
        }

        if ($property == 'model') {
            $this->model->$key = $value;

            return $this->model->$key;
        }

        $this->parameters->$key = $value;

        return $this->parameters->$key;
    }

    /**
     * setField adds a field to the 'normal' or special field group
     *
     * @param   $field
     * @param   $new_field_name
     * @param   $value
     *
     * @return  $this
     * @since   1.0
     */
    public function setField($field, $new_field_name, $value)
    {
        if (is_object($this->query_results)) {
        } else {
            $this->query_results = new stdClass();
        }

        $this->query_results->$new_field_name = $value;

        if (is_array($this->model_registry['fields'])) {
        } else {
            $this->model_registry['fields'] = array();
        }

        if (isset($this->model_registry['fields'])) {
            foreach ($this->model_registry['fields'] as $field) {
                if ($field['type'] == $new_field_name) {
                    return $this;
                }
            }
        }

        $temp                             = $field;
        $temp['name']                     = $new_field_name;
        $this->model_registry['fields'][] = $temp;

        return $this;
    }

    /**
     * saveForeignKeyValue
     *
     * @param   $new_field_name
     * @param   $value
     *
     * @return void
     * @since   1.0
     */
    public function saveForeignKeyValue($new_field_name, $value)
    {
        if (isset($this->query_results->$new_field_name)) {
            return;
        }
        $this->query_results->$new_field_name = $value;

        return;
    }

    /**
     * Filter Input
     *
     * @param  string $key
     * @param  mixed  $value
     * @param  string $filter
     * @param  array  $filter_options
     *
     * @return  mixed
     * @since   1.0
     * @throws  \Exception\Plugin\PluginException
     */
    protected function filter($key, $value = null, $filter, $filter_options)
    {
        try {
            $value = $this->fieldhandler->filter($key, $value, $filter, $filter_options);

        } catch (Exception $e) {
            throw new PluginException
            ('Request: Filter class Failed for Key: ' . $key . ' Filter: ' . $filter . ' ' . $e->getMessage());
        }

        return $value;
    }
}
