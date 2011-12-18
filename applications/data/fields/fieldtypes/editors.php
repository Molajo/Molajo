<?php
/**
 * @package     Molajo
 * @subpackage  Form
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2012 Cristina Solano. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Form Field class for the Joomla Framework.
 *
 * @package    Molajo
 * @subpackage  Form
 * @since       1.0
 */
class MolajoFormFieldEditors extends MolajoFormFieldList
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  1.0
     */
    public $type = 'Editors';

    /**
     * Method to get the field options.
     *
     * @return  array  The field option objects.
     * @since   1.0
     */
    protected function getOptions()
    {
        // Get the database object and a new query object.
        $db = MolajoFactory::getDbo();
        $query = $db->getQuery(true);

        // Build the query.
        $query->select('element AS value, name AS text');
        $query->from('#__extensions');
        $query->where('folder = ' . $db->quote('editors'));
        $query->where('enabled = 1');
        $query->order('ordering, name');

        // Set the query and load the options.
        $db->setQuery($query);
        $options = $db->loadObjectList();
        $lang = MolajoFactory::getLanguage();
        foreach ($options as $i => $option) {
            $lang->load('plg_editors_' . $option->value, MOLAJO_BASE_FOLDER, null, false, false)
            || $lang->load('plg_editors_' . $option->value, MOLAJO_EXTENSIONS_PLUGINS . '/editors/' . $option->value, null, false, false)
            || $lang->load('plg_editors_' . $option->value, MOLAJO_BASE_FOLDER, $lang->getDefault(), false, false)
            || $lang->load('plg_editors_' . $option->value, MOLAJO_EXTENSIONS_PLUGINS . '/editors/' . $option->value, $lang->getDefault(), false, false);
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
