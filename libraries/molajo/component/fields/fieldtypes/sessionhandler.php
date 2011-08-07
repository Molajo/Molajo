<?php
/**
 * @package     Molajo
 * @subpackage  Form
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Cristina Solano. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
/**
 * Form Field class for the Joomla Framework.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       1.0
 */
class MolajoFormFieldSessionHandler extends MolajoFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $type = 'SessionHandler';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 * @since   1.0
	 */
	protected function getOptions()
	{
		// Initialize variables.
		$options = array();

		// Get the options from JSession.
		foreach (JSession::getStores() as $store) {
			$options[] = JHtml::_('select.option', $store, JText::_('MOLAJO_FORM_VALUE_SESSION_'.$store), 'value', 'text');
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}