<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Extension\Plugin\Logon;

use Molajo\Extension\Plugin\Plugin\Plugin;

defined('MOLAJO') or die;

/**
 * Logon
 *
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class LogonPlugin extends Plugin
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
