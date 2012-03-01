<?php
/**
 * @package     Molajo
 * @subpackage  Service
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application\Service;

defined('MOLAJO') or die;

/**
 * Parameter
 *
 * Symfony\Component\HttpFoundation\Parameter
 * http://api.symfony.com/2.0/Symfony/Component/HttpFoundation/Parameter.html
 *
 * @package     Molajo
 * @subpackage  Services
 * @since       1.0
 */
Class ParameterService
{
    /**
     * Static instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

    /**
     * $page_parameters
     *
     * Parameters specific to the extension for the URL request
     *
     * @var    object
     * @since  1.0
     */
    public $page_parameters;

    /**
     * $extension_parameters
     *
     * Parameters specific to the current extension
     *
     * @var    object
     * @since  1.0
     */
    public $extension_parameters;

    /**
     * $theme_parameters
     *
     * Parameters specific to the current theme
     *
     * @var    object
     * @since  1.0
     */
    public $theme_parameters;

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
            self::$instance = new ParameterService();
        }
        return self::$instance;
    }

    /**
     * __construct
     *
     * Class constructor.
     *
     * @since  1.0
     */
    public function __construct()
    {
        $this->request_parameters = new Registry();
        $this->theme_parameters = new Registry();
        $this->page_parameters = new Registry();
    }

    /**
     * get
     *
     * Returns a property of the Request Parameter object
     *
     * @param   string  $key
     * @param   mixed   $default
     *
     * @return  mixed
     * @since   1.0
     */
    public function get($key, $default = null)
    {
        return $this->request_parameters->get($key, $default);
    }

    /**
     * set
     *
     * Modifies a property of the Request Parameter object
     *
     * @param   string  $key
     * @param   mixed   $value
     *
     * @return  mixed
     * @since   1.0
     */
    public function set($key, $value = null)
    {
        return $this->request_parameters->set($key, $value);
    }

    /**
     * getPage
     *
     * Returns a property of the Page Parameter object
     *
     * @param   string  $key
     * @param   mixed   $default
     *
     * @return  mixed
     * @since   1.0
     */
    public function getPage($key, $default = null)
    {
        return $this->page_parameters->get($key, $default);
    }

    /**
     * setPage
     *
     * Modifies a property of the Page Parameter object
     *
     * @param   string  $key
     * @param   mixed   $value
     *
     * @return  mixed
     * @since   1.0
     */
    public function setPage($key, $value = null)
    {
        return $this->page_parameters->set($key, $value);
    }

    /**
     * getTheme
     *
     * Returns a property of the Theme Parameter object
     *
     * @param   string  $key
     * @param   mixed   $default
     *
     * @return  mixed
     * @since   1.0
     */
    public function getTheme($key, $default = null)
    {
        return $this->theme_parameters->get($key, $default);
    }

    /**
     * setTheme
     *
     * Modifies a property of the Theme Parameter object
     *
     * @param   string  $key
     * @param   mixed   $value
     *
     * @return  mixed
     * @since   1.0
     */
    public function setTheme($key, $value = null)
    {
        return $this->theme_parameters->set($key, $value);
    }
}
