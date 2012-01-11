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
 * Form Field class for the Joomla Framework.
 *
 * @package        Joomla.Framework
 * @subpackage    Form
 * @since        1.6
 */
class MolajoFormFieldColorpicker extends MolajoFormField
{
    /**
     * The form field type.
     *
     * @var        string
     * @since    1.6
     */
    public $type = 'colorpicker';

    /**
     * Method to get the field calendar markup.
     *
     * @return    string    The field calendar markup.
     * @since    1.6
     */
    protected function getInput()
    {

        $size = $this->element['size'] ? ' size="' . (int)$this->element['size'] . '"' : '';
        $maxLength = $this->element['maxlength'] ? ' maxlength="' . (int)$this->element['maxlength'] . '"' : '';

        $class = 'class="color {pickerPosition:' . "'right'" . '}"';

        $readonly = ((string)$this->element['readonly'] == 'true') ? ' readonly="readonly"' : '';
        $disabled = ((string)$this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';

        $onchange = $this->element['onchange'] ? ' onchange="' . (string)$this->element['onchange'] . '"' : '';

        MolajoController::getApplication()->addJavascriptLink('../media/js/jscolor.js');

        return '<input type="text" name="' . $this->name . '" id="' . $this->id . '"' .
               ' value="' . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"' .
               $class . $size . $disabled . $readonly . $onchange . $maxLength . '/>';

    }
}
