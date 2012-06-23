<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Trigger\Logon;

use Molajo\Extension\Trigger\Trigger\Trigger;

defined('MOLAJO') or die;

/**
 * Logon
 *
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class LogonTrigger extends Trigger
{
    /**
     * Before Authenticating the Logon Process
     *
     * @return boolean
     * @since   1.0
     */
    public function onBeforeLogon()
    {
        return false;
    }

    /**
     * After Authenticating the Logon Process
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterLogon()
    {
        return false;
    }
}
