<?php
/**
 * Authenticate Event Interface
 *
 * @package    Event
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace CommonApi\Event;

/**
 * Authenticate Event Interface
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
interface AuthenticateInterface
{
    /**
     * Before logging in processing
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeAuthenticate();

    /**
     * After Logging in event
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterAuthenticate();

    /**
     * Before logging out processing
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeLogout();

    /**
     * After Logging out event
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterLogout();
}
