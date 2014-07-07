<?php
/**
 * User Data Interface
 *
 * @package    User
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace CommonApi\User;

/**
 * User Data Interface
 *
 * @package    User
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
interface UserDataInterface
{
    /**
     * Get user data using a value for id, username, email or initialize new user
     *
     * @param   null|string $value
     * @param   null|string $key
     *
     * @return  $this
     */
    public function load($value = null, $key = 'username');

    /**
     * Get User Data
     *
     * @return  object
     * @since   1.0
     */
    public function getUserData();

    /**
     * Insert User
     *
     * @param   array $data
     *
     * @return  object
     * @since   1.0
     */
    public function insertUserData(array $data = array());

    /**
     * Update User Data for loaded User
     *
     * @param   array $updates
     *
     * @return  object
     * @since   1.0
     */
    public function updateUserData(array $updates = array());

    /**
     * Delete User Data
     *
     * @return  $this
     * @since   1.0
     */
    public function deleteUserData();
}
