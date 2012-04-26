<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application;

use Joomla\registry\JRegistry;

use Molajo\Application\Molajo;

defined('MOLAJO') or die;

/**
 * Service
 *
 * @package     Molajo
 * @subpackage  Services
 * @since       1.0
 */
Class Services
{
    /**
     * Static instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

    /**
     * Service Connections
     *
     * @var object
     * @since 1.0
     */
    protected $service_connection;

    /**
     * getInstance
     *
     * @static
     * @return bool|object
     * @since  1.0
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new Services();
        }
        return self::$instance;
    }

    /**
     * Retrieves service key value pair
     *
     * @param  string  $key
     * @param  string  $default
     *
     * @return  mixed
     *
     * @since   1.0
     */
    public function get($key, $default = null)
    {
        return $this->service_connection->get($key, $default);
    }

    /**
     * Used to connect to services
     *
     * @static
     * @param  $name
     * @param  $arguments
     */
    public static function __callStatic($name, $arguments)
    {
        return Application::Service()->get($name . 'Service');
    }

    /**
     * loads all services defined in the services.xml file
     *
     * @return  boolean
     * @since   1.0
     */
    public function startServices()
    {
        /** store connection messages */
        $this->message = array();

        /** store service connections  */
        $this->service_connection = new JRegistry();

        /** start services in this sequence */
		$services = Service::Configuration()->loadXML('Services');

        foreach ($services->service as $item) {
            $try = true;
            $connection = '';

            /** class name */
            $entry = (string)$item . 'Service';
            $serviceClass = 'Molajo\\Service\\' . $entry;

            /** method name */
            $serviceMethod = 'getInstance';

            /** trap errors for missing class or method */
            if (class_exists($serviceClass)) {
                if (method_exists($serviceClass, $serviceMethod)) {
                } else {
                    $try = false;
                    $connection = $serviceClass . '::' . $serviceMethod . ' Class does not exist';
                }
            } else {
                $try = false;
                $connection = $serviceClass . ' Class does not exist';
            }

            /** make service connection */
            if ($try === true) {
                try {
                    $connection = $serviceClass::$serviceMethod();

                } catch (\Exception $e) {
                    $connection = 'Fatal Error: ' . $e->getMessage();
                }
            }

            /** store connection or error message */
            $this->set($entry, $connection, $try);
        }

        foreach ($this->message as $message) {
            Service::Debug()->set($message);
        }

        return true;
    }

    /**
     * set
     *
     * Stores the service connection
     *
     * @param  string  $key
     * @param  mixed   $value
     *
     * @return  mixed
     * @since   1.0
     */
    private function set($key, $value = null, $try = true)
    {
        $i = count($this->message);

        if ($value == null || $try == false
        ) {
            $this->message[$i] = 'Service: ' . $key . ' FAILED' . $value;

        } else {
            $this->service_connection->set($key, $value);
            $this->message[$i] = 'Service: ' . $key . ' started successfully. ';
        }
    }
}
