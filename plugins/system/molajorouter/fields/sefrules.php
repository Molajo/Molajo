<?php
/**
 * @version		$Id: checkboxes.php 18279 2010-07-28 18:39:16Z ian $
 * @package		Joomla.Framework
 * @subpackage	Form
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
JFormHelper::loadFieldClass('checkboxes');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package		Joomla.Framework
 * @subpackage	Form
 * @since		1.6
 */
class JFormFieldSEFRules extends JFormFieldCheckboxes
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'SEF Rules';

	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 * @since	1.6
	 */
	protected function getOptions()
	{
		// Initialize variables.
		$options = array();

		$rules = array();
		$app = JFactory::getApplication();
		$event = $app->triggerEvent('onRouterRules');
		foreach($event as $ruleset) {
			$rules = array_merge($rules, (array) $ruleset);
		}
		foreach($rules as $rule) {
			$options[] = JHtml::_('select.option', $rule, 'COM_CONFIG_FIELD_SEF_RULES_'.strtoupper($rule).'_LABEL', 'value', 'text');
		}

		reset($options);

		return $options;
	}
}
