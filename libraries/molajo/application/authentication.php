<?php
/**
 * @package     Molajo
 * @subpackage  Authentication
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Authorisation response class, provides an object for storing user and error details
 *
 * @package     Molajo
 * @subpackage  User
 * @since       1.0
 */
class MolajoAuthentication extends JObject
{
	/**
	 * Response status (see status codes)
	 *
	 * @var type string
	 */
	public $status		= MOLAJO_AUTHENTICATE_STATUS_FAILURE;

	/**
	 * The type of authentication that was successful
	 *
	 * @var type string
	 */
	public $type		= '';

	/**
	 *  The error message
	 *
	 * @var error_message string
	 */
	public $error_message	= '';

	/**
	 * Any UTF-8 string that the End User wants to use as a username.
	 *
	 * @var fullname string
	 */
	public $username		= '';

	/**
	 * Any UTF-8 string that the End User wants to use as a password.
	 *
	 * @var password string
	 */
	public $password		= '';

	/**
	 * The email address of the End User as specified in section 3.4.1 of [RFC2822]
	 *
	 * @var email string
	 */
	public $email			= '';

	/**
	 * UTF-8 string free text representation of the End User's full name.
	 *
	 * @var fullname string
	 *
	 */
	public $fullname		= '';

	/**
	 * The End User's date of birth as YYYY-MM-DD. Any values whose representation uses
	 * fewer than the specified number of digits should be zero-padded. The length of this
	 * value MUST always be 10. If the End User user does not want to reveal any particular
	 * component of this value, it MUST be set to zero.
	 *
	 * For instance, if a End User wants to specify that his date of birth is in 1980, but
	 * not the month or day, the value returned SHALL be "1980-00-00".
	 *
	 * @var fullname string
	 */
	public $birthdate		= '';

	/**
	 * The End User's gender, "M" for male, "F" for female.
	 *
	 * @var gender string
	 *
	 */
	public $gender		= '';

	/**
	 * UTF-8 string free text that SHOULD conform to the End User's country's postal system.
	 *
	 * @var postcode string
	 */
	public $postcode		= '';

	/**
	 * The End User's country of residence as specified by ISO3166.
	 *
	 * @var country string
	 */
	public $country		= '';

	/**
	 * End User's preferred language as specified by ISO639.
	 *
	 * @var language string
	 */
	public $language		= '';

	/**
	 * ASCII string from TimeZone database
	 *
	 * @var timezone string
	 */
	public $timezone		= '';

	/**
	 * Constructor
	 *
	 * @param   string  $name  The type of the response
	 * @since   11.1
	 */
	function __construct() {}
}