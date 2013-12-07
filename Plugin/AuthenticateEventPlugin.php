<?php
/**
 * Authenticate Event Plugin
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Plugin;

use CommonApi\Event\AuthenticateInterface;

/**
 * Authenticate Event Plugin
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @since      1.0
 */
abstract class AuthenticateEventPlugin extends AbstractPlugin implements AuthenticateInterface
{
    /**
     * Before logging in processing
     *
     * @return  $this
     * @since   1.0
     * @throws  \Exception\Plugin\AuthenticateEventException
     */
    public function onBeforeLogin()
    {
    }

    /**
     * After Logging in event
     *
     * @return  $this
     * @since   1.0
     * @throws  \Exception\Plugin\AuthenticateEventException
     */
    public function onAfterLogin()
    {
    }

    /**
     * Before logging out processing
     *
     * @return  $this
     * @since   1.0
     * @throws  \Exception\Plugin\AuthenticateEventException
     */
    public function onBeforeLogout()
    {
    }

    /**
     * After Logging out event
     *
     * @return  $this
     * @since   1.0
     * @throws  \Exception\Plugin\AuthenticateEventException
     */
    public function onAfterLogout()
    {
    }
}
