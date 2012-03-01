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
 * Joomla Database
 *
 * Used by the model
 *
 * @package     Molajo
 * @subpackage  Service
 * @since       1.0
 */
Class JdatabaseService
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
     * @return null
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
     */
    public function connect()
    {
        $configuration_file = SITE_FOLDER_PATH . '/configuration.php';
        $configuration_class = 'MolajoSiteConfiguration';

        if (file_exists($configuration_file)) {
            require_once $configuration_file;
        } else {
            throw new RuntimeException('Fatal error - Application-Site Configuration File does not exist');
        }

        $site = new $configuration_class();

        $options = array(
            'driver' => $site->dbtype,
            'host' => $site->host,
            'user' => $site->user,
            'password' => $site->password,
            'database' => $site->db,
            'prefix' => $site->dbprefix);

        $this->db = JDatabase::getInstance($options);

        if ($this->db == null) {
            header('HTTP/1.1 500 Internal Server Error');
            jexit('Database Connection Failed.');
        }

        if ($this->db->getErrorNum() > 0) {
            MolajoError::raiseError(500, Services::Language()->sprintf('MOLAJO_UTIL_ERROR_CONNECT_db', $this->db->getErrorNum(), $this->db->getErrorMsg()));
        }

        $this->db->debug($site->debug);

        return $this->db;
    }
}
