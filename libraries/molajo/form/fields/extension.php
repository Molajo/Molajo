<?php
/**
 * @package     Molajo
 * @subpackage  Form
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Cristina Solano. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die();

/**
 * Supports an HTML select list of extensions
 *
 * @package		Joomla.Framework
 * @subpackage	Form
 * @since		1.6
 */
class MolajoFormFieldExtension extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	public $type = 'Extension';

	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 * @since	1.6
	 */
	protected function getOptions()
	{
		// Initialize variables.
		$session = JFactory::getSession();
		$options = array();

		// Extension Type
                $extensiontype = '';
                if (isset($this->element['extensiontype'])) {
                    $type = trim((string) $this->element['extensiontype']);
                }
                if ($extensiontype == '') {
                    $extensiontype = 'component';
                }
		// Comma-delimited list of extensions to exclude
                if (isset($this->element['notextension'])) {
                    $notextension = trim((string) $this->element['notextension']);
                } else {
                    $notextension = '';
                }

                // Get the database object and a new query object.
		$db	= JFactory::getDBO();
		$query	= $db->getQuery(true);

		// Build the query.
		$query->select('element AS value, name AS text');
		$query->from('#__extensions');
		$query->where('enabled = 1');
                $query->where('type = "'.$extensiontype.'"');
                if (trim($notextension) == '') {
                } else {
                    $query->where('extension_id NOT IN ("'.$notextension.'")');
                }
		$query->order('ordering, name');

		// Set the query and load the options.
		$db->setQuery($query);
		$options = $db->loadObjectList();

		// Set the query and load the options.
		$lang = JFactory::getLanguage();
		foreach ($options as $i=>$option) {
                    $lang->load($option->value, JPATH_ADMINISTRATOR, null, false, false);
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