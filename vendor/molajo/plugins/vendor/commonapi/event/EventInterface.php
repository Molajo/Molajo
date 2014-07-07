<?php
/**
 * Event Interface
 *
 * @package    Event
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace CommonApi\Event;

/**
 * Event Interface
 *
 * @package    Event
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
interface EventInterface
{
    /**
     * Get a property
     *
     * @param    $key
     *
     * @return   mixed
     * @since    0.1
     * @throws   \CommonApi\Exception\InvalidArgumentException
     */
    public function get($key);

    /**
     * Set a property value
     *
     * @param    string $key
     * @param    mixed  $value
     *
     * @return   $this
     * @since    0.1
     * @throws   \CommonApi\Exception\InvalidArgumentException
     */
    public function set($key, $value);
}
