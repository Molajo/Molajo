<?php
/**
 * Fields Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugin\Fields;

use CommonApi\Event\DisplayInterface;
use Molajo\Plugin\DisplayEventPlugin;
use stdClass;

/**
 * Fields Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
class FieldsPlugin extends DisplayEventPlugin implements DisplayInterface
{
    /**
     * Generates list of Fields for select lists and defining Custom Fields
     *
     * @return  $this
     * @since   1.0
     */
    public function onBeforeRender()
    {
        $model_registry = $this->runtime_data->resource->model_registry;

        $fields = $model_registry['fields'];

        if (count($fields) > 0 && is_array($fields)) {
            foreach ($fields as $field) {
                $row     = new stdClass();
                $row->id = $field['name'];

                if (isset($field['calculated']) && $field['calculated'] == 1) {
                    $row->value             = $field['name'] . ' (' . $this->language_controller->translate(
                            'Extended'
                        ) . ')';
                    $extended_field_array[] = $row;
                } else {
                    $row->value = $field['name'];
                }
                $field_array[]      = $row;
                $all_fields_array[] = $row;
            }
            $this->runtime_data->plugin_data->fieldsstandard = $field_array;
        }

        ksort($extended_field_array);
        $this->runtime_data->plugin_data->extended_field_array = $extended_field_array;

        $groups = $model_registry['customfieldgroups'];

        if (count($groups) > 0 && is_array($groups)) {
            foreach ($groups as $group) {

                $group_array = array();

                if (isset($model_registry[$group])) {
                    $fields = $model_registry[$group];

                    if (count($fields) > 0 && is_array($fields)) {
                        foreach ($fields as $field) {
                            $row        = new stdClass();
                            $row->id    = $field['name'];
                            $row->value = $field['name'] . ' (' . $this->language_controller->translate($group) . ')';

                            $group_array[]      = $row;
                            $all_fields_array[] = $row;
                        }
                        ksort($group_array);
                        $this->runtime_data->plugin_data->$group = $group_array;
                    }
                }
            }
        }

        $joins = $model_registry['joins'];

        if (count($joins) > 0) {
            foreach ($joins as $field) {
                $row = explode(',', $field['select']);
                if (count($row) > 0) {
                    foreach ($row as $f) {
                        if (trim($f) == '') {
                        } else {
                            $row        = new stdClass();
                            $row->id    = $field['alias'] . '_' . $f;
                            $row->value = $field['alias'] . '_' . $f . ' (' . $this->language_controller->translate(
                                    'joins'
                                ) . ')';

                            $join_array[]       = $row;
                            $all_fields_array[] = $row;
                        }
                        ksort($group_array);
                        $this->runtime_data->plugin_data->$group = $group_array;
                    }
                }
            }
        }

        asort($all_fields_array);
        $this->runtime_data->plugin_data->fields = $all_fields_array;

        return $this;
    }
}
