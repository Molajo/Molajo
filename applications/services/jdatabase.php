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
     * connect
     *
     * Connect to the database
     *
     * @static
     * @return JDatabase
     */
    public static function connect($config = null)
    {
        if ($config instanceof Registry) {
            $site = $config;

        } else {

            $file = MOLAJO_SITE_FOLDER_PATH . '/configuration.php';
            if (file_exists($file)) {
                require_once $file;
            } else {
                throw new RuntimeException('Fatal error - Application-Site Configuration File does not exist');
                return;
            }

            $site = new MolajoSiteConfiguration();
        }

        $options = array(
            'driver' => $site->dbtype,
            'host' => $site->host,
            'user' => $site->user,
            'password' => $site->password,
            'database' => $site->db,
            'prefix' => $site->dbprefix);

        $db = JDatabase::getInstance($options);

        if (MolajoError::isError($db)) {
            header('HTTP/1.1 500 Internal Server Error');
            jexit('Database Error: ' . (string)$db);
        }

        if ($db->getErrorNum() > 0) {
            MolajoError::raiseError(500, TextService::sprintf('MOLAJO_UTIL_ERROR_CONNECT_db', $db->getErrorNum(), $db->getErrorMsg()));
        }

        $db->debug($site->debug);

        return $db;
    }
}
