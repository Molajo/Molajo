<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Service\Services\Database;

use Molajo\Service\Services;
use Molajo\Service\Services\Configuration\ConfigurationService;

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
     * Retrieve Site and Application data, set constants and paths
     *
     * @param   null  $configuration_file
     *
     * @return  object
     * @since   1.0
     */
    public function __construct()
    {
        $this->connect();

        return $this;
    }

    /**
     * get
     *
     * @param   $value
     *
     * @return  mixed
     * @since   1.0
     */
    public function get($value)
    {
        return $this->$value;
    }

    /**
     * Connect to Database
     *
     * @return mixed
     * @since   1.0
     * @throws \RuntimeException
     */
    public function connect()
    {
        if (defined('DATABASE_SERVICE')) {
        } else {
            define('DATABASE_SERVICE', true);
            ConfigurationService::getDataobject(DATA_OBJECT_LITERAL, DATABASE_LITERAL);
        }

        $this->options = array(
            'driver' => preg_replace(
                '/[^A-Z0-9_\.-]/i',
                '',
                Services::Registry()->get(DATABASE_LITERAL.DATA_OBJECT_LITERAL, 'db_type')
            ),
            'host' => Services::Registry()->get(DATABASE_LITERAL.DATA_OBJECT_LITERAL, 'db_host'),
            'user' => Services::Registry()->get(DATABASE_LITERAL.DATA_OBJECT_LITERAL, 'db_user'),
            'password' => Services::Registry()->get(DATABASE_LITERAL.DATA_OBJECT_LITERAL, 'db_password'),
            'database' => Services::Registry()->get(DATABASE_LITERAL.DATA_OBJECT_LITERAL, 'db'),
            'prefix' => Services::Registry()->get(DATABASE_LITERAL.DATA_OBJECT_LITERAL, 'db_prefix'),
            'select' => true
        );

        $this->name = Services::Registry()->get(DATABASE_LITERAL.DATA_OBJECT_LITERAL, 'db_type');

        $data_object_connection_namespace = Services::Registry()->get(
            DATABASE_LITERAL.DATA_OBJECT_LITERAL,
            'data_object_connection_namespace'
        );

        if (class_exists($data_object_connection_namespace)) {
        } else {
            throw new \RuntimeException(sprintf('Unable to load Database Driver: %s', $this->options['driver']));
        }

        try {
            $this->db = new $data_object_connection_namespace($this->options);

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
    public function getQuery()
    {
        $data_object_query_namespace = Services::Registry()->get(DATABASE_LITERAL.DATA_OBJECT_LITERAL, 'data_object_query_namespace');

        if (class_exists($data_object_query_namespace)) {
        } else {
            throw new \RuntimeException('Database Query class not found');
        }

        return new $data_object_query_namespace($this->db);
    }
}
