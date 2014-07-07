<?php
/**
 * Authentication Interface
 *
 * @package    User
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace CommonApi\User;

/**
 * Authentication Interface
 *
 * @package    User
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
interface AuthenticationInterface
{
    /**
     * Guest - verify the Session
     *
     * @param   string $session_id
     *
     * @return  int   $id
     * @since   1.0
     */
    public function isGuest($session_id);

    /**
     * Login - verify username and password, handle remember request if value is true
     *
     * @param   string $session_id
     * @param   string $username
     * @param   string $password
     * @param   bool   $remember
     *
     * @return  int     $id
     * @since   1.0
     */
    public function login($session_id, $username, $password, $remember = false);

    /**
     * Verify if the User is Logged On
     *
     * @param   string $session_id
     * @param   string $username
     *
     * @return  int
     * @since   1.0
     */
    public function isLoggedOn($session_id, $username);

    /**
     * Change the password for a user
     *
     * @param   string $session_id
     * @param   string $username
     * @param   string $password
     * @param   string $reset_password_code
     * @param   bool   $remember
     *
     * @return  $this
     * @since   1.0
     */
    public function changePassword(
        $session_id,
        $username,
        $password = '',
        $reset_password_code = '',
        $remember = false
    );

    /**
     * Generate a token and email a temporary link to change password and sends to user
     *
     * @param   string $username
     * @param   string $session_id
     *
     * @return  $this
     * @since   1.0
     */
    public function requestPasswordReset($session_id, $username);

    /**
     * Log out and Redirect
     *
     * @param   string $username
     * @param   string $session_id
     *
     * @return  null
     * @since   1.0
     */
    public function logout($session_id, $username);
}
