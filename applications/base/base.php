<?php
/**
 * @package     Molajo
 * @subpackage  Base
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Molajo Base
 *
 * Creates static instances of base clases
 *
 * Aliases are set for each of the base classes:
 * - For example, MolajoBase is aliased as Molajo
 * - Process classes aliased as primary function, ex. MolajoRenderer is Renderer
 * - Combined, the shortcut alias is Molajo::Subject, ex. Molajo::Renderer
 *
 * Aliases are set in applications/includes/aliases.php during bootstrap processes
 */
class MolajoBase
{
    /**
     * Molajo::Site
     *
     * @var    object MolajoSite
     * @since  1.0
     */
    protected static $site = null;

    /**
     * Molajo::Application
     *
     * @var    object MolajoApplication
     * @since  1.0
     */
    protected static $application = null;

    /**
     * Molajo::Request
     *
     * @var    object MolajoRequest
     * @since  1.0
     */
    protected static $request = null;

    /**
     * Molajo::Parser
     *
     * @var    object MolajoParser
     * @since  1.0
     */
    protected static $parser = null;

    /**
     * Molajo::Responder
     *
     * @var    object MolajoResponder
     * @since  1.0
     */
    protected static $responder = null;

    /**
     * Molajo::Services
     *
     * @var    object MolajoServices
     * @since  1.0
     */
    protected static $services = null;

    /**
     * getSite
     *
     * MolajoSite, alias Molajo::Site
     *
     * @static
     * @return  MolajoSite
     * @since   1.0
     */
    public static function getSite()
    {
        if (self::$site) {
        } else {
            self::$site = MolajoSite::getInstance();
        }
        return self::$site;
    }

    /**
     * getApplication
     *
     * MolajoApplication, alias Molajo::Application
     *
     * @static
     * @return  MolajoApplication
     * @since   1.0
     */
    public static function getApplication()
    {
        if (self::$application) {
        } else {
            self::$application = MolajoApplication::getInstance();
        }
        return self::$application;
    }

    /**
     * getRequest
     *
     * MolajoRequest, alias Molajo::Request
     *
     * @static
     * @param null $request
     * @param string $override_request_url
     * @param string $override_asset_id
     *
     * @return MolajoRequest
     * @since 1.0
     */
    public static function getRequest($override_request_url = null,
                                      $override_asset_id = null)
    {
        if (self::$request) {
        } else {
            self::$request =
                MolajoRequest::getInstance(
                    $override_request_url,
                    $override_asset_id
                );
        }
        return self::$request;
    }

    /**
     * getParser
     *
     * MolajoParser, alias Molajo::Parser
     *
     * @static
     * @return  MolajoRequest
     * @since   1.0
     */
    public static function getParser()
    {
        if (self::$parser) {
        } else {
            self::$parser =
                MolajoParser::getInstance();
        }
        return self::$parser;
    }

    /**
     * getResponder
     *
     * MolajoResponder, alias Molajo::Responder
     *
     * @static
     * @return  MolajoResponder
     * @since   1.0
     */
    public static function getResponder()
    {
        if (self::$responder) {
        } else {
            self::$responder =
                MolajoResponder::getInstance();
        }
        return self::$responder;
    }

    /**
     * getServices
     *
     * MolajoServices, alias Molajo::Services
     *
     * @static
     * @return  MolajoServices
     * @since   1.0
     */
    public static function getServices()
    {
        if (self::$services) {
        } else {
            self::$services =
                MolajoServices::getInstance();
        }
        return self::$services;
    }
}
