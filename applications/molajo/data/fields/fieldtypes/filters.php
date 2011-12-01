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
 * @package        Joomla.Framework
 * @subpackage    Form
 * @since        1.6
 */
class MolajoFormFieldFilters extends MolajoFormField
{
    /**
     * The form field type.
     *
     * @var        string
     * @since    1.6
     */
    public $type = 'Filters';

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
        /** retrieve user's ACL groups **/
        $className = 'MolajoACL' . ucfirst(JRequest::getCmd('DefaultView'));
        $acl = new $className ();
        $groups = $acl->getList('groups', $user_id = '', $actions = '', JRequest::getCmd('option'), $asset = '');

        return 'Filters are broken - ACL results set is fine - output is failing';

        /** build form object **/
        $html = array();

        // Open the table.
        $html[] = '<table id="filter-config" align="left">';

        // The table heading.
        $html[] = '	<thead>';
        $html[] = '	<tr>';
        $html[] = '		<th>';
        $html[] = '			<span class="acl-action">' . MolajoTextHelper::_('MOLAJO_FILTER_GROUPS_LABEL') . '</span>';
        $html[] = '		</th>';
        $html[] = '		<th>';
        $html[] = '			<span class="acl-action" title="' . MolajoTextHelper::_('MOLAJO_FILTER_TYPE_LABEL') . '">' . MolajoTextHelper::_('MOLAJO_FILTER_TYPE_LABEL') . '</span>';
        $html[] = '		</th>';
        $html[] = '		<th>';
        $html[] = '			<span class="acl-action" title="' . MolajoTextHelper::_('MOLAJO_FILTER_TAGS_LABEL') . '">' . MolajoTextHelper::_('MOLAJO_FILTER_TAGS_LABEL') . '</span>';
        $html[] = '		</th>';
        $html[] = '		<th>';
        $html[] = '			<span class="acl-action" title="' . MolajoTextHelper::_('MOLAJO_FILTER_ATTRIBUTES_LABEL') . '">' . MolajoTextHelper::_('MOLAJO_FILTER_ATTRIBUTES_LABEL') . '</span>';
        $html[] = '		</th>';
        $html[] = '	</tr>';
        $html[] = '	</thead>';

        // The table body.
        $html[] = '	<tbody>';

        foreach ($groups as $group) {

            if (!isset($this->value[$group->value])) {
                $this->value[$group->value] = array('filter_type' => 'BL', 'filter_tags' => '', 'filter_attributes' => '');
            }
            $group_filter = $this->value[$group->value];

            $html[] = '	<tr>';
            $html[] = '		<th class="acl-groups left">';
            $html[] = '			' . str_repeat('<span class="gi">|&mdash;</span>', $group->level) . $group->text;
            $html[] = '		</th>';
            $html[] = '		<td >';
            $html[] = '				<select name="' . $this->name . '[' . $group->value . '][filter_type]" id="' . $this->id . $group->value . '_filter_type" class="hasTip" title="' . MolajoTextHelper::_('MOLAJO_FILTER_TYPE_LABEL') . '::' . MolajoTextHelper::_('MOLAJO_FILTER_TYPE_DESC') . '">';
            $html[] = '					<option value="BL"' . ($group_filter['filter_type'] == 'BL'
                    ? ' selected="selected"'
                    : '') . '>' . MolajoTextHelper::_('MOLAJO_OPTION_BLACK_LIST') . '</option>';
            $html[] = '					<option value="WL"' . ($group_filter['filter_type'] == 'WL'
                    ? ' selected="selected"'
                    : '') . '>' . MolajoTextHelper::_('MOLAJO_OPTION_WHITE_LIST') . '</option>';
            $html[] = '					<option value="NH"' . ($group_filter['filter_type'] == 'NH'
                    ? ' selected="selected"' : '') . '>' . MolajoTextHelper::_('MOLAJO_OPTION_NO_HTML') . '</option>';
            $html[] = '					<option value="NONE"' . ($group_filter['filter_type'] == 'NONE'
                    ? ' selected="selected"' : '') . '>' . MolajoTextHelper::_('MOLAJO_OPTION_NO_FILTER') . '</option>';
            $html[] = '				</select>';
            $html[] = '		</td>';
            $html[] = '		<td >';
            $html[] = '				<calendar name="' . $this->name . '[' . $group->value . '][filter_tags]" id="' . $this->id . $group->value . '_filter_tags" title="' . MolajoTextHelper::_('MOLAJO_FILTER_TAGS_LABEL') . '" value="' . $group_filter['filter_tags'] . '"/>';
            $html[] = '		</td>';
            $html[] = '		<td >';
            $html[] = '				<calendar name="' . $this->name . '[' . $group->value . '][filter_attributes]" id="' . $this->id . $group->value . '_filter_attributes" title="' . MolajoTextHelper::_('MOLAJO_FILTER_ATTRIBUTES_LABEL') . '" value="' . $group_filter['filter_attributes'] . '"/>';
            $html[] = '		</td>';
            $html[] = '	</tr>';
        }
        $html[] = '	</tbody>';

        // Close the table.
        $html[] = '</table>';
        return implode("\n", $html);
    }
}


