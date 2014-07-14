<?php
/**
 * Abstract Adapter Database Class
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Database\Adapter;

use Exception;
use CommonApi\Exception\RuntimeException;
use CommonApi\Database\DatabaseInterface;
use CommonApi\Database\ConnectionInterface;

/**
 * Database Connection
 *
 * @package     Molajo
 * @subpackage  Database
 * @since       1.0
 */
abstract class AbstractAdapter implements ConnectionInterface, DatabaseInterface
{
    /**
     * Database Type
     *
     * @var    string
     * @since  1.0
     */
    protected $database_type;

    /**
     * Options
     *
     * @var    array
     * @since  1.0
     */
    protected $options;

    /**
     * Database Instance
     *
     * @var    object
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
    abstract public function __construct(array $options = array());

    /**
     * Set the Database Object
     *
     * @param   array $options
     *
     * @return  $this
     * @since   1.0
     */
    abstract public function connect($options = array());

    /**
     * Escape the value
     *
     * @param   string $value
     *
     * @return  string
     * @since   1.0
     */
    public function escape($value)
    {
        try {
            return $this->database->escape($value);

        } catch (Exception $e) {

            throw new RuntimeException('Database Adapter execute Exception: ' . $e->getMessage());
        }
    }

    /**
     * Query the database and return a single value as the result
     *
     * @param   string $sql
     *
     * @return  object
     * @since   1.0
     */
    abstract public function loadResult($sql);

    /**
     * Query the database and return an array of object values returned from query
     *
     * @param   string   $sql
     * @param   null|int $offset
     * @param   null|int $limit
     *
     * @return  object
     * @since   1.0
     */
    abstract public function loadObjectList($sql);

    /**
     * Execute the Database Query
     *
     * @param   string $sql
     *
     * @return  object
     * @since   1.0
     */
    abstract public function execute($sql);

    /**
     * Returns the primary key following insert
     *
     * @return  integer
     * @since   1.0
     */
    abstract public function getInsertId();

    /**
     * Disconnect from Database
     *
     * @return  $this
     * @since   1.0
     */
    abstract public function disconnect();
}
