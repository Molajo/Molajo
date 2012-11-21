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
 * Database
 *
 * Used by the model
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
     * Connect
     *
     * @return  Dataobject
     * @since   1.0
     */
    public function connect()
    {
        if (Services::Registry()->exists('DatabaseDataobject')) {
        } else {
            ConfigurationService::getDataobject('Dataobject', 'Database');
        }

        $this->options = array(
            'driver' => preg_replace('/[^A-Z0-9_\.-]/i', '',
                Services::Registry()->get('DatabaseDataObject', 'dbtype')),
            'host' => Services::Registry()->get('DatabaseDataObject', 'host'),
            'user' => Services::Registry()->get('DatabaseDataObject', 'user'),
            'password' => Services::Registry()->get('DatabaseDataObject', 'password'),
            'database' => Services::Registry()->get('DatabaseDataObject', 'db'),
            'prefix' => Services::Registry()->get('DatabaseDataObject', 'prefix'),
            'select' => true
        );

        $this->name = Services::Registry()->get('DatabaseDataObject', 'dbtype');

        $connectclass = Services::Registry()->get('DatabaseDataObject', 'connectclass');
        if (class_exists($connectclass)) {
        } else {
            throw new \RuntimeException(sprintf('Unable to load Database Driver: %s', $this->options['driver']));
        }

        try {
            $this->db = new $connectclass($this->options);

        } catch (\Exception $e) {

            throw new \RuntimeException(sprintf('Unable to connect to the Database: %s', $e->getMessage()));
        }

        return $this->db;
    }

    /**
     * Get the current query object for the current database connection
     *
     * @return  Database Query Object
     * @since   1.0
     * @throws  \RuntimeException
     */
    public function getQuery()
    {
        $queryclass = Services::Registry()->get('DatabaseDataObject', 'queryclass');
        if (class_exists($queryclass)) {
        } else {
            throw new \RuntimeException('Database Query class not found');
        }

        return new $queryclass($this->db);
    }
}
