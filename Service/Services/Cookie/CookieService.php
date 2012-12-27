<?php
/**
 * @package    Niambie
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    MIT
 */
namespace Molajo\Service\Services\Cookie;

use Molajo\Service\Services;

use Symfony\Component\HttpFoundation\Cookie;

defined('NIAMBIE') or die;

/**
 * Redirect
 *
 * http://api.symfony.com/2.0/Symfony/Resource/HttpFoundation/Cookie.html
 *
 * @package         Niambie
 * @subpackage      Services
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
