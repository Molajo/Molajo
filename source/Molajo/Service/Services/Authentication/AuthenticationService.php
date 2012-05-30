<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Service\Services\Authentication;

use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Authentication
 *
 * @package     Molajo
 * @subpackage  Services
 * @since       1.0
 */
Class AuthenticationService
{
    /**
     * Static instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

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
            self::$instance = new AuthenticationService();
        }

        return self::$instance;
    }

    /**
     * Class constructor.
     *
     * @since  1.0
     */
    public function __construct()
    {

    }
}
