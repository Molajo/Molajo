<?php
/**
 * @package     Molajo
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application;

defined('MOLAJO') or die;

use Molajo\Application\Service\AccessService;
//use Molajo\Application\Service\AuthenticationService;
use Molajo\Application\Service\ConfigurationService;
use Molajo\Application\Service\DatabaseService;
use Molajo\Application\Service\DateService;
use Molajo\Application\Service\DispatcherService;
use Molajo\Application\Service\DocumentService;
use Molajo\Application\Service\FileService;
use Molajo\Application\Service\FolderService;
use Molajo\Application\Service\ImageService;
use Molajo\Application\Service\InstallService;
use Molajo\Application\Service\LanguageService;
use Molajo\Application\Service\MailService;
use Molajo\Application\Service\MessageService;
use Molajo\Application\Service\ParameterService;
use Molajo\Application\Service\RequestService;
use Molajo\Application\Service\ResponseService;
use Molajo\Application\Service\SecurityService;
use Molajo\Application\Service\TextService;
use Molajo\Application\Service\UrlService;
use Molajo\Application\Service\UserService;

/**
 * Service
 *
 * @package     Molajo
 * @subpackage  Services
 * @since       1.0
 */
Class Service
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
            self::$instance = new Service();
        }
        return self::$instance;
    }

    /**
     * get
     *
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
    public function set($key, $value = null)
    {
        if (!(is_object($value)) || $value == null) {
            debug('Service::set Service failed to start: ' . $key);
        } else {
            $this->service_connection->set($key, $value);
        }
    }

    /**
     * startServices
     *
     * loads all services defined in the services.xml file
     *
     * @param null|Registry $config
     *
     * @return mixed
     * @since 1.0
     */
    public function startServices()
    {
        $services = simplexml_load_file(
            MOLAJO_APPLICATIONS . '/Configuration/services.xml'
        );
        if (count($services) == 0) {
            return;
        }
        $this->service_connection = new Registry();

        foreach ($services->service as $s) {
            $serviceName = (string)$s->name;

            try {
                $connection = $this->_connectService($s);

            } catch (Exception $e) {
                echo 'Fatal Error: ' . $e->getMessage() . ' ' . $serviceName;
                debug('Service::startServices Service Failed' . ' ' . $serviceName);
                exit(0);
            }

            $this->set($serviceName, $connection);
            debug('Service::startServices Service Connection' . ' ' . $serviceName);
        }
        return;
    }

    /**
     * connectService
     *
     * @param   $service
     * @return  bool
     * @since   1.0
     */
    protected function _connectService($service)
    {
        $serviceName = (string)$service->name;
        if (substr($serviceName, 0, 4) == 'HOLD') {
            return false;
        }

        $serviceClass = (string)$service->serviceClass;
        if (trim($serviceClass == '')) {
            $serviceClass = ucfirst($serviceName);
        }

        /** execute the getInstance method */
        $getInstanceConnection = false;
        if (method_exists($serviceClass, 'getInstance')) {

            /** connect Method Parameters */
            $getInstanceParameters = array();
            if (isset($service->getInstance->parameters->parameter)) {
                foreach ($service->getInstance->parameters->parameter as $p) {
                    $name = (string)$p['key'];
                    $value = (string)$p['value'];
                    $getInstanceParameters[$name] = $value;
                }
            }

            $getInstanceConnection = $this->_connectServiceMethod(
                null,
                $serviceClass,
                'getInstance',
                $getInstanceParameters
            );

            if ($getInstanceConnection == false) {
                return false;
            }
        }

        /** execute the connect method */
        if (method_exists($serviceClass, 'connect')) {

            /** connect Method Parameters */
            $connectParameters = array();
            if (isset($service->connect->parameters->parameter)) {
                foreach ($service->connect->parameters->parameter as $p) {
                    $name = (string)$p['key'];
                    $value = (string)$p['value'];
                    $connectParameters[$name] = $value;
                }
            }

            $connection = $this->_connectServiceMethod(
                $getInstanceConnection,
                $serviceClass,
                'connect',
                $connectParameters
            );

            if ($connection == false) {
                return false;
            } else {
                return $connection;
            }
        } else {
            return $getInstanceConnection;
        }
    }

    /**
     * _connectServiceMethod
     *
     * Execute the Service Method
     *
     * $param $objectContext
     * @param $serviceClass
     * @param $serviceMethod
     * @param $connectParameters
     *
     * @since 1.0
     */
    protected function _connectServiceMethod(
        $objectContext = null,
        $serviceClass,
        $serviceMethod,
        $connectParameters)
    {
        /** parameters from array to string */
        $parms = '';
        if (count($connectParameters) == 0) {
        } else {
            foreach ($connectParameters as $key => $value) {
                if ($parms !== '') {
                    $parms .= ',';
                }
                if ($value == '{{userid}}') {
                    $value = 42;
                }
                $parms .= '$' . $key . '="' . $value . '"';
            }
        }

        /** execute method */
        $connection = '';
        if ($serviceMethod == 'getInstance') {
            $execute = '$connection = $serviceClass::getInstance ' .
                '(' . $parms . ');';
        } else {
            $execute = '$connection = $objectContext->' .
                $serviceMethod .
                '(' . $parms . ');';
        }

        eval($execute);

        return $connection;
    }
}

/**
 *  Molajo Services
 */
class Services extends Service
{
    public static function Access()
    {
        return Molajo::Service()->get('Access');
    }

    public static function Authentication()
    {
        return Molajo::Service()->get('Authentication');
    }

    public static function Configuration()
    {
        return Molajo::Service()->get('Configuration');
    }

    public static function Cookie()
    {
        return Molajo::Service()->get('Cookie');
    }

    public static function Date()
    {
        return Molajo::Service()->get('Date');
    }

    public static function DB()
    {
        return Molajo::Service()->get('jdb');
    }

    public static function Dispatcher()
    {
        return Molajo::Service()->get('Dispatcher');
    }

    public static function Document()
    {
        return Molajo::Service()->get('Document');
    }

    public static function File()
    {
        return Molajo::Service()->get('File');
    }

    public static function Folder()
    {
        return Molajo::Service()->get('Folder');
    }

    public static function Image()
    {
        return Molajo::Service()->get('Image');
    }

    public static function Language()
    {
        return Molajo::Service()->get('Language');
    }

    public static function Mail()
    {
        return Molajo::Service()->get('Mail');
    }

    public static function Message()
    {
        return Molajo::Service()->get('Message');
    }

    public static function Parameter()
    {
        return Molajo::Service()->get('Parameter');
    }

    public static function Request()
    {
        return Molajo::Service()->get('Request');
    }

    public static function Response()
    {
        return Molajo::Service()->get('Response');
    }

    public static function Security()
    {
        return Molajo::Service()->get('Security');
    }

    public static function Session()
    {
        return Molajo::Service()->get('Session');
    }

    public static function Text()
    {
        return Molajo::Service()->get('Text');
    }

    public static function Url()
    {
        return Molajo::Service()->get('URL');
    }

    public static function User()
    {
        return Molajo::Service()->get('User');
    }
}
