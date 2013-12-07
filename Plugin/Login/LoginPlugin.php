<?php
/**
 * Login Plugin
 *
 * @package    Molajo
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugin\Login;

use Molajo\Plugin\AuthenticateEventPlugin;
use CommonApi\Event\AuthenticateInterface;
use Exception\Plugin\AuthenticateEventException;

/**
 * Login Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
class LoginPlugin extends AuthenticateEventPlugin implements AuthenticateInterface
{
    /**
     * Before Authenticating the Login Process
     *
     * @return  $this
     * @since   1.0
     * @throws  AuthenticateEventException
     */
    public function onBeforeLogin()
    {
        return $this;
    }

    /**
     * After Authenticating the Login Process
     *
     * @return  $this
     * @since   1.0
     * @throws  AuthenticateEventException
     */
    public function onAfterLogin()
    {
        return $this;
    }
}
