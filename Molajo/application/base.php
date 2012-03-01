<?php
/**
 * @package     Molajo
 * @subpackage  Base
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application;

defined('MOLAJO') or die;

/**
 * Base
 *
 * Creates instances of base clases
 *
 * Aliases are set for each of the base classes:
 * - For example, Base is aliased as Molajo
 * - Combined, the shortcut alias is Molajo::Subject, ex. Molajo::Renderer
 *
 * Aliases are set in applications/includes/aliases.php during bootstrap processes
 */
Class Base
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
     * Molajo::Parser
     *
     * @var    object Parser
     * @since  1.0
     */
    protected static $parser = null;

    /**
     * Molajo::Services
     *
     * @var    object Services
     * @since  1.0
     */
    protected static $services = null;

    /**
     * getSite
     *
     * Site, alias Molajo::Site
     *
     * @static
     * @return  Site
     * @since   1.0
     */
    public static function getSite()
    {
        if (self::$site) {
        } else {
            self::$site = Site::getInstance();
        }
        return self::$site;
    }

    /**
     * getApplication
     *
     * Application, alias Molajo::Application
     *
     * @static
     * @return  Application
     * @since   1.0
     */
    public static function getApplication()
    {
        if (self::$application) {
        } else {
            self::$application = Application::getInstance();
        }
        return self::$application;
    }

    /**
     * getRequest
     *
     * Request, alias Molajo::Request
     *
     * @static
     * @param null $request
     * @param string $override_request_url
     * @param string $override_asset_id
     *
     * @return Request
     * @since 1.0
     */
    public static function getRequest($override_request_url = null,
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
     * getParser
     *
     * Parser, alias Molajo::Parser
     *
     * @static
     * @return  Parser
     * @since   1.0
     */
    public static function getParser()
    {
        if (self::$parser) {
        } else {
            self::$parser = Parser::getInstance();
        }
        return self::$parser;
    }

    /**
     * getServices
     *
     * Services, alias Molajo::Services
     *
     * @static
     * @return  Services
     * @since   1.0
     */
    public static function getServices()
    {
        if (self::$services) {
        } else {
            self::$services = Services::getInstance();
        }
        return self::$services;
    }
}
