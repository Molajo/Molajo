<?php
/**
 * Database Service
 *
 * @package      Molajo
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Services\Database;

defined('MOLAJO') or die;

/**
 * Database Connection
 *
 * @package     Molajo
 * @subpackage  Service
 * @since       1.0
 */
Class DatabaseService
{
    /**
     * Database instance
     *
     * @var    object
     * @since  1.0
     */
    protected $database_instance;

    /**
     * Name
     *
     * @var    string
     * @since  1.0
     */
    protected $name;

    /**
     * Options
     *
     * @var    array
     * @since  1.0
     */
    protected $options;

    /**
     * Model Registry
     *
     * @var    array
     * @since  1.0
     */
    protected $model_registry = array();

    /**
     * List of Properties
     *
     * @var    object
     * @since  1.0
     */
    protected $property_array = array(
        'database_instance',
        'name',
        'options',
        'model_registry'
    );

    /**
     * Configuration invokes this method to initialise the profiler service (on or off)
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
     * @param  string  $key
     * @param  null    $default
     *
     * @return  mixed
     * @since   1.0
     */
    public function get($key, $default = null)
    {
        if (in_array($key, $this->property_array)) {
            if (isset($this->$key)) {
            } else {
                $this->$key = $default;
            }

            return $this->$key;
        }

        $key = 'data_object_' . $key;

        if (isset($this->model_registry[$key])) {
        } else {
            $this->model_registry[$key] = $default;
        }

        return $this->model_registry[$key];
    }

    /**
     * set property
     *
     * @param   string  $key
     * @param   mixed   $value
     *
     * @return  mixed
     * @since   1.0
     */
    public function set($key, $value)
    {
        if (in_array($key, $this->property_array)) {
            $this->$key = $value;

            return $this->$key;
        }

        $key = 'data_object_' . $key;

        $this->model_registry[$key] = $value;

        return $this->model_registry[$key];
    }

    /**
     * Connect to Database
     *
     * @param   object  $model_registry
     *
     * @return  mixed
     * @since   1.0
     * @throws  \RuntimeException
     */
    public function connect($model_registry)
    {
        $this->set('model_registry', $model_registry);

        $this->options = array(
            'driver'   => preg_replace(
                '/[^A-Z0-9_\.-]/i',
                '',
                $this->get('db_type')
            ),
            'host'     => $this->get('db_host'),
            'user'     => $this->get('db_user'),
            'password' => $this->get('db_password'),
            'database' => $this->get('db'),
            'prefix'   => $this->get('db_prefix'),
            'select'   => true
        );

        $this->name = $this->options['driver'];

        $class_namespace = $this->get('service_class_connection_namespace');

        if (class_exists($class_namespace)) {
        } else {
            throw new \RuntimeException(sprintf('Unable to load Database Driver: %s', $this->options['driver']));
        }

        try {
            $this->database_instance = new $class_namespace($this->options);

            return $this->database_instance;

        } catch (\Exception $e) {
            throw new \RuntimeException(sprintf('Unable to connect to the Database: %s', $e->getMessage()));
        }

    }

    /**
     * Get the current query object for the current database connection
     *
     * @return  object
     * @since   1.0
     * @throws  \RuntimeException
     */
    public function getQuery()
    {
        $query_namespace = $this->get('service_class_query_method');

        if (class_exists($query_namespace)) {
        } else {
            throw new \RuntimeException('Database: Query Class not found');
        }

        return new $query_namespace($this->database_instance);
    }
}
