<?php
/**
 * @package    Molajo
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    MIT
 */
namespace Molajo\Plugin\Logout;

use Molajo\Plugin\Plugin\Plugin;

defined('MOLAJO') or die;

/**
 * Logout
 *
 * @package     Molajo
 * @license     MIT
 * @since       1.0
 */
class LogoutPlugin extends Plugin
{

    /**
     * Before Authenticating the Logout Process
     *
     * @return  boolean
     * @since   1.0
     */
    public function onBeforeLogout()
    {
        return false;
    }

    /**
     * After Authenticating the Logout Process
     *
     * @return  boolean
     * @since   1.0
     */
    public function onAfterLogout()
    {
        return false;
    }
}
