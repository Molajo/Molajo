<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Form Rule class for the prefix DB.
 *
 * @package		Joomla.Installation
 * @since		1.7
 */
class JFormRulePrefix extends MolajoFormRulePrefix
{
	/**
	 * The regular expression to use in testing a form field value.
	 *
	 * @var    string
	 * @since  1.7
	 */
	protected $regex = '^[a-z][a-z0-9]*_$';

	/**
	 * The regular expression modifiers to use when testing a form field value.
	 *
	 * @var    string
	 * @since  1.7
	 */
	protected $modifiers = 'i';
}
