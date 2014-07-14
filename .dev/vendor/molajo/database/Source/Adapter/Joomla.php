<?php
/**
 * Joomla Database Adapter
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Database\Adapter;

use Exception;
use CommonApi\Exception\RuntimeException;
use CommonApi\Database\ConnectionInterface;
use CommonApi\Database\DatabaseInterface;
use Joomla\Database\DatabaseFactory;

/**
 * Joomla Database Adapter
 *
 * @package     Molajo
 * @subpackage  Database
 * @since       1.0
 */
class Joomla extends AbstractAdapter implements ConnectionInterface, DatabaseInterface
{
    /**
     * Database Factory
     *
     * @var    string
     * @since  1.0
     */
    protected $database;

    /**
     * Constructor
     *
     * @param   array $options
     *
     * @return  mixed
     * @since   1.0
     */
    public function __construct(array $options = array())
    {
        $this->database_type = 'Joomla';

        $this->connect($options);
    }

    /**
     * Set the Database Object
     *
     * @param   array $options
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function connect($options = array())
    {
        $this->options = $options;

        $db_options = array(
            'host'     => $this->options['db_host'],
            'type'     => $this->options['db_type'],
            'user'     => $this->options['db_user'],
            'password' => $this->options['db_password'],
            'database' => $this->options['db_name'],
            'prefix'   => $this->options['db_prefix'],
            'select'   => true
        );

        $connection = $this->connectDatabaseFactory();

        $this->getDriver($connection, $db_options);

        return $this;
    }

    /**
     * Query the database and return a single value as the result
     *
     * @param   string $sql
     *
     * @return  object
     * @since   1.0
     */
    public function loadResult($sql)
    {
        $this->database->setQuery($sql);

        return $this->database->loadResult();
    }

    /**
     * Query the database and return an array of object values returned from query
     *
     * @param   string $sql
     *
     * @return  object
     * @since   1.0
     */
    public function loadObjectList($sql)
    {
        $this->database->setQuery($sql);

        return $this->database->loadObjectList();
    }

    /**
     * Execute the Database Query (SQL can be sent in or derived from Query Object)
     *
     * @param   string $sql
     *
     * @return  object
     * @since   1.0
     */
    public function execute($sql)
    {
        $this->database->setQuery($sql);

        return $this->database->execute();
    }

    /**
     * Returns the primary key following insert
     *
     * @return  integer
     * @since   1.0
     */
    public function getInsertId()
    {
        return $this->database->insertid();
    }

    /**
     * Disconnect from Database
     *
     * @return  $this
     * @since   1.0
     */
    public function disconnect()
    {
        $this->database->disconnect();
    }

    /**
     * Connect to the Database Factory
     *
     * @return  $this
     * @since   1.0
     */
    protected function connectDatabaseFactory()
    {
        try {
            return new DatabaseFactory();
        } catch (Exception $e) {
            throw new RuntimeException(
                'Unable to connect to the Database: Joomla DatabaseFactory '
                . $e->getMessage()
            );
        }
    }

    /**
     * Connect to the Database Driver
     *
     * @return  $this
     * @since   1.0
     */
    protected function getDriver($connection, $db_options)
    {
        try {
            $this->database = $connection->getDriver($this->options['db_type'], $db_options);
        } catch (Exception $e) {
            throw new RuntimeException(
                'Unable to connect to the Joomla Database Factory Driver: '
                . $this->options['db_type'] . ' ' . $e->getMessage()
            );
        }
    }
}
