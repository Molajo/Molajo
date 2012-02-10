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
 * Aliases are set for each of the base classes
 *
 * MolajoBase is aliased as Molajo
 * Each process class is aliased as its subject, ex. MolajoRenderer is Renderer
 * Combined, the shortcut alias is Molajo::Subject, ex. MolajoRenderer
 *
 * Aliases are set in applications/includes/aliases.php
 */
class MolajoBase
{
    /**
     * Alias Molajo::Site
     *
     * @var    object MolajoSite
     * @since  1.0
     */
    public static $site = null;

    /**
     * Alias Molajo::Application
     *
     * @var    object MolajoApplication
     * @since  1.0
     */
    public static $application = null;

    /**
     * Alias Molajo::Request
     *
     * @var    object MolajoRequest
     * @since  1.0
     */
    public static $request = null;

    /**
     * Alias Molajo::Parser
     *
     * @var    object MolajoParser
     * @since  1.0
     */
    public static $parser = null;

    /**
     * Alias Molajo::Responder
     *
     * @var    object MolajoResponder
     * @since  1.0
     */
    public static $responder = null;

    /**
     * Alias Molajo::Services
     *
     * @var    object MolajoServices
     * @since  1.0
     */
    public static $services = null;

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
     *
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
     * @return MolajoRequest|null
     * @since 1.0
     */
    public static function getRequest($request = null,
                                      $override_request_url = null,
                                      $override_asset_id = null)
    {
        if (self::$request) {
        } else {
            self::$request =
                MolajoRequest::getInstance(
                    $request,
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
     * @return  MolajoRequest|null
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
     * @return  Responder|null
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
     * @param   Registry|null $config
     *
     * @return  Services|null
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

