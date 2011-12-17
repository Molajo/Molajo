<?php
/**
 * @package     Molajo
 * @subpackage  Form
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2012 Cristina Solano. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Supports an HTML select list of menu item
 *
 * @package    Molajo
 * @subpackage  Form
 * @since       1.0
 */
class MolajoFormFieldMenuItem extends MolajoFormFieldGroupedList
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  1.0
     */
    public $type = 'MenuItem';

    /**
     * Method to get the field option groups.
     *
     * @return  array  The field option objects as a nested array in groups.
     * @since   1.0
     */
    protected function getGroups()
    {
        // Initialize variables.
        $groups = array();

        // Initialize some field attributes.
        $menuType = (string)$this->element['menu_type'];
        $published = $this->element['published'] ? explode(',', (string)$this->element['published']) : array();
        $disable = $this->element['disable'] ? explode(',', (string)$this->element['disable']) : array();
        $language = $this->element['language'] ? explode(',', (string)$this->element['language']) : array();

        // Get the menu items.
        $items = MenusHelper::getMenuLinks($menuType, 0, 0, $published, $language);

        // Build group for a specific menu type.
        if ($menuType) {
            // Initialize the group.
            $groups[$menuType] = array();

            // Build the options array.
            foreach ($items as $link) {
                $groups[$menuType][] = MolajoHTML::_('select.option', $link->value, $link->text, 'value', 'text', in_array($link->type, $disable));
            }
        }

            // Build groups for all menu types.
        else {
            // Build the groups arrays.
            foreach ($items as $menu)
            {
                // Initialize the group.
                $groups[$menu->menu_id] = array();

                // Build the options array.
                foreach ($menu->links as $link) {
                    $groups[$menu->menu_id][] = MolajoHTML::_('select.option', $link->value, $link->text, 'value', 'text', in_array($link->type, $disable));
                }
            }
        }

        // Merge any additional groups in the XML definition.
        $groups = array_merge(parent::getGroups(), $groups);

        return $groups;
    }
}
