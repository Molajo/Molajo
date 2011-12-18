<?php
/**
 * @package     Molajo
 * @subpackage  Form
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2012 Amy Stephen, Cristina Solano. All rights reserved.
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
class MolajoFormFieldTag extends MolajoFormFieldList
{
    /**
     * The form field type.
     *
     * @var        string
     * @since    1.6
     */
    public $tagtype = 'Tag';

    /**
     * Method to get the field options.
     *
     * @return    array    The field option objects.
     * @since    1.6
     */
    protected function getOptions()
    {
        // Initialize variables.
        $options = array();
        $db = MolajoFactory::getDbo();
        $query = $db->getQuery(true);

        // Build the query.
        $query->select('id AS value, title AS text');
        $query->from('#__tags');
        $query->where('state = 1');

        $tagtype = 0;
        if (isset($this->element['tagtype'])) {
            $tagtype = (int)$this->element['tagtype'];
        }
        if ((int)$tagtype == 0) {
        } else {
            $query->where('tag_type = ' . (int)$tagtype);
        }
        $query->order('ordering, title');

        // Set the query and load the options.
        $db->setQuery($query);
        $options = $db->loadObjectList();

        // Set the query and load the options.
        $lang = MolajoFactory::getLanguage();
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