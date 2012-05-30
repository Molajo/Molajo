<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Trigger\Logout;

use Molajo\Extension\Trigger\Trigger\Trigger;

defined('MOLAJO') or die;

/**
 * Logout
 *
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class LogoutTrigger extends Trigger
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
            self::$instance = new LogoutTrigger();
        }

        return self::$instance;
    }

    /**
     * Before Authenticating the Logout Process
     *
     * @return boolean
     * @since   1.0
     */
    public function onBeforeLogout()
    {
        return false;
    }

    /**
     * After Authenticating the Logout Process
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterLogout()
    {
        return false;
    }
}
