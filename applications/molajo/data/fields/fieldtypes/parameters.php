<?php
/**
 * @package     Molajo
 * @subpackage  Form
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen, Cristina Solano. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
/**
 * Form Field class for the Joomla Framework.
 *
 * @package        Joomla.Framework
 * @subpackage    Form
 * @since        1.6
 */
class MolajoFormFieldParameters extends MolajoFormField
{
    /**
     * The form field type.
     *
     * @var        string
     * @since    1.6
     */
    public $type = 'Parameters';

    /**
     * Method to get the field calendar markup.
     *
     * TODO: Add access check.
     *
     * @return    string    The field calendar markup.
     * @since    1.6
     */
    protected function getInput()
    {
        /** language **/
        MolajoFactory::getLanguage()->load('plg_system_create_molajosamples', MOLAJO_BASE_FOLDER.'/media/plg_system_create/components/', MolajoFactory::getLanguage()->getDefault(), true, true);

        /** $parameter_type **/
        $parameter_type = (string)$this->element['parameter_type'];
        $path = (string)$this->element['path'];

        return $this->getParameterSet($parameter_type, $path);
    }

    /**
     * Get a list of the parameter sets
     *
     * @return    array
     * @since    1.6
     */
    protected function getParameterSet($parameter_type, $path)
    {
        if ($path == '' || $path == null) {
            $path = MOLAJO_APPLICATIONS_DATA_XML;
        }

        JFile::makeSafe($parameter_type);
        JFilterOutput::stringURLSafe($parameter_type);

        return simplexml_load_file($parameter_type);

        return MolajoForm::loadFile($parameter_type, false);
    }
}

