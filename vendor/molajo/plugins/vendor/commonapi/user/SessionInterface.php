<?php
/**
 * Session Interface
 *
 * @package    User
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace CommonApi\User;

/**
 * Session Interface
 *
 * @package    User
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
interface SessionInterface
{
    /**
     * Gets the value for a key
     *
     * @param   string $key
     *
     * @return  mixed
     * @since   1.0
     */
    public function getSession($key);

    /**
     * Sets the value for key
     *
     * @param   string $key
     * @param   mixed  $value
     *
     * @return  mixed
     * @since   1.0
     */
    public function setSession($key, $value);

    /**
     * Delete a single or all session keys
     *
     * @param   null|string $key
     *
     * @return  mixed
     * @since   1.0
     */
    public function deleteSession($key);
}
