<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application\Service;

use Molajo\Application\Services;
use Joomla\database\JDatabaseFactory;

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
Class DatabaseService extends BaseService
{
    /**
     * Static instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

    /**
     * $action_to_action_id
     *
     * ACL Action literal to database pk
     *
     * @var    Registry
     * @since  1.0
     */
    protected $action_to_action_id;

    /**
     * $task_to_action
     *
     * Task to ACL Action list
     *
     * @var    Registry
     * @since  1.0
     */
    protected $task_to_action;

    /**
     * $action_to_controller
     *
     * ACL Action to Molajo Controller list
     *
     * @var    Registry
     * @since  1.0
     */
    protected $action_to_controller;

    /**
     * getInstance
     *
     * @static
     * @return bool|object
     * @since  1.0
     */
    public static function getInstance($database_class = 'JDatabaseFactory', $configuration_file = null)
    {

        if ($configuration_file === null) {
            $configuration_file = SITE_FOLDER_PATH . '/configuration.php';
        }
        $configuration_class = 'SiteConfiguration';

        if (file_exists($configuration_file)) {
            require_once $configuration_file;
        } else {
            throw new \Exception('Fatal error - Application-Site Configuration File does not exist');
        }

        if (class_exists($configuration_class)) {
            $site = new $configuration_class();
        } else {
            throw new \Exception('Fatal error - Configuration Class does not exist');
        }

        /** database connection specific elements */
        $database_type = strtolower($database_class) . '_dbtype';
        $host = strtolower($database_class) . '_host';
        $user = strtolower($database_class) . '_user';
        $password = strtolower($database_class) . '_password';
        $db = strtolower($database_class) . '_db';
        $dbprefix = strtolower($database_class) . '_dbprefix';
        $namespace = strtolower($database_class) . '_namespace';

        /** set connection options */
		$options = array(
			'driver' => $site->$database_type,
			'host' => $site->$host,
			'user' => $site->$user,
			'password' => $site->$password,
			'database' => $site->$db,
			'prefix' => $site->$dbprefix);

        /** connect */
        $connectDBClass = $site->$namespace;
		   echo $connectDBClass;
        try {
            $connect = $connectDBClass::getInstance();
            $db = $connect->getDriver($site->$database_type, $options);

        } catch (\Exception $e) {
            header('HTTP/1.1 500 Internal Server Error');
            exit(0);
        }

        $db->debug($site->debug);

        return $db;
    }
}
