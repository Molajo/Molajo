<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
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
