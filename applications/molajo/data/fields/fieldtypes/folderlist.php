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
 * Supports an HTML select list of folder
 *
 * @package    Molajo
 * @subpackage  Form
 * @since       1.0
 */
class MolajoFormFieldFolderList extends MolajoFormFieldList
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  1.0
     */
    public $type = 'FolderList';

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

        // Initialize some field attributes.
        $filter = (string)$this->element['filter'];
        $exclude = (string)$this->element['exclude'];
        $hideNone = (string)$this->element['hide_none'];
        $hideDefault = (string)$this->element['hide_default'];

        // Get the path in which to search for file options.
        $path = (string)$this->element['directory'];
        if (!is_dir($path)) {
            $path = MOLAJO_BASE_FOLDER . '/' . $path;
        }

        // Prepend some default options based on field attributes.
        if (!$hideNone) {
            $options[] = MolajoHTML::_('select.option', '-1', MolajoTextHelper::alt('JOPTION_DO_NOT_USE', preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->name)));
        }
        if (!$hideDefault) {
            $options[] = MolajoHTML::_('select.option', '', MolajoTextHelper::alt('JOPTION_USE_DEFAULT', preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->name)));
        }

        // Get a list of folders in the search path with the given filter.
        $folders = JFolder::folders($path, $filter);

        // Build the options list from the list of folders.
        if (is_array($folders)) {
            foreach ($folders as $folder) {

                // Check to see if the file is in the exclude mask.
                if ($exclude) {
                    if (preg_match(chr(1) . $exclude . chr(1), $folder)) {
                        continue;
                    }
                }

                $options[] = MolajoHTML::_('select.option', $folder, $folder);
            }
        }

        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }
}
