<?php
/**
 * @package     Molajo
 * @subpackage  Form
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Cristina Solano. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die();

/**
 * Form Field class for the Joomla Framework.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class MolajoFormFieldCombo extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $type = 'Combo';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string   The field input markup.
	 * @since   11.1
	 */
	protected function getInput()
	{
		// Initialize variables.
		$html = array();
		$attr = '';

		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="combobox '.(string) $this->element['class'].'"' : ' class="combobox"';
		$attr .= ((string) $this->element['readonly'] == 'true') ? ' readonly="readonly"' : '';
		$attr .= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';

		// Initialize JavaScript field attributes.
		$attr .= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';

		// Get the field options.
		$options = $this->getOptions();

		// Load the combobox behavior.
		JHtml::_('behavior.combobox');

		// Build the input for the combo box.
		$html[] = '<input type="text" name="'.$this->name.'" id="'.$this->id.'"' .
				' value="'.htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8').'"'.$attr.'/>';

		// Build the list for the combo box.
		$html[] = '<ul id="combobox-'.$this->id.'" style="display:none;">';
		foreach ($options as $option) {
			$html[] = '<li>'.$option->text.'</li>';
		}
		$html[] = '</ul>';

		return implode($html);
	}
}
