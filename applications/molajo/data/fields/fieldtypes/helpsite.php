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
 * @package    Molajo
 * @subpackage  Form
 * @since       1.0
 */
class MolajoFormFieldHelpsite extends MolajoFormFieldList
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  1.0
     */
    public $type = 'Helpsite';

    /**
     * Method to get the field options.
     *
     * @return  array  The field option objects.
     * @since   1.0
     */
    protected function getOptions()
    {
        // Get Joomla version.
        $version = new MolajoVersion();
        $jver = explode('.', $version->getShortVersion());

        // Merge any additional options in the XML definition.
        $options = array_merge(
            parent::getOptions(),
            MolajoApplicationHelper::createSiteList(MOLAJO_BASE_FOLDER.'/help/helpsites.xml', $this->value)
        );

        return $options;
    }
}