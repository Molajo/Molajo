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
 * Access Level
 *
 * @package     Molajo
 * @subpackage  Form
 * @since       1.0
 */
class MolajoFormFieldAccesslevel extends MolajoFormFieldList
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  1.0
     */
    public $type = 'AccessLevel';

    /**
     * Method to get the field calendar markup.
     *
     * @return  string   The field calendar markup.
     * @since   1.0
     */
    protected function getInput()
    {
        // Initialize variables.
        $attr = '';

        // Initialize some field attributes.
        $attr .= $this->element['class'] ? ' class="'.(string)$this->element['class'].'"' : '';
        $attr .= ((string)$this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
        $attr .= $this->element['size'] ? ' size="'.(int)$this->element['size'].'"' : '';
        $attr .= $this->multiple ? ' multiple="multiple"' : '';

        // Initialize JavaScript field attributes.
        $attr .= $this->element['onchange'] ? ' onchange="'.(string)$this->element['onchange'].'"' : '';

        // Get the field options.
        $options = $this->getOptions();

        return MolajoHTML::_('access.level', $this->name, $this->value, $attr, $options, $this->id);
    }
}
