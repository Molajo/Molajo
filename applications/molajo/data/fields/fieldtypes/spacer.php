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
 * Form Field class for the Joomla Framework.
 *
 * @package        Joomla.Framework
 * @subpackage    Form
 * @since        1.6
 */
class MolajoFormFieldSpacer extends MolajoFormField
{
    /**
     * The form field type.
     *
     * @var        string
     * @since    1.6
     */
    protected $type = 'Spacer';

    /**
     * Method to get the field calendar markup.
     *
     * @return    string    The field calendar markup.
     * @since    1.6
     */
    protected function getInput()
    {
        return ' ';
    }

    /**
     * Method to get the field label markup.
     *
     * @return    string    The field label markup.
     * @since    1.6
     */
    protected function getLabel()
    {
        $results = '<div class="clr"></div>';

        if (isset($this->element['hr']) && ($this->element['hr'] == "true")) {
            $results .= '<hr />';
        }
        if (isset($this->element['label'])) {
            $results .= '<br /><strong>' . MolajoTextHelper::_($this->element['label']) . '</strong><br />';
        }
        if (isset($this->element['description'])) {
            $results .= '<br />' . MolajoTextHelper::_($this->element['description']) . '<br /><br />';
        }
        $results .= '<div class="clr"></div>';

        return $results;
    }
}