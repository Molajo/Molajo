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
class MolajoFormFieldMedia extends MolajoFormField
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  1.0
     */
    protected $type = 'Media';

    /**
     * The initialised state of the document object.
     *
     * @var    boolean
     * @since  1.0
     */
    protected static $initialised = false;

    /**
     * Method to get the field calendar markup.
     *
     * @return  string  The field calendar markup.
     * @since   1.0
     */
    protected function getInput()
    {
        $assetField = $this->element['asset_field'] ? (string)$this->element['asset_field'] : 'asset_id';
        $authorField = $this->element['created_by_field'] ? (string)$this->element['created_by_field'] : 'created_by';
        $asset = $this->form->getValue($assetField) ? $this->form->getValue($assetField)
                : (string)$this->element['asset_id'];
        if ($asset == '') {
            $asset = JRequest::getCmd('option');
        }

        $link = (string)$this->element['link'];
        if (!self::$initialised) {

            // Load the modal behavior script.
            MolajoHTML::_('behavior.modal');

            // Build the script.
            $script = array();
            $script[] = '	function jInsertFieldValue(value, id) {';
            $script[] = '		var old_id = document.id(id).value;';
            $script[] = '		if (old_id != id) {';
            $script[] = '			var elem = document.id(id)';
            $script[] = '			elem.value = value;';
            $script[] = '			elem.fireEvent("change");';
            $script[] = '		}';
            $script[] = '	}';

            // Add the script to the document head.
            MolajoFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

            self::$initialised = true;
        }

        // Initialize variables.
        $html = array();
        $attr = '';

        // Initialize some field attributes.
        $attr .= $this->element['class'] ? ' class="' . (string)$this->element['class'] . '"' : '';
        $attr .= $this->element['size'] ? ' size="' . (int)$this->element['size'] . '"' : '';

        // Initialize JavaScript field attributes.
        $attr .= $this->element['onchange'] ? ' onchange="' . (string)$this->element['onchange'] . '"' : '';

        // The text field.
        $html[] = '<div class="fltlft">';
        $html[] = '	<calendar type="text" name="' . $this->name . '" id="' . $this->id . '"' .
                  ' value="' . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"' .
                  ' readonly="readonly"' . $attr . ' />';
        $html[] = '</div>';

        $directory = (string)$this->element['directory'];
        if ($this->value && file_exists(MOLAJO_BASE_FOLDER . '/' . $this->value)) {
            $folder = explode('/', $this->value);
            array_shift($folder);
            array_pop($folder);
            $folder = implode('/', $folder);
        }
        elseif (file_exists(MOLAJO_BASE_FOLDER . '/' . MolajoComponent::getParameters('media')->get('image_path', 'images') . '/' . $directory)) {
            $folder = $directory;
        }
        else {
            $folder = '';
        }
        // The button.
        $html[] = '<div class="button2-left">';
        $html[] = '	<div class="blank">';
        $html[] = '		<a class="modal" title="' . MolajoTextHelper::_('MOLAJO_FORM_BUTTON_SELECT') . '"' .
                  ' href="' . ($this->element['readonly'] ? '' : ($link ? $link
                : 'index.php?option=media&amp;view=images&amp;layout=component&amp;asset=' . $asset . '&amp;author=' . $this->form->getValue($authorField)) . '&amp;fieldid=' . $this->id . '&amp;folder=' . $folder) . '"' .
                  ' rel="{handler: \'iframe\', size: {x: 800, y: 500}}">';
        $html[] = '			' . MolajoTextHelper::_('MOLAJO_FORM_BUTTON_SELECT') . '</a>';
        $html[] = '	</div>';
        $html[] = '</div>';

        $html[] = '<div class="button2-left">';
        $html[] = '	<div class="blank">';
        $html[] = '		<a title="' . MolajoTextHelper::_('MOLAJO_FORM_BUTTON_CLEAR') . '"' .
                  ' href="#"' .
                  ' onclick="document.getElementById(\'' . $this->id . '\').value=\'\'; document.getElementById(\'' . $this->id . '\').onchange();">';
        $html[] = '			' . MolajoTextHelper::_('MOLAJO_FORM_BUTTON_CLEAR') . '</a>';
        $html[] = '	</div>';
        $html[] = '</div>';

        return implode("\n", $html);
    }
}
