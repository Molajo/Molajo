<?php
/**
 * @package     Molajo
 * @subpackage  Service
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Configuration
 *
 * @package     Molajo
 * @subpackage  Service
 * @since       1.0
 */
class MolajoJdatabaseService
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
            self::$instance = new MolajoJdatabaseService();
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

    }

    /**
     * connect
     *
     * @return mixed
     * @throws RuntimeException
     */
    public function connect($file = null, $configuration_class = null)
    {
        if ($file == null) {
            $configuration_file = MOLAJO_SITE_FOLDER_PATH . '/configuration.php';
            $configuration_class = 'MolajoSiteConfiguration';

        } else if (file_exists($file)) {
            $configuration_file = $file;

        } else {
            $configuration_file = MOLAJO_SITE_FOLDER_PATH . '/configuration.php';
            $configuration_class = 'MolajoSiteConfiguration';
        }

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

        if (MolajoError::isError($this->db)) {
            header('HTTP/1.1 500 Internal Server Error');
            jexit('Database Error: ' . (string)$this->db);
        }

        if ($this->db->getErrorNum() > 0) {
            MolajoError::raiseError(500, Services::Language()->sprintf('MOLAJO_UTIL_ERROR_CONNECT_db', $this->db->getErrorNum(), $this->db->getErrorMsg()));
        }

        $this->db->debug($site->debug);

        return $this->db;
    }
}
