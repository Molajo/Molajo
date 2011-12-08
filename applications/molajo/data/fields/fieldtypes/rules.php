<?php
/**
 * @package     Molajo
 * @subpackage  Form
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2012 Amy Stephen, Cristina Solano. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
defined('MOLAJO') or die;

/**
 * Form Field class for the Joomla Framework.
 *
 * @package    Molajo
 * @subpackage  Form
 * @since       1.0
 */
class MolajoFormFieldRules extends MolajoFormField
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  1.0
     */
    public $type = 'Rules';

    /**
     * Method to get the field calendar markup.
     *
     * TODO: Add access check.
     *
     * @return  string  The field calendar markup.
     * @since   1.0
     */
    protected function getInput()
    {
        MolajoHTML::_('behavior.tooltip');

        // Initialise some field attributes.
        $section = $this->element['section'] ? (string)$this->element['section'] : '';
        $component = $this->element['component'] ? (string)$this->element['component'] : '';
        $assetField = $this->element['asset_field'] ? (string)$this->element['asset_field'] : 'asset_id';

        // Get the actions for the asset.
        $acl = new MolajoACL();
        $actions = $acl->getList('Actions', '', array($component, $section));

        // Iterate over the children and add to the actions.
        foreach ($this->element->children() as $el)
        {
            if ($el->getName() == 'action') {
                $actions[] = (object)array(
                    'name' => (string)$el['name'],
                    'title' => (string)$el['title'],
                    'description' => (string)$el['description']
                );
            }
        }

        // Use the compact form for the content rules (deprecated).
        //if (!empty($component) && $section != 'component') {
        //	return MolajoHTML::_('rules.assetFormWidget', $actions, $assetId, $assetId ? null : $component, $this->name, $this->id);
        //}


        // Full width format.

        // Get the rules for just this asset (non-recursive).
        $assetId = 0;
        $assetRules = $acl->getList('Assetrules', '', array($assetId));

        /**
        object(JRules)#465 (1)
        { ["_data":protected]=> array(10)
        { ["core.login.site"]=> object(JRule)#471 (1)
        { ["_data":protected]=> array(2) { [6]=> int(1) [2]=> int(1) } }
         * ["core.login.admin"]=> object(JRule)#470 (1) { ["_data":protected]=> array(1) { [6]=> int(1) } }
         * ["core.login.offline"]=> object(JRule)#438 (1) { ["_data":protected]=> array(0) { } }
         * ["core.admin"]=> object(JRule)#492 (1) { ["_data":protected]=> array(1) { [8]=> int(1) } }
         * ["core.manage"]=> object(JRule)#491 (1) { ["_data":protected]=> array(1) { [7]=> int(1) } }
         * ["core.create"]=> object(JRule)#490 (1) { ["_data":protected]=> array(2) { [6]=> int(1) [3]=> int(1) } }
         * ["core.delete"]=> object(JRule)#489 (1) { ["_data":protected]=> array(1) { [6]=> int(1) } }
         * ["core.edit"]=> object(JRule)#488 (1) { ["_data":protected]=> array(2) { [6]=> int(1) [4]=> int(1) } }
         * ["core.edit.state"]=> object(JRule)#487 (1) { ["_data":protected]=> array(2) { [6]=> int(1) [5]=> int(1) } }
         * ["core.edit.own"]=> object(JRule)#486 (1) { ["_data":protected]=> array(2) { [6]=> int(1) [3]=> int(1) } } } }

         */
        // Get the available user groups.
        $groups = $acl->getList('Usergroups', $option = '', $task = '', $parameters = array());

        // Build the form control.
        $curLevel = 0;

        // Prepare output
        $html = array();
        $html[] = '<div id="permissions-sliders" class="pane-sliders">';
        $html[] = '<p class="rule-desc">' . MolajoTextHelper::_('MOLAJO_RULES_SETTINGS_DESC') . '</p>';
        $html[] = '<ul id="rules">';

        // Start a row for each user group.
        foreach ($groups as $group)
        {
            $difLevel = $group->level - $curLevel;

            if ($difLevel > 0) {
                $html[] = '<li><ul>';
            }
            else if ($difLevel < 0) {
                $html[] = str_repeat('</ul></li>', -$difLevel);
            }

            $html[] = '<li>';

            $html[] = '<div class="panel">';
            $html[] = '<h3 class="pane-toggler title"><a href="javascript:void(0);"><span>';
            $html[] = str_repeat('<span class="level">|&ndash;</span> ', $curLevel = $group->level) . $group->text;
            $html[] = '</span></a></h3>';
            $html[] = '<div class="pane-slider content pane-hide">';
            $html[] = '<div class="mypanel">';
            $html[] = '<table class="group-rules">';
            $html[] = '<thead>';
            $html[] = '<tr>';

            $html[] = '<th class="actions" id="actions-th' . $group->value . '">';
            $html[] = '<span class="acl-action">' . MolajoTextHelper::_('MOLAJO_RULES_ACTION') . '</span>';
            $html[] = '</th>';

            $html[] = '<th class="settings" id="settings-th' . $group->value . '">';
            $html[] = '<span class="acl-action">' . MolajoTextHelper::_('MOLAJO_RULES_SELECT_SETTING') . '</span>';
            $html[] = '</th>';

            // The calculated setting is not shown for the root group of global configuration.
            $canCalculateSettings = ($group->parent_id || !empty($component));
            if ($canCalculateSettings) {
                $html[] = '<th id="aclactionth' . $group->value . '">';
                $html[] = '<span class="acl-action">' . MolajoTextHelper::_('MOLAJO_RULES_CALCULATED_SETTING') . '</span>';
                $html[] = '</th>';
            }

            $html[] = '</tr>';
            $html[] = '</thead>';
            $html[] = '<tbody>';

            foreach ($actions as $action)
            {
                $html[] = '<tr>';
                $html[] = '<td headers="actions-th' . $group->value . '">';
                $html[] = '<label class="hasTip" for="' . $this->id . '_' . $action->name . '_' . $group->value . '" title="' . htmlspecialchars(MolajoTextHelper::_($action->title) . '::' . MolajoTextHelper::_($action->description), ENT_COMPAT, 'UTF-8') . '">';
                $html[] = MolajoTextHelper::_($action->title);
                $html[] = '</label>';
                $html[] = '</td>';

                $html[] = '<td headers="settings-th' . $group->value . '">';

                $html[] = '<select name="' . $this->name . '[' . $action->name . '][' . $group->value . ']" id="' . $this->id . '_' . $action->name . '_' . $group->value . '" title="' . MolajoTextHelper::sprintf('MOLAJO_RULES_SELECT_ALLOW_DENY_GROUP', MolajoTextHelper::_($action->title), trim($group->text)) . '">';

                $inheritedRule = JAccess::checkGroup($group->value, $action->name, $assetId);

                // Get the actual setting for the action for this group.
                $assetRule = $assetRules->allow($action->name, $group->value);

                // Build the dropdowns for the permissions sliders

                // The parent group has "Not Set", all children can rightly "Inherit" from that.
                $html[] = '<option value=""' . ($assetRule === null ? ' selected="selected"' : '') . '>' .
                          MolajoTextHelper::_(empty($group->parent_id) && empty($component) ? 'MOLAJO_RULES_NOT_SET'
                                                      : 'MOLAJO_RULES_INHERITED') . '</option>';
                $html[] = '<option value="1"' . ($assetRule === true ? ' selected="selected"' : '') . '>' .
                          MolajoTextHelper::_('MOLAJO_RULES_ALLOWED') . '</option>';
                $html[] = '<option value="0"' . ($assetRule === false ? ' selected="selected"' : '') . '>' .
                          MolajoTextHelper::_('MOLAJO_RULES_DENIED') . '</option>';

                $html[] = '</select>&#160; ';

                // If this asset's rule is allowed, but the inherited rule is deny, we have a conflict.
                if (($assetRule === true) && ($inheritedRule === false)) {
                    $html[] = MolajoTextHelper::_('MOLAJO_RULES_CONFLICT');
                }

                $html[] = '</td>';

                // Build the Calculated Settings column.
                // The inherited settings column is not displayed for the root group in global configuration.
                if ($canCalculateSettings) {
                    $html[] = '<td headers="aclactionth' . $group->value . '">';

                    // This is where we show the current effective settings considering currrent group, path and cascade.
                    // Check whether this is a component or global. Change the text slightly.

                    if (JAccess::checkGroup($group->value, 'administer') !== true) {
                        if ($inheritedRule === null) {
                            $html[] = '<span class="icon-16-unset">' .
                                      MolajoTextHelper::_('MOLAJO_RULES_NOT_ALLOWED') . '</span>';
                        }
                        else if ($inheritedRule === true) {
                            $html[] = '<span class="icon-16-allowed">' .
                                      MolajoTextHelper::_('MOLAJO_RULES_ALLOWED') . '</span>';
                        }
                        else if ($inheritedRule === false) {
                            if ($assetRule === false) {
                                $html[] = '<span class="icon-16-denied">' .
                                          MolajoTextHelper::_('MOLAJO_RULES_NOT_ALLOWED') . '</span>';
                            }
                            else {
                                $html[] = '<span class="icon-16-denied"><span class="icon-16-locked">' .
                                          MolajoTextHelper::_('MOLAJO_RULES_NOT_ALLOWED_LOCKED') . '</span></span>';
                            }
                        }
                    }
                    else if (!empty($component)) {
                        $html[] = '<span class="icon-16-allowed"><span class="icon-16-locked">' .
                                  MolajoTextHelper::_('MOLAJO_RULES_ALLOWED_ADMIN') . '</span></span>';
                    }
                    else {
                        // Special handling for  groups that have global admin because they can't  be denied.
                        // The admin rights can be changed.
                        if ($action->name === 'administer') {
                            $html[] = '<span class="icon-16-allowed">' .
                                      MolajoTextHelper::_('MOLAJO_RULES_ALLOWED') . '</span>';
                        }
                        elseif ($inheritedRule === false) {
                            // Other actions cannot be changed.
                            $html[] = '<span class="icon-16-denied"><span class="icon-16-locked">' .
                                      MolajoTextHelper::_('MOLAJO_RULES_NOT_ALLOWED_ADMINISTER_CONFLICT') . '</span></span>';
                        }
                        else {
                            $html[] = '<span class="icon-16-allowed"><span class="icon-16-locked">' .
                                      MolajoTextHelper::_('MOLAJO_RULES_ALLOWED_ADMIN') . '</span></span>';
                        }
                    }

                    $html[] = '</td>';
                }

                $html[] = '</tr>';
            }

            $html[] = '</tbody>';
            $html[] = '</table></div>';

            $html[] = '</div></div>';
            $html[] = '</li>';

        }

        $html[] = str_repeat('</ul></li>', $curLevel);
        $html[] = '</ul><div class="rule-notes">';
        if ($section == 'component' || $section == null) {
            $html[] = MolajoTextHelper::_('MOLAJO_RULES_SETTING_NOTES');
        } else {
            $html[] = MolajoTextHelper::_('MOLAJO_RULES_SETTING_NOTES_ITEM');
        }
        $html[] = '</div></div>';

        $js = "window.addEvent('domready', function(){ new Fx.Accordion($$('div#permissions-sliders.pane-sliders .panel h3.pane-toggler'), $$('div#permissions-sliders.pane-sliders .panel div.pane-slider'), {onActive: function(toggler, i) {toggler.addClass('pane-toggler-down');toggler.removeClass('pane-toggler');i.addClass('pane-down');i.removeClass('pane-hide');Cookie.write('jpanesliders_permissions-sliders" . $component . "',$$('div#permissions-sliders.pane-sliders .panel h3').indexOf(toggler));},onBackground: function(toggler, i) {toggler.addClass('pane-toggler');toggler.removeClass('pane-toggler-down');i.addClass('pane-hide');i.removeClass('pane-down');},duration: 300,display: " . JRequest::getInt('jpanesliders_permissions-sliders' . $component, 0, 'cookie') . ",show: " . JRequest::getInt('jpanesliders_permissions-sliders' . $component, 0, 'cookie') . ", alwaysHide:true, opacity: false}); });";

        MolajoFactory::getDocument()->addScriptDeclaration($js);

        return implode("\n", $html);
    }
}