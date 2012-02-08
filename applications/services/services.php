<?php
/**
 * @package     Molajo
 * @subpackage  Services
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Molajo Services: Alias class name "Services"
 *
 * Activates static instance of services defined in the services.xml
 * Activates services when requested the first time
 * Stores instance in a static array for reuse with subsequent calls
 *
 * All services must be named 'Molajo' . Name of service . 'Service'
 *
 * To use:
 * 1. For services required at startup, add an XML entry to services.xml
 *
 * 2. Services can be connected on demand with this syntax:
 *
 * $nos = Services::Connect('Name of Service', array($parameters) );
 *
 */
class MolajoServices
{
    /**
     * Static Instance
     *
     * @static
     * @var    $instance
     * @since  1.0
     */
    public static $instance = null;

    /**
     * Array of Connected Services Services
     *
     * @static
     * @var    $connection
     * @since  1.0
     */
    public static $connection = null;

    /**
     * getInstance
     *
     * @static
     * @return  null
     * @since   1.0
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new MolajoServices();
        }
        return self::$instance;
    }

    /**
     * Class Constructor
     *
     * @return  null
     * @since   1.0
     */
    public function __construct()
    {
        self::$connection = array();
        $this->startup();
    }

    /**
     * startup
     *
     * loads all services defined in the services.xml file
     *
     * @param null|Registry $config
     *
     * @return mixed
     * @since 1.0
     */
    public function startup()
    {
        $services = simplexml_load_file(
            MOLAJO_APPLICATIONS_CORE .
                '/services/services.xml'
        );

        if (count($services) == 0) {
            return;
        }

        foreach ($services->service as $s) {

            $service_name = (string)$s->name;
echo $service_name;
            echo '<pre>';
            var_dump($s);
            echo '</pre>';
                        die;
            if (isset(self::$connection[$service_name])) {
            } else {

                $class = 'Molajo' . ucfirst($service_name) . 'Service';
                $method = 'connect';
                $parameters = (string)$s->parameters;

                self::$connection[$service_name] = $this->makeConnection(
                    $service_name, $class, $method, $parameters
                );
            }
        }
    }

    /**
     * connect
     *
     * Connections "on demand" will return existing connection, if existing,
     * or make the connection at this time
     *
     * @param $service_name
     *
     * @return bool
     * @since 1.0
     */
    public function connect($service_name, $parameters = array())
    {
        if (isset(self::$connection[$service_name])) {
            return self::$connection[$service_name];
        } else {

            $class = 'Molajo' . ucfirst($service_name) . 'Services';
            $method = 'connect';
            $parameters = (array)$parameters;

            self::$connection[$service_name] = $this->makeConnection(
                $service_name, $class, $method, $parameters
            );
        }
    }

    /**
     * makeConnection
     *
     * Activate the specific service and return results to store in the
     * static class variable; used for both startup and ondemand connections
     *
     * @param $service_name
     * @param $class
     * @param $method
     * @param $parameters
     *
     * @return bool|mixed
     * @since 1.0
     */
    protected function activate_service(
        $service_name, $class, $method, $parameters)
    {
        if (isset(self::$connection->$service_name)) {
            return false;
        }

        if (class_exists($class)) {
            if (method_exists($class, $method)) {
                return call_user_func(array($class, $method), $parameters);
            }
        }
        return false;
    }
}

