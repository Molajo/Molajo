<?php
/**
 * @package     Molajo
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application;

defined('MOLAJO') or die;

Use Molajo\Application\Site;
Use Molajo\Application\Application;
Use Molajo\Application\Services;
Use Molajo\Application\Request;
Use Molajo\Application\Includer;
Use Molajo\Application\Service\RequestService;

/**
 * Molajo
 *
 * Creates instances of base classes
 */
class Molajo
{
    /**
     * Molajo::Site
     *
     * @var    object Site
     * @since  1.0
     */
    protected static $site = null;

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
     * Molajo::RequestService
     *
     * @var    object Parse
     * @since  1.0
     */
    protected static $request_service = null;

    /**
     * Molajo::Site
     *
     * @static
     * @return  Site
     * @since   1.0
     */
    public static function Site()
    {
        if (self::$site) {
        } else {
            self::$site = Site::getInstance();
        }
        return self::$site;
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
        return self::$application;
    }

    /**
     * Molajo::Services
     *
     * @static
     * @return  Services
     * @since   1.0
     */
    public static function Services()
    {
        if (self::$services) {
        } else {
            self::$services = Services::getInstance();
        }
        return self::$services;
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
    public static function Request($override_request_url = null,
                                      $override_asset_id = null)
    {
        if (self::$request) {
        } else {
            self::$request = Request::getInstance(
                $override_request_url,
                $override_asset_id);
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
