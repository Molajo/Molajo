<?php
/**
 * Custom Fields
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Fields;

use stdClass;

/**
 * Custom Fields Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
abstract class CustomFields extends StandardFields
{
    /**
     * Set Custom Fields
     *
     * @param   array $model_registry
     *
     * @return  FieldsPlugin
     * @since   1.0.0
     */
    protected function setCustomFields($model_registry)
    {
        $groups = $model_registry['customfieldgroups'];

        if (count($groups) === 0) {
            return $this;
        }

        foreach ($groups as $group) {
            $this->setCustomFieldsGroup($model_registry, $group);
        }

        return $this;
    }

    /**
     * Set Custom Fields Group
     *
     * @param   array $model_registry
     * @param   string $group
     *
     * @return  FieldsPlugin
     * @since   1.0.0
     */
    protected function setCustomFieldsGroup($model_registry, $group)
    {
        if (isset($model_registry[$group])) {

            $fields = $model_registry[$group];

            if (count($fields) > 0) {
                $this->setCustomFieldGroupFields($fields, $group);
            }
        }

        return $this;
    }

    /**
     * Set Custom Fields for a specific Group
     *
     * @param   array  $fields
     * @param   string $group
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setCustomFieldGroupFields($fields, $group)
    {
        foreach ($fields as $field) {
            $row           = new stdClass();
            $row->id       = $field['name'];
            $row->value    = $field['name'] . ' (' . $this->language_controller->translateString($group) . ')';
            $group_array[] = $row;

            $this->all_fields_array[] = $row;
        }

        ksort($group_array);
        $this->plugin_data->$group = $group_array;

        return $this;
    }
}
