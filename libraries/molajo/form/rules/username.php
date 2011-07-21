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
class MolajoFormRuleUsername extends MolajoFormRule
{
	/**
	 * Method to test the username for uniqueness.
	 *
	 * @param   object  $element	The JXMLElement object representing the <field /> tag for the
	 * 								form field object.
	 * @param   mixed   $value		The form field value to validate.
	 * @param   string  $group		The field name group control value. This acts as as an array
	 * 								container for the field. For example if the field has name="foo"
	 * 								and the group value is set to "bar" then the full field name
	 * 								would end up being "bar[foo]".
	 * @param   object  $calendar		An optional JRegistry object with the entire data set to validate
	 * 								against the entire form.
	 * @param   object  $form		The form object for which the field is being tested.
	 *
	 * @return  boolean  True if the value is valid, false otherwise.
	 *
	 * @since   11.1
	 * @throws    JException on invalid rule.
	 */
	public function test(& $element, $value, $group = null, & $calendar = null, & $form = null)
	{
		// Get the database object and a new query object.
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);

		// Build the query.
		$query->select('COUNT(*)');
		$query->from('#__users');
		$query->where('username = '.$db->quote($value));

		// Get the extra field check attribute.
		$userId = ($form instanceof MolajoForm) ? $form->getValue('id') : '';
		$query->where($db->quoteName('id').' <> '.(int) $userId);

		// Set and query the database.
		$db->setQuery($query);
		$duplicate = (bool) $db->loadResult();

		// Check for a database error.
		if ($db->getErrorNum()) {
			JError::raiseWarning(500, $db->getErrorMsg());
		}

		if ($duplicate) {
			return false;
		}

		return true;
	}
}