<?php
/**
 * Logout Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Logout;

use CommonApi\Event\UserEventInterface;
use Molajo\Plugins\UserEvent;
use CommonApi\Exception\RuntimeException;

/**
 * Logout Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
final class LogoutPlugin extends UserEvent implements UserEventInterface
{
    /**
     * Before Authenticating the Logout Process
     *
     * @return  $this
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function onBeforeLogout()
    {
        return $this;
    }

    /**
     * After Authenticating the Logout Process
     *
     * @return  $this
     * @since   1.0.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function onAfterLogout()
    {
        return $this;
    }
}
