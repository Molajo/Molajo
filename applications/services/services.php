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
 * Instantiates a static instance of a service and stores the instance in a
 * static array for reuse with subsequent calls
 *
 * Creating a service:
 * 1. The class must be named 'Molajo' . Name of service . 'Service'
 * 2. Each class must have a getInstance and __construct methods
 * 3. Each class must have a connect method which invokes the logic that
 * should be retained in the static array so that it's not re-executed
 *
 * To use:
 * 1. For services required at startup, add an XML entry to services.xml
 * This file is used during bootstrapping in the index.php.
 *
 * 2. For connecting services on demand, use this syntax:
 *
 * $nos = Services::Connect('Name of Service', array($parameters) );
 *
 * To use the connection:
 * ...whether the connection is initated in bootstrapping or on demand
 *
 * 1. Set the connect => Services::connect('language')
 * 2. Then, using chaining, add the method desired:
 *
 * Services::connect('language')
 *            ->load ($path,
 *                    Molajo::Application()->get('language'),
 *                    false,
 *                    false
 *                    );
 */
class MolajoServices
{
    /**
     * Array of Connected Services
     *
     * @static
     * @var    $connection
     * @since  1.0
     */
    protected $connection = array();

    /**
     * Static Instance
     *
     * @static
     * @var    $instance
     * @since  1.0
     */
    public static $instance = null;

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
    public function connectStandardServices()
    {
        $services = simplexml_load_file(
            MOLAJO_APPLICATIONS_CORE . '/services/services.xml'
        );

        if (count($services) == 0) {
            return;
        }

        foreach ($services->service as $s) {

            $serviceName = (string)$s->name;

            echo 'Service Name ' . $serviceName . '<br /><br />';

            if (substr(basename($serviceName), 0, 4) == 'HOLD') {
            } else {
                $serviceClass = (string)$s->serviceClass;

                if (trim($serviceClass == '')) {
                    break;
                }

                $instanceParameters = array();
                if (isset($s->getInstance->parameters->parameter)) {
                    foreach ($s->getInstance->parameters->parameter as $p) {
                        $name = (string)$p['name'];
                        $value = (string)$p['value'];
                        $instanceParameters[$name] = $value;
                    }
                }
                $connectParameters = array();
                if (isset($s->connect->parameters->parameter)) {
                    foreach ($s->connect->parameters->parameter as $p) {
                        $name = (string)$p['key'];
                        $value = (string)$p['value'];
                        $connectParameters[$name] = $value;
                    }
                }

                $this->connect(
                    $serviceName,
                    $serviceClass,
                    $instanceParameters,
                    $connectParameters
                );
            }
        }
        return true;
    }

    /**
     * connect
     *
     * Connections "on demand" return existing connections, if existing
     *
     * @param $serviceName
     * @param $serviceClass
     * @param $instanceParameters
     * @param $connectParameters
     *
     * @return bool
     * @since 1.0
     */
    public function connect($serviceName,
                            $serviceClass = null,
                            $instanceParameters = array(),
                            $connectParameters = array())
    {
        if (isset($this->connection[$serviceName])) {
            return $this->connection[$serviceName];

        } else {

            if ($serviceClass == null || trim($serviceClass == '')) {
                $serviceClass = 'Molajo' . ucfirst($serviceName) . 'Service';
            }
            $this->makeConnection(
                $serviceName,
                $serviceClass,
                $instanceParameters,
                $connectParameters
            );
        }
    }

    /**
     * makeConnection
     *
     * Activate the specific service and store results in array
     *
     * @param $serviceName
     * @param $serviceClass
     * @param $instanceParameters
     * @param $connectParameters
     *
     * @return bool|mixed
     * @since 1.0
     */
    protected function makeConnection($serviceName,
                                      $serviceClass,
                                      $instanceParameters,
                                      $connectParameters)
    {
        if (isset($this->connection[$serviceName])) {
            return false;
        }

        if (class_exists($serviceClass)) {

            $this->connection[$serviceName] = '';

            /** instantiate a static instance of the class */
            if (method_exists($serviceClass, 'getInstance')) {
                $this->connection[$serviceName] =
                    call_user_func(array($serviceClass, 'getInstance'), $instanceParameters);
            }

            /** connect in object context */
            if (method_exists($serviceClass, 'connect')) {
                /** parameters from array to string */
                $cp ='';
                foreach ($connectParameters as $key => $value) {
                    if ($cp !== '') {
                        $cp .= ',';
                    }
                    $cp .= '$' . $key . '="' . $value . '"';
                }

                /** connect */
                $objectContext = new $serviceClass ();
                $execute = '$connection = $objectContext->connect('.$cp.');';
                eval($execute);
                if ($connection == false) {
                    //todo: amy error handling
                } else {
                    $this->connection[$serviceName] = $connection;
                }
            }
        }
        return true;

        echo '<pre>';
        var_dump($this->connection[$serviceName]);
        echo '</pre>';

        return true;

    }
}

