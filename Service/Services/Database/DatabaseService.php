<?php
/**
 * @package    Niambie
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    MIT
 */
namespace Molajo\Service\Services\Database;

use Molajo\Service\Services;
use Molajo\Service\Services\Configuration\ConfigurationService;

defined('NIAMBIE') or die;

/**
 * Database Connection
 *
 * @package     Niambie
 * @subpackage  Service
 * @since       1.0
 */
Class DatabaseService
{
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
     * DB Connection
     *
     * @var    object
     * @since  1.0
     */
    protected $db;

    /**
     * Model Registry
     *
     * @var    array
     * @since  1.0
     */
    protected $model_registry = array();

    /**
     * Retrieve Site and Application data, set constants and paths
     *
     * @param   null  $configuration_file
     *
     * @return  object
     * @since   1.0
     */
    public function __construct()
    {
        return $this;
    }

    /**
     * get property
     *
     * @param   $value
     *
     * @return  mixed
     * @since   1.0
     */
    public function get($key)
    {
        if (isset($this->model_registry['data_object_' . $key])) {
            return $this->model_registry['data_object_' . $key];
        }

        return $this->$key;
    }

    /**
     * set property
     *
     * @param   string  $key
     * @param   varies  $value
     * @param   null    $property
     *
     * @return  mixed
     * @since   1.0
     */
    public function set($key, $value, $property = null)
    {
        if ($property == 'model_registry') {
            $this->model_registry[$key] = $value;
            return $this->model_registry[$key];
        }

        $this->$key = $value;
        return $this->$key;
    }

    /**
     * Connect to Database
     *
     * @return  mixed
     * @since   1.0
     * @throws  \RuntimeException
     */
    public function connect($model_registry)
    {
        $this->set('model_registry', $model_registry);

        if (defined('DATABASE_SERVICE')) {
        } else {
            define('DATABASE_SERVICE', true);
        }

        $this->options = array(
            'driver' => preg_replace(
                '/[^A-Z0-9_\.-]/i',
                '',
                $this->get('db_type')
            ),
            'host' => $this->get('db_host'),
            'user' => $this->get('db_user'),
            'password' => $this->get('db_password'),
            'database' => $this->get('db'),
            'prefix' => $this->get('db_prefix'),
            'select' => true
        );

        $this->name = $this->get('db_type');

        $service_class_connection_namespace =  $this->get('service_class_connection_namespace');

        if (class_exists($service_class_connection_namespace)) {
        } else {
            throw new \RuntimeException(sprintf('Unable to load Database Driver: %s', $this->options['driver']));
        }

        try {
            $this->db = new $service_class_connection_namespace($this->options);

        } catch (\Exception $e) {
            throw new \RuntimeException(sprintf('Unable to connect to the Database: %s', $e->getMessage()));
        }

        return $this->db;
    }

    /**
     * Get the current query object for the current database connection
     *
     * @return  object
     * @since   1.0
     * @throws  \RuntimeException
     */
    public function getQuery($db)
    {
        $service_class_query_namespace = $this->get('service_class_query_namespace');

        if (class_exists($service_class_query_namespace)) {
        } else {
            throw new \RuntimeException('Database: Query Class not found');
        }

        return new $service_class_query_namespace($db);
    }
}
