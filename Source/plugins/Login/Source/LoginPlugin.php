<?php
/**
 * Login Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Login;

use Molajo\Plugins\UserEvent;
use CommonApi\Event\UserEventInterface;

/**
 * Login Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
final class LoginPlugin extends UserEvent implements UserEventInterface
{
    /**
     * Before Authenticating the Login Process
     *
     * @return  $this
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function onBeforeAuthenticate()
    {
        return $this;
    }

    /**
     * After Authenticating the Login Process
     *
     * @return  $this
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function onAfterAuthenticate()
    {
        return $this;
    }
}
