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
 * MolajoFormFieldModal_Item
 *
 * Supports a modal item picker.
 *
 * @package        Molajo
 * @subpackage    Lists Content for Single Item Selection
 * @since        1.6
 */
class MolajoFormFieldModal_item extends MolajoFormField
{
    /**
     * The form field type.
     *
     * @var        string
     * @since    1.6
     */
    protected $type = 'Modal_Item';

    /**
     * Method to get the field calendar markup.
     *
     * @return    string    The field calendar markup.
     * @since    1.6
     */
    protected function getInput()
    {
        // Load the modal behavior script.
        MolajoHTML::_('behavior.modal', 'a.modal');

        // Build the script.
        $script = array();
        $script[] = '	function jSelectItem_' . $this->id . '(id, title, catid, object) {';
        $script[] = '		document.id("' . $this->id . '_id").value = id;';
        $script[] = '		document.id("' . $this->id . '_name").value = title;';
        $script[] = '		SqueezeBox.close();';
        $script[] = '	}';

        // Add the script to the document head.
        MolajoFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

        // Setup variables for display.
        $html = array();
        $link = 'index.php?option=' . $this->element['extension'] . '&amp;view=' . $this->element['view'] . '&amp;layout=modal&amp;layout=component&amp;function=jSelectItem_' . $this->id;

        $db = MolajoFactory::getDBO();
        $db->setQuery(
            'SELECT title' .
            ' FROM ' . $this->element['table'] .
            ' WHERE id = ' . (int)$this->value
        );
        $title = $db->loadResult();

        if ($error = $db->getErrorMsg()) {
            MolajoError::raiseWarning(500, $error);
        }

        $title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

        // The current user display field.
        $html[] = '<div class="fltlft">';
        $html[] = '  <calendar type="text" id="' . $this->id . '_name" value="' . $title . '" disabled="disabled" size="35" />';
        $html[] = '</div>';

        // The user select button.
        $html[] = '<div class="button2-left">';
        $html[] = '  <div class="blank">';
        $html[] = '	<a class="modal" title="' . MolajoTextHelper::_('MOLAJO_CHANGE_ITEM') . '"  href="' . $link . '" rel="{handler: \'iframe\', size: {x: 800, y: 450}}">' . MolajoTextHelper::_('MOLAJO_SELECT_AN_ITEM') . '</a>';
        $html[] = '  </div>';
        $html[] = '</div>';

        // The active article id field.
        if (0 == (int)$this->value) {
            $value = '';
        } else {
            $value = (int)$this->value;
        }

        // class='required' for application side validation
        $class = '';
        if ($this->required) {
            $class = ' class="required modal-value"';
        }

        $html[] = '<calendar type="hidden" id="' . $this->id . '_id"' . $class . ' name="' . $this->name . '" value="' . $value . '" />';

        return implode("\n", $html);
    }
}