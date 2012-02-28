<?php
/**
 * @package     Molajo
 * @subpackage  Services
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

use Symfony\Component\HttpFoundation\Cookie;

/**
 * Cookie
 *
 * Symfony\Component\HttpFoundation\Cookie
 * http://api.symfony.com/2.0/Symfony/Component/HttpFoundation/Cookie.html
 *
 * @package     Molajo
 * @subpackage  Services
 * @since       1.0
 */
class MolajoCookieService extends Cookie
{
    /**
     * Static instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

    /**
     * $cookie
     *
     * @var    object
     * @since  1.0
     */
    public $cookie;

    /**
     * getInstance
     *
     * @static
     * @return bool|object
     * @since  1.0
     */
    public static function getInstance($name, $value = null, $expire = 0, $path = '/', $domain = null, $secure = false, $httpOnly = true)
    {
        if (empty(self::$instance)) {
            self::$instance = new MolajoCookieService($name, $value, $expire, $path, $domain, $secure, $httpOnly);
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
    public function __construct($name, $value = null, $expire = 0, $path = '/', $domain = null, $secure = false, $httpOnly = true)
    {
    }
}
