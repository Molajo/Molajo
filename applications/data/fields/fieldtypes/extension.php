<?php
/**
 * @package     Molajo
 * @subpackage  Form
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2012 Cristina Solano. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Supports an HTML select list of extensions
 *
 * @package        Joomla.Framework
 * @subpackage    Form
 * @since        1.6
 */
class MolajoFormFieldExtension extends MolajoFormFieldList
{
    /**
     * The form field type.
     *
     * @var        string
     * @since    1.6
     */
    public $type = 'Extension';

    /**
     * Method to get the field options.
     *
     * @return    array    The field option objects.
     * @since    1.6
     */
    protected function getOptions()
    {
        // Initialize variables.
        $session = MolajoController::getSession();
        $options = array();

        // Extension Type
        $extensiontype = '';
        if (isset($this->element['extensiontype'])) {
            $type = trim((string)$this->element['extensiontype']);
        }
        if ($extensiontype == '') {
            $extensiontype = 'component';
        }
        // Comma-delimited list of extensions to exclude
        if (isset($this->element['exclude'])) {
            $exclude = trim((string)$this->element['exclude']);
        } else {
            $exclude = '';
        }

        // Get the database object and a new query object.
        $db = MolajoController::getDbo();
        $query = $db->getQuery(true);

        // Build the query.
        $query->select('element AS value, name AS text');
        $query->from('#__extensions');
        $query->where('enabled = 1');
        $query->where('type = "' . $extensiontype . '"');
        if (trim($exclude) == '') {
        } else {
            $query->where('extension_id NOT IN ("' . $exclude . '")');
        }
        $query->order('ordering, name');

        // Set the query and load the options.
        $db->setQuery($query);
        $options = $db->loadObjectList();

        // Set the query and load the options.
        $lang = MolajoController::getLanguage();
        foreach ($options as $i => $option) {
            $lang->load($option->value, MOLAJO_BASE_FOLDER, null, false, false);
            $options[$i]->text = MolajoTextHelper::_($option->text);
        }

        // Check for a database error.
        if ($db->getErrorNum()) {
            MolajoError::raiseWarning(500, $db->getErrorMsg());
        }

        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }
}