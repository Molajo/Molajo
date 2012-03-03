<?php
/**
 * @package     Molajo
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application;
require_once MOLAJO_BASE_FOLDER.'/Molajo/Application/Site.php';
require_once MOLAJO_BASE_FOLDER.'/Molajo/Application/Application.php';
require_once MOLAJO_BASE_FOLDER.'/Molajo/Application/Parse.php';
require_once MOLAJO_BASE_FOLDER.'/Molajo/Application/Renderer.php';
require_once MOLAJO_BASE_FOLDER.'/Molajo/Application/Service.php';
defined('MOLAJO') or die;

Use Molajo\Application\Site;
Use Molajo\Application\Application;
Use Molajo\Application\Request;
Use Molajo\Application\Parse;
Use Molajo\Application\Service;

/**
 * Molajo
 *
 * Creates instances of base classes
 *
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
     * Molajo::Parse
     *
     * @var    object Parse
     * @since  1.0
     */
    protected static $parse = null;

    /**
     * Molajo::Service
     *
     * @var    object Service
     * @since  1.0
     */
    protected static $service = null;

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
     * Molajo::Service
     *
     * @static
     * @return  Service
     * @since   1.0
     */
    public static function Service()
    {
        if (self::$service) {
        } else {
            self::$service = Service::getInstance();
        }
        return self::$service;
    }
}
