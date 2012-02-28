<?php
/**
 * @package     Molajo
 * @subpackage  Services
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

use Symfony\Component\HttpFoundation\Session;

/**
 * Session
 *
 * Symfony\Component\HttpFoundation\Session
 * http://api.symfony.com/2.0/Symfony/Component/HttpFoundation/Session.html
 *
 * @package     Molajo
 * @subpackage  Services
 * @since       1.0
 */
class MolajoSessionService extends Session
{
    /**
     * Static instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

    /**
     * $session
     *
     * @var    object
     * @since  1.0
     */
    public $session;

    /**
     * getInstance
     *
     * @static
     * @return bool|object
     * @since  1.0
     */
    public static function getInstance($storage, $attributes = null, $flashes = null)
    {
        if (empty(self::$instance)) {
            self::$instance = new MolajoSessionService($storage, $attributes = null, $flashes = null);
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
    public function __construct($storage, $attributes = null, $flashes = null)
    {
    }
}
