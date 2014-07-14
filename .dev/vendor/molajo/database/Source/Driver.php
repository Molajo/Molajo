<?php
/**
 * Database Driver
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Database;

use Exception;
use CommonApi\Exception\RuntimeException;
use CommonApi\Database\DatabaseInterface;

/**
 * Database Driver
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @since      1.0.0
 */
class Driver implements DatabaseInterface
{
    /**
     * Database Adapter
     *
     * @var     object  CommonApi\Database\DatabaseInterface
     * @since   1.0
     */
    protected $adapter;

    /**
     * Constructor
     *
     * @param  DatabaseInterface $database
     *
     * @since  1.0
     */
    public function __construct(DatabaseInterface $database)
    {
        $this->adapter = $database;
    }

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
            return $this->adapter->escape($value);

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
    public function loadResult($sql)
    {
        try {
            return $this->adapter->loadResult($sql);
        } catch (Exception $e) {

            throw new RuntimeException('Database Adapter loadResult Exception: ' . $e->getMessage());
        }
    }

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
    public function loadObjectList($sql)
    {
        try {
            return $this->adapter->loadObjectList($sql);

        } catch (Exception $e) {

            throw new RuntimeException('Database Adapter loadObjectList Exception: ' . $e->getMessage());
        }
    }

    /**
     * Execute the Database Query
     *
     * @param   string $sql
     *
     * @return  object
     * @since   1.0
     */
    public function execute($sql)
    {
        try {
            return $this->adapter->execute($sql);
        } catch (Exception $e) {

            throw new RuntimeException('Database Adapter execute Exception: ' . $e->getMessage());
        }
    }

    /**
     * Returns the primary key following insert
     *
     * @return  integer
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function getInsertId()
    {
        try {
            return $this->adapter->getInsertId();
        } catch (Exception $e) {

            throw new RuntimeException('Database Adapter getInsertId Exception: ' . $e->getMessage());
        }
    }
}
