<?php
/**
 * Logout Plugin
 *
 * @package    Molajo
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugin\Logout;

use CommonApi\Event\AuthenticateInterface;
use Molajo\Plugin\AuthenticateEventPlugin;
use Exception\Plugin\AuthenticateEventException;

/**
 * Logout Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
class LogoutPlugin extends AuthenticateEventPlugin implements AuthenticateInterface
{
    /**
     * Before Authenticating the Logout Process
     *
     * @return  $this
     * @since   1.0
     * @throws  AuthenticateEventException
     */
    public function onBeforeLogout()
    {
        return $this;
    }

    /**
     * After Authenticating the Logout Process
     *
     * @return  $this
     * @since   1.0
     * @throws  AuthenticateEventException
     */
    public function onAfterLogout()
    {
        return $this;
    }
}
