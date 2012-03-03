<?php
/**
 * @package     Molajo
 * @subpackage  Service
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application\Service;

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
Class Database
{
    /**
     * Static instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

    /**
     * Database connection
     *
     * @var    object
     * @since  1.0
     */
    protected $db;

    /**
     * getInstance
     * @static
     * @return bool|object
     * @since  1.0
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new DatabaseService();
        }
        return self::$instance;
    }

    /**
     * __construct
     *
     * @return object
     * @since  1.0
     */
    public function __construct()
    {
        return $this->connect();
    }

    /**
     * connect
     *
     * @return mixed
     * @throws RuntimeException
     * @since 1.0
     */
    public function connect($database_class = 'JDatabase',
                            $configuration_file = null)
    {
        if ($configuration_file === null) {
            $configuration_file = SITE_FOLDER_PATH . '/configuration.php';
        }
        $configuration_class = 'SiteConfiguration';

        if (file_exists($configuration_file)) {
            require_once $configuration_file;
        } else {
            throw new RuntimeException('Fatal error - Application-Site Configuration File does not exist');
        }

        if (class_exists($configuration_class)) {
            $site = new $configuration_class();
        } else {
            throw new RuntimeException('Fatal error - Configuration Class does not exist');
        }

        /** database connection specific elements */
        $database_type = $database_class . '_dbtype';
        $host = $database_class . '_host';
        $user = $database_class . '_user';
        $password = $database_class . '_password';
        $db = $database_class . '_db';
        $dbprefix = $database_class . '_dbprefix';

        /** set connection options */
        $options = array(
            'driver' => $site->$database_type,
            'host' => $site->$host,
            'user' => $site->$user,
            'password' => $site->$password,
            'database' => $site->$db,
            'prefix' => $site->$dbprefix);

        /** connect */
        $this->db = $database_class::getInstance($options);

        if ($this->db == null
            || $this->db->getErrorNum() > 0) {
            header('HTTP/1.1 500 Internal Server Error');
            jexit('Database Connection Failed.');
        }

        $this->db->debug($site->debug);

        return $this->db;
    }
}
