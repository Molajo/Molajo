<?php
/**
 * @package    Molajo
 * @subpackage  User
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('MOLAJO') or die;

/**
 * Authenthication class, provides an interface for the Joomla authentication system
 *
 * @package    Molajo
 * @subpackage  User
 * @since       1.0
 */
class JAuthentication extends MolajoAuthentication {}

/**
 * Authorisation response class, provides an object for storing user and error details
 *
 * @package    Molajo
 * @subpackage  User
 * @since       1.0
 */
class JAuthenticationResponse extends MolajoAuthenticationResponse {}