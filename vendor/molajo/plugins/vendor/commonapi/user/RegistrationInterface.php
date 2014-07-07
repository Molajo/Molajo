<?php
/**
 * Registration Interface
 *
 * @package    User
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace CommonApi\User;

/**
 * Registration Interface
 *
 * @package    User
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
interface RegistrationInterface
{
    /**
     * Register User and email activation code
     *
     * @param   array $options
     *
     * @return  $this
     * @since   1.0
     */
    public function register($options);

    /**
     * Determine if this user is registered
     *
     * @return  boolean
     * @since   1.0
     */
    public function isRegistered();

    /**
     * Activate Registration using Activation Code
     *
     * @param   array $options
     *
     * @return  $this
     * @since   1.0
     */
    public function activateRegistration($options);

    /**
     * Determine if this user registration has been activated
     *
     * @return  boolean
     * @since   1.0
     */
    public function isActivated();

    /**
     * Deactivate Registration
     *
     * @param   array $options
     *
     * @return  $this
     * @since   1.0
     */
    public function deactivateRegistration($options = array());

    /**
     * Suspend User
     *
     * @param   array $options
     *
     * @return  $this
     * @since   1.0
     */
    public function suspendUser($options);

    /**
     * Determine if this user was suspended
     *
     * @return  $this
     * @since   1.0
     */
    public function isSuspended();

    /**
     * Remove Suspension on this User
     *
     * @param   array $options
     *
     * @return  $this
     * @since   1.0
     */
    public function removeSuspension($options);
}
