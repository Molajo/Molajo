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
 * Field to select a user id from a modal list.
 *
 * @package    Molajo
 * @subpackage  users
 * @since       1.0
 */
class MolajoFormFieldUser extends MolajoFormField
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  1.0
     */
    public $type = 'User';

    /**
     * Method to get the field calendar markup.
     *
     * @return  string  The field calendar markup.
     * @since   1.0
     */
    protected function getInput()
    {

        // Initialize variables.
        $html = array();
        $groups = $this->getGroups();
        $excluded = $this->getExcluded();
        $link = 'index.php?option=users&amp;view=users&amp;layout=modal&amp;layout=component&amp;field='.$this->id.(isset($groups)
                ? ('&amp;groups='.base64_encode(json_encode($groups))) : '').(isset($excluded)
                ? ('&amp;excluded='.base64_encode(json_encode($excluded))) : '');

        // Initialize some field attributes.
        $attr = $this->element['class'] ? ' class="'.(string)$this->element['class'].'"' : '';
        $attr .= $this->element['size'] ? ' size="'.(int)$this->element['size'].'"' : '';

        // Initialize JavaScript field attributes.
        $onchange = (string)$this->element['onchange'];

        // Load the modal behavior script.
        MolajoHTML::_('behavior.modal', 'a.modal_'.$this->id);

        // Build the script.
        $script = array();
        $script[] = '	function jSelectUser_'.$this->id.'(id, title) {';
        $script[] = '		var old_id = document.getElementById("'.$this->id.'_id").value;';
        $script[] = '		if (old_id != id) {';
        $script[] = '			document.getElementById("'.$this->id.'_id").value = id;';
        $script[] = '			document.getElementById("'.$this->id.'_name").value = title;';
        $script[] = '			'.$onchange;
        $script[] = '		}';
        $script[] = '		SqueezeBox.close();';
        $script[] = '	}';

        // Add the script to the document head.
        MolajoFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

        // Load the current username if available.
        $table = MolajoTable::getInstance('user');
        if ($this->value) {
            $table->load($this->value);
        } else {
            $table->username = MolajoText::_('MOLAJO_FORM_SELECT_USER');
        }
        return;
        // Create a dummy text field with the user name.
        $html[] = '<div class="fltlft">';
        $html[] = '	<calendar type="text" id="'.$this->id.'_name"' .
                  ' value="'.htmlspecialchars($table->username, ENT_COMPAT, 'UTF-8').'"' .
                  ' disabled="disabled"'.$attr.' />';
        $html[] = '</div>';

        // Create the user select button.
        $html[] = '<div class="button2-left">';
        $html[] = '  <div class="blank">';
        if ($this->element['readonly'] != 'true') {
            $html[] = '		<a class="modal_'.$this->id.'" title="'.MolajoText::_('MOLAJO_FORM_CHANGE_USER').'"' .
                      ' href="'.$link.'"' .
                      ' rel="{handler: \'iframe\', size: {x: 800, y: 500}}">';
            $html[] = '			'.MolajoText::_('MOLAJO_FORM_CHANGE_USER').'</a>';
        }
        $html[] = '  </div>';
        $html[] = '</div>';

        // Create the real field, hidden, that stored the user id.
        $html[] = '<calendar type="hidden" id="'.$this->id.'_id" name="'.$this->name.'" value="'.(int)$this->value.'" />';

        return implode("\n", $html);
    }

    /**
     * Method to get the filtering groups (null means no filtering)
     *
     * @return  mixed  array of filtering groups or null.
     * @since   1.0
     */
    protected function getGroups()
    {
        return null;
    }

    /**
     * Method to get the users to exclude from the list of users
     *
     * @return  mixed  array of users to exclude or null to to not exclude them
     * @since   1.0
     */
    protected function getExcluded()
    {
        return null;
    }
}
