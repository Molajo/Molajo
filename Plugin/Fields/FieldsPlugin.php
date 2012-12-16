<?php
/**
 * @package    Niambie
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    MIT, see License folder
 */
namespace Molajo\Plugin\Fields;

use Molajo\Plugin\Plugin\Plugin;

defined('NIAMBIE') or die;

/**
 * @package     Niambie
 * @license     MIT
 * @since       1.0
 */
class FieldsPlugin extends Plugin
{
    /**
     * Generates list of Fields for select lists and defining Custom Fields
     *
     * @return  boolean
     * @since   1.0
     */
    public function onBeforeInclude()
    {
        if (APPLICATION_ID == 2) {
        } else {
            return true;
        }

        $model_name = $this->get('model_name', '', 'parameters');
        $model_type = $this->get('model_type', '', 'parameters');

        $model_registry = ucfirst(strtolower($model_name)) . ucfirst(strtolower($model_type));

        $extended_literal = ' (' . Services::Language()->translate('extended') . ')';
        $parameter_literal = ' (' . Services::Language()->translate('parameter') . ')';
        $customfield_literal = ' (' . Services::Language()->translate('customfield') . ')';
        $metadata_literal = ' (' . Services::Language()->translate(METADATA_LITERAL) . ')';

        $fieldArray = array();
        $standardArray = array();

        $normalFields = $this->get(strtolower(FIELDS_LITERAL), array(), 'model_registry');

        $status = 0;

        if (count($normalFields) > 0) {

            foreach ($normalFields as $field) {

                $temp_row = new \stdClass();
                $temp_row->id = $field['name'];
                $temp_row->value = $field['name'];

                $standardArray[] = $temp_row;
                $fieldArray[] = $temp_row;

                if ($field['name'] == 'status') {
                    $status = 1;
                }

                if ($field['type'] == 'datetime') {
                    $temp_row = new \stdClass();
                    $temp_row->id = $field['name'] . '_n_days_ago';
                    $temp_row->value = $field['name'] . '_n_days_ago ' . $extended_literal;
                    $fieldArray[] = $temp_row;

                    $temp_row = new \stdClass();
                    $temp_row->id = $field['name'] . '_ccyy';
                    $temp_row->value = $field['name'] . '_ccyy' . $extended_literal;
                    $fieldArray[] = $temp_row;

                    $temp_row = new \stdClass();
                    $temp_row->id = $field['name'] . '_mm';
                    $temp_row->value = $field['name'] . '_mm' . $extended_literal;
                    $fieldArray[] = $temp_row;

                    $temp_row = new \stdClass();
                    $temp_row->id = $field['name'] . '_dd';
                    $temp_row->value = $field['name'] . '_dd' . $extended_literal;
                    $fieldArray[] = $temp_row;

                    $temp_row = new \stdClass();
                    $temp_row->id = $field['name'] . '_ccyy_mm_dd';
                    $temp_row->value = $field['name'] . '_ccyy_mm_dd' . $extended_literal;
                    $fieldArray[] = $temp_row;

                    $temp_row = new \stdClass();
                    $temp_row->id = $field['name'] . '_month_name_abbr';
                    $temp_row->value = $field['name'] . '_month_name_abbr' . $extended_literal;
                    $fieldArray[] = $temp_row;

                    $temp_row = new \stdClass();
                    $temp_row->id = $field['name'] . '_month_name';
                    $temp_row->value = $field['name'] . '_month_name' . $extended_literal;
                    $fieldArray[] = $temp_row;

                    $temp_row = new \stdClass();
                    $temp_row->id = $field['name'] . '_time';
                    $temp_row->value = $field['name'] . '_time' . $extended_literal;
                    $fieldArray[] = $temp_row;

                    $temp_row = new \stdClass();
                    $temp_row->id = $field['name'] . '_day_number';
                    $temp_row->value = $field['name'] . '_day_number' . $extended_literal;
                    $fieldArray[] = $temp_row;

                    $temp_row = new \stdClass();
                    $temp_row->id = $field['name'] . '_day_name_abbr';
                    $temp_row->value = $field['name'] . '_day_name_abbr' . $extended_literal;
                    $fieldArray[] = $temp_row;

                    $temp_row = new \stdClass();
                    $temp_row->id = $field['name'] . '_day_name';
                    $temp_row->value = $field['name'] . '_day_name' . $extended_literal;
                    $fieldArray[] = $temp_row;
                }

                if ($field['type'] == 'text') {
                    $temp_row = new \stdClass();
                    $temp_row->id = $field['name'] . '_introductory';
                    $temp_row->value = $field['name'] . '_introductory' . $extended_literal;
                    $fieldArray[] = $temp_row;

                    $temp_row = new \stdClass();
                    $temp_row->id = $field['name'] . '_fulltext';
                    $temp_row->value = $field['name'] . '_fulltext' . $extended_literal;
                    $fieldArray[] = $temp_row;

                    $temp_row = new \stdClass();
                    $temp_row->id = $field['name'] . '_snippet';
                    $temp_row->value = $field['name'] . '_snippet' . $extended_literal;
                    $fieldArray[] = $temp_row;
                }
            }
        }

        if ($status == 0) {
        } else {
            $temp_row = new \stdClass();
            $temp_row->id = 'status_name';
            $temp_row->value = 'status_name' . $extended_literal;
            $fieldArray[] = $temp_row;
        }

        $joins = $this->get('joins', array(), 'model_registry');

        if (count($joins) > 0) {
            foreach ($joins as $field) {
                $temp = explode(',', $field['select']);
                if (count($temp) > 0) {
                    foreach ($temp as $f) {
                        if (trim($f) == '') {
                        } else {
                            $temp_row = new \stdClass();
                            $temp_row->id = $field['alias'] . '_' . $f;
                            $temp_row->value = $field['alias'] . '_' . $f . $extended_literal;

                            $fieldArray[] = $temp_row;
                        }
                    }
                }
            }
        }

//$exists = Services::Registry()->exists($model_registry, CUSTOMFIELDGROUPS_LITERAL);
        $customfields = $this->get(strtolower(CUSTOMFIELDS_LITERAL), array(), 'model_registry');
        $customFieldArray = array();
        if (count($customfields) > 0) {
            foreach ($customfields as $field) {
                $temp_row = new \stdClass();
                $temp_row->id = $field['name'];
                $temp_row->value = $field['name'] . $customfield_literal;

                $fieldArray[] = $temp_row;
                $standardArray[] = $temp_row;
                $customFieldArray[] = $temp_row;
            }
        }

        $parameters = $this->get(strtolower(PARAMETERS_LITERAL), array(), 'model_registry');
        $parametersArray = array();
        if (count($parameters) > 0) {
            foreach ($parameters as $field) {
                $temp_row = new \stdClass();
                $temp_row->id = 'parameter' . '_' . $field['name'];
                $temp_row->value = $field['name'] . $parameter_literal;

                $fieldArray[] = $temp_row;
                $parametersArray[] = $temp_row;
            }
        }

        $metadata = $this->get(strtolower(METADATA_LITERAL), array(), 'model_registry');
        if (count($metadata) > 0) {
            foreach ($metadata as $field) {
                $temp_row = new \stdClass();
                $temp_row->id = METADATA_LITERAL . '_' . $field['name'];
                $temp_row->value = METADATA_LITERAL . '_' . $field['name'] . $metadata_literal;

                $fieldArray[] = $temp_row;
                $standardArray[] = $temp_row;
                $metadataArray[] = $temp_row;
            }
        }

        asort($fieldArray);
        asort($standardArray);
        asort($metadataArray);
        asort($parametersArray);
        asort($customFieldArray);

        Services::Registry()->set(DATALIST_LITERAL, $model_registry . FIELDS_LITERAL, $fieldArray);
        Services::Registry()->set(DATALIST_LITERAL, $model_registry . FIELDS_STANDARD_LITERAL, $standardArray);
        Services::Registry()->set(DATALIST_LITERAL, $model_registry . FIELDS_METADATA_LITERAL, $metadataArray);
        Services::Registry()->set(
            DATALIST_LITERAL,
            $model_registry . FIELDS_PARAMETERS_LITERAL,
            $parametersArray
        );
        Services::Registry()->set(DATALIST_LITERAL, $model_registry . FIELDS_CUSTOM_LITERAL, $customFieldArray);

        return true;
    }
}
