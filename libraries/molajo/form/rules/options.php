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
class MolajoFormRuleOptions extends MolajoFormRule
{
	public function test(& $element, $value, $group = null, & $input = null, & $form = null)
	{
		// Check each value and return true if we get a match
		foreach ($element->option as $option) {
			if ($value == $option->getAttribute('value')) {
				return true;
			}
		}
		return false;
	}
}
