<?php
/**
 * Join Fields
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Fields;

use stdClass;

/**
 * Join Fields Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
abstract class JoinFields extends CustomFields
{
    /**
     * Set Custom Fields defined by Joins
     *
     * @param  array $model_registry
     *
     * @return  $this
     * @since   1.0.0
     */
    public function setCustomFieldsJoins($model_registry)
    {
        $joins = $model_registry['joins'];

        if (count($joins) === 0) {
            $this->plugin_data->fieldjoins = array();
            return $this;
        }

        $join_array = array();

        foreach ($joins as $field) {
            $join_array = $this->setCustomFieldsJoinsSingle($field, $join_array);
        }

        ksort($join_array);
        $this->plugin_data->fieldsjoins = $join_array;

        return $this;
    }

    /**
     * Process a single column defined by a model registry join
     *
     * @param   array  $field
     * @param   array  $join_array
     *
     * @return  array
     * @since   1.0.0
     */
    public function setCustomFieldsJoinsSingle($field, $join_array)
    {
        $row = explode(',', $field['select']);

        if (count($row) === 0) {
            return $join_array;
        }

        foreach ($row as $f) {

            if (trim($f) === '') {
            } else {
                $join_array = $this->setCustomFieldsJoinsSingleRow($field, $join_array, $f);
            }
        }

        return $join_array;
    }

    /**
     * Process a single column for a Join
     *
     * @param   array  $field
     * @param   array  $join_array
     * @param   string $f
     *
     * @return  stdClass[]
     * @since   1.0.0
     */
    public function setCustomFieldsJoinsSingleRow($field, $join_array, $f)
    {
        $row        = new stdClass();
        $row->id    = $field['alias'] . '_' . $f;
        $row->value = $row->id . ' (' . $this->language_controller->translateString('joins') . ')';
        $join_array[] = $row;
        $this->all_fields_array[] = $row;

        return $join_array;
    }
}
