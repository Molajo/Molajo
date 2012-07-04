<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Service\Services\Cookie;

use Molajo\Service\Services;

use Symfony\Component\HttpFoundation\Cookie;

defined('MOLAJO') or die;

/**
 * Redirect
 *
 * http://api.symfony.com/2.0/Symfony/Component/HttpFoundation/Cookie.html
 *
 * @package   Molajo
 * @subpackage  Services
 * @since           1.0
 */
Class CookieService
{
    /**
     * Response instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

    /**
     * getInstance
     *
     * @static
     * @return object
     * @since  1.0
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new CookieService();
        }

        return self::$instance;
    }
}
