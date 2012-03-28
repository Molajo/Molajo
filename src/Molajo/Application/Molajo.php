<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application;

use Molajo\Application\Service\RequestService;
use Molajo\Extension\Helper;

defined('MOLAJO') or die;

/**
 * Molajo
 *
 * Creates instances of base classes
 */
class Molajo
{
    /**
     * Molajo::Application
     *
     * @var    object Application
     * @since  1.0
     */
    protected static $application = null;

    /**
     * Molajo::Request
     *
     * @var    object Request
     * @since  1.0
     */
    protected static $request = null;

    /**
     * Molajo::Service
     *
     * @var    object Service
     * @since  1.0
     */
    protected static $services = null;

    /**
     * Molajo::Parse
     *
     * @var    object Parse
     * @since  1.0
     */
    protected static $parse = null;

    /**
     * Molajo::Helper
     *
     * @var    object Helper
     * @since  1.0
     */
    protected static $helper = null;

    /**
     * Molajo::RequestService
     *
     * @var    object Parse
     * @since  1.0
     */
    protected static $request_service = null;

    /**
     * Default code if lookup value does not exist
     *
     * @var    integer  constant
     * @since  1.0
     */
    const DEFAULT_CODE = 100000;

    /**
     * Default message if no message is provided
     *
     * @var    string  Constant
     * @since  12.1
     */
    const DEFAULT_MESSAGE = 'Undefined Message';


    /**
     * Return message given message code
     *
     * @param   string  $code  Numeric value associated with message
     *
     * @return  mixed  Array or String
     *
     * @since   12.1
     */
    public function getMessage($code = 0)
    {
        $message = array(
            300100 => 'Invalid key of type. Expected simple.',
            300200 => 'The mcrypt extension is not available.',
            300300 => 'Invalid JCryptKey used with Mcrypt decryption.',
            300400 => 'Invalid JCryptKey used with Mcrypt encryption.',
            300500 => 'Invalid JCryptKey used with Simple decryption.',
            300600 => 'Invalid JCryptKey used with Simple encryption.',
        );

        if ($code == 0) {
            return $message;
        }

        if (isset($message[$code])) {
            return $message[$code];
        }

        return self::DEFAULT_MESSAGE;
    }

    /**
     * Return code given message
     *
     * @param   string  $code  Numeric value associated with message
     *
     * @return  mixed  Array or String
     *
     * @since   12.1
     */
    public function getMessageCode($message = null)
    {
        $messageArray = self::get(0);

        $code = array_search($message, $messageArray);

        if ((int)$code == 0) {
            $code = self::DEFAULT_CODE;
        }

        return $code;
    }

    /**
     * Molajo::Application
     *
     * @static
     * @return  Application
     * @since   1.0
     */
    public static function Application()
    {
        if (self::$application) {
        } else {
            self::$application = Application::getInstance();
        }
        if (!is_callable('mcrypt_encrypt')) {
            throw new RuntimeException(JCryptMessage::get(300200), 300200);
        }
        return self::$application;
    }

    /**
     * Molajo::Services
     *
     * @static
     * @return  null|object
     * @throws  InvalidArgumentException
     * @since   1.0
     */
    public static function Services()
    {
        if (self::$services) {
        } else {
            self::$services = Services::getInstance();
        }

        if (self::$services) {
            return self::$services;
        } else {
            throw new InvalidArgumentException(JCryptMessage::get(300600), 300600);
        }
    }

    /**
     * Molajo::Request
     *
     * @static
     * @param null $request
     * @param string $override_request_url
     * @param string $override_asset_id
     *
     * @return Request
     * @since 1.0
     */
    public static function Request()
    {
        if (self::$request) {
        } else {
            self::$request = Request::getInstance();
        }
        return self::$request;
    }

    /**
     * Molajo::Parse
     *
     * @static
     * @return  Parse
     * @since   1.0
     */
    public static function Parse()
    {
        if (self::$parse) {
        } else {
            self::$parse = Parse::getInstance();
        }
        return self::$parse;
    }

    /**
     * Molajo::Helper
     *
     * @static
     * @return  Parse
     * @since   1.0
     */
    public static function Helper()
    {
        if (self::$helper) {
        } else {
            self::$helper = Helper::getInstance();
        }
        return self::$helper;
    }

    /**
     * Molajo::RequestService
     *
     * @static
     * @return  Parse
     * @since   1.0
     */
    public static function RequestService()
    {
        if (self::$request_service) {
        } else {
            self::$request_service = RequestService::getInstance();
        }
        return self::$request_service;
    }
}

abstract class JFactory extends Molajo
{
}
