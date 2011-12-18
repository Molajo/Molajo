<?php
/**
 * @package     Molajo
 * @subpackage  Form
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
/**
 * Sample data Form Field class.
 *
 * @package        Joomla.Installation
 * @since        1.6
 */
class MolajoFormFieldSample extends MolajoFormFieldList
{
    /**
     * The form field type.
     *
     * @var        string
     * @since    1.6
     */
    protected $type = 'Sample';

    /**
     * Method to get the field options.
     *
     * @return    array    The field option objects.
     * @since    1.6
     */
    protected function getOptions()
    {
        // Initialize variables.
        $lang = MolajoFactory::getLanguage();
        $options = array();
        $type = $this->form instanceof MolajoForm ? $this->form->getValue('db_type') : 'mysql';
        if ($type == 'mysqli') {
            $type = 'mysql';
        }
        // Get a list of files in the search path with the given filter.
        $files = JFolder::files(MOLAJO_SITE_INSTALLATION . '/sql/' . $type, '^sample.*\.sql$');

        // Build the options list from the list of files.
        if (is_array($files)) {
            foreach ($files as $file)
            {
                $options[] = MolajoHTML::_('select.option', $file, $lang->hasKey($key = 'INSTL_' . ($file = JFile::stripExt($file)) . '_SET')
                                                                  ? MolajoTextHelper::_($key) : $file);
            }
        }

        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }

    /**
     * Method to get the field calendar markup.
     *
     * @return    string    The field calendar markup.
     * @since    1.6
     */
    protected function getInput()
    {
        if (!$this->value) {
            $conf = MolajoFactory::getApplication()->getConfig();
            if ($conf->get('sampledata')) {
                $this->value = $conf->get('sampledata');
            } else {
                $this->value = 'sample_data.sql';
            }
        }
        return parent::getInput();
    }
}
