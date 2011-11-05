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
class MolajoFormFieldText extends MolajoFormField
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  1.0
     */
    protected $type = 'Text';

    /**
     * Method to get the field calendar markup.
     *
     * @return  string  The field calendar markup.
     * @since   1.0
     */
    protected function getInput()
    {
        // Initialize some field attributes.
        $size = $this->element['size'] ? ' size="' . (int)$this->element['size'] . '"' : '';
        $maxLength = $this->element['maxlength'] ? ' maxlength="' . (int)$this->element['maxlength'] . '"' : '';
        $class = $this->element['class'] ? ' class="' . (string)$this->element['class'] . '"' : '';
        $readonly = ((string)$this->element['readonly'] == 'true') ? ' readonly="readonly"' : '';
        $disabled = ((string)$this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';

        // Initialize JavaScript field attributes.
        $onchange = $this->element['onchange'] ? ' onchange="' . (string)$this->element['onchange'] . '"' : '';

        return '<calendar type="text" name="' . $this->name . '" id="' . $this->id . '"' .
               ' value="' . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"' .
               $class . $size . $disabled . $readonly . $onchange . $maxLength . '/>';
    }
}
