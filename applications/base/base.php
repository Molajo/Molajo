<?php
/**
 * @package     Molajo
 * @subpackage  Base
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Molajo Factory
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
     * Alias Molajo::Responder
     *
     * @var    object MolajoResponder
     * @since  1.0
     */
    public static $responder = null;

    /**
     * @var    object JDatabase
     * @since  1.0
     */
    public static $db = null;

    /**
     * @var    Dates
     * @since  1.0
     */
    public static $dates = array();

    /**
     * getSite
     *
     * MolajoSite, alias Molajo::Site
     *
     * @static
     * @param   null   $id
     * @param   array  $config
     * @param   string $prefix
     *
     * @return  null|Site
     * @since   1.0
     */
    public static function getSite($id = null,
                                   $config = null)
    {
        if (self::$site) {
        } else {
            self::$site = MolajoSite::getInstance(
                $id,
                $config
            );
        }
        return self::$site;
    }

    /**
     * getApplication
     *
     * MolajoApplication, alias Molajo::Application
     *
     * @static
     * @param   null   $id
     * @param   array  $config
     * @param   string $prefix
     *
     * @return  null|Site
     * @since   1.0
     */
    public static function getApplication($id = null,
                                          $config = null,
                                          $input = null)
    {
        if (self::$application) {
        } else {
            self::$application =
                MolajoApplication::getInstance(
                    $id,
                    $config,
                    $input
                );
        }
        return self::$application;
    }

    /**
     * getRequest
     *
     * Get the Request Controller Object
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
     * Get the Request Controller Object
     *
     * @static
     * @param Registry|null $config
     * @param string $override_request_url
     * @param string $override_asset_id
     *
     * @return Request|null
     * @since 1.0
     */
    public static function getParser($config = null)
    {
        if (self::$parser) {
        } else {
            self::$parser =
                MolajoParser::getInstance(
                    $config
                );
        }
        return self::$parser;
    }

    /**
     * getResponder
     *
     * Get the Responder Controller Object
     *
     * @static
     * @param Registry|null $config
     *
     * @return Responder|null
     * @since 1.0
     */
    public static function getResponder($config = null)
    {
        if (self::$responder) {
        } else {
            self::$responder =
                MolajoResponder::getInstance(
                    $config
                );
        }
        return self::$responder;
    }

    /**
     * Get an user object.
     *
     * Returns the global User object, only creating it if it doesn't already exist.
     *
     * @static
     * @param null $id
     * @return object|User
     */
    public static function getUser($id = null)
    {
        $id = 42;
        //        if (is_null($id)) {
        //            $instance = self::getSession()->get('user');
        //            if ($instance instanceof MolajoUser) {
        //            } else {
        //                $instance = MolajoUser::getInstance();
        //            }
        //        } else {
        //            $current = self::getSession()->get('user');
        //            var_dump($current);
        //            if ($current->id = $idxxxxxx) {
        //                $instance = self::getSession()->get('user');
        //            } else {
        $instance = MolajoUser::getInstance($id);
        //            }
        //        }
        //        echo '<pre>';var_dump($instance);'</pre>';
        return $instance;
    }

    /**
     * getDbo
     *
     * Get a database object
     *
     * @return Database object
     * @since 1.0
     */
    public static function getDbo()
    {
        if (self::$db) {
        } else {
            self::$db = ConfigurationHelper::getDB();
        }
        return self::$db;
    }

    /**
     * getDate
     *
     * Return the Date object
     *
     * @param   mixed  $time     The initial time for the JDate object
     * @param   mixed  $tzOffset The timezone offset.
     *
     * @return JDate object
     * @since   1.0
     */
    public static function getDate($time = 'now', $tzOffset = null)
    {
        static $instances;
        static $classname;
        static $mainLocale;

        if (!isset($instances)) {
            $instances = array();
        }

        $language = self::getApplication()->get('languageObject');
        $locale = $language->getTag();

        if (!isset($classname) || $locale != $mainLocale) {
            $mainLocale = $locale;

            if ($mainLocale !== false) {
                $classname = str_replace('-', '_', $mainLocale) . 'Date';

                if (class_exists($classname)) {
                } else {
                    $classname = 'JDate';
                }
            } else {
                $classname = 'JDate';
            }
        }
        $key = $time . '-' . $tzOffset;

        $tmp = new $classname($time, $tzOffset);
        return $tmp;
    }
}

