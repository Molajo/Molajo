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
 * Alias Molajo
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
     * Alias Molajo::Renderer
     *
     * @var    object MolajoRenderer
     * @since  1.0
     */
    public static $renderer = null;

    /**
     * Alias Molajo::Responder
     *
     * @var    object MolajoResponder
     * @since  1.0
     */
    public static $responder = null;

    /**
     * Alias Molajo::User
     *
     * @var    object MolajoUser
     * @since  1.0
     */
    public static $user = null;

    /**
     * getSite
     *
     * MolajoSite, alias Molajo::Site
     *
     * @static
     * @return  null|Site
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
     * @param   object JInput
     *
     * @return  Application
     * @since   1.0
     */
    public static function getApplication(Input $input = null)
    {
        if (self::$application) {
        } else {
            self::$application =
                MolajoApplication::getInstance(
                    $input
                );
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
     * @return Request|null
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
     * @return  Request|null
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
     * getRender
     *
     * MolajoRender, alias Molajo::Renderer
     *
     * @static
     * @param   string $override_request_url
     * @param   string $override_asset_id
     *
     * @return  Render|null
     * @since   1.0
     */
    public static function getRenderer()
    {
        if (self::$renderer) {
        } else {
            self::$renderer =
                MolajoRenderer::getInstance();
        }
        return self::$renderer;
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
     * getUser
     *
     * MolajoUser, alias Molajo::User
     *
     * @static
     * @param   Registry|null $config
     *
     * @return  User|null
     * @since   1.0
     */
    public static function getUser($id = null)
    {
        $id = 42;
        if (self::$user) {
        } else {
            self::$user =
                MolajoUser::getInstance(
                    $id
                );
        }
        return self::$user;
    }
}

