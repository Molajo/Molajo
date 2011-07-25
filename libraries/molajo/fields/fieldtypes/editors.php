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
 * @since       11.1
 */
class MolajoFormFieldEditors extends MolajoFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $type = 'Editors';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 * @since   11.1
	 */
	protected function getOptions()
	{
		// Get the database object and a new query object.
		$db		= MolajoFactory::getDBO();
		$query	= $db->getQuery(true);

		// Build the query.
		$query->select('element AS value, name AS text');
		$query->from('#__extensions');
		$query->where('folder = '.$db->quote('editors'));
		$query->where('enabled = 1');
		$query->order('ordering, name');

		// Set the query and load the options.
		$db->setQuery($query);
		$options = $db->loadObjectList();
		$lang = MolajoFactory::getLanguage();
		foreach ($options as $i=>$option) {
				$lang->load('plg_editors_'.$option->value, MOLAJO_PATH_ADMINISTRATOR, null, false, false)
			||	$lang->load('plg_editors_'.$option->value, MOLAJO_PATH_PLUGINS .'/editors/'.$option->value, null, false, false)
			||	$lang->load('plg_editors_'.$option->value, MOLAJO_PATH_ADMINISTRATOR, $lang->getDefault(), false, false)
			||	$lang->load('plg_editors_'.$option->value, MOLAJO_PATH_PLUGINS .'/editors/'.$option->value, $lang->getDefault(), false, false);
			$options[$i]->text = JText::_($option->text);
		}

		// Check for a database error.
		if ($db->getErrorNum()) {
			JError::raiseWarning(500, $db->getErrorMsg());
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
