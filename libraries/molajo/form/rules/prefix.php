<?php
/**
 * @package     Molajo
 * @subpackage  Form
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die();

/**
 * Boolean Rule
 *
 * @package     Molajo
 * @subpackage  Form
 * @since       1.0
 */
class MolajoFormRulePrefix extends MolajoFormRule
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
