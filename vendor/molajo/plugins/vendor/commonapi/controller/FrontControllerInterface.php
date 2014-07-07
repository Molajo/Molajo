<?php
/**
 * Front Controller Interface
 *
 * @package    Controller
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace CommonApi\Controller;

/**
 * Front Controller Interface
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
interface FrontControllerInterface
{
    /**
     * Request to Response Processing
     *
     * @return  $this
     * @since   1.0.0
     */
    public function process();

    /**
     * Schedule Event Processing
     *
     * @param   string $event_name
     * @param   array  $options
     *
     * @return  array
     * @since   1.0.0
     */
    public function scheduleEvent($event_name, array $options = array());

    /**
     * Shutdown the application
     *
     * @return  void
     * @since   1.0.0
     */
    public function shutdown();
}
