<?php
/**
 * @package    Niambie
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    MIT
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

        $extended_literal    = ' (' . Services::Language()->translate('extended') . ')';
        $parameter_literal   = ' (' . Services::Language()->translate('parameter') . ')';
        $customfield_literal = ' (' . Services::Language()->translate('customfield') . ')';
        $metadata_literal    = ' (' . Services::Language()->translate('metadata') . ')';

        $fieldArray    = array();
        $standardArray = array();

        $normalFields = $this->get('Fields', array(), 'model_registry');

        $status = 0;

        if (count($normalFields) > 0) {

            foreach ($normalFields as $field) {

                $temp_row        = new \stdClass();
                $temp_row->id    = $field['name'];
                $temp_row->value = $field['name'];

                $standardArray[] = $temp_row;
                $fieldArray[]    = $temp_row;

                if ($field['name'] == 'status') {
                    $status = 1;
                }

                if ($field['type'] == 'datetime') {
                    $temp_row        = new \stdClass();
                    $temp_row->id    = $field['name'] . '_n_days_ago';
                    $temp_row->value = $field['name'] . '_n_days_ago ' . $extended_literal;
                    $fieldArray[]    = $temp_row;

                    $temp_row        = new \stdClass();
                    $temp_row->id    = $field['name'] . '_ccyy';
                    $temp_row->value = $field['name'] . '_ccyy' . $extended_literal;
                    $fieldArray[]    = $temp_row;

                    $temp_row        = new \stdClass();
                    $temp_row->id    = $field['name'] . '_mm';
                    $temp_row->value = $field['name'] . '_mm' . $extended_literal;
                    $fieldArray[]    = $temp_row;

                    $temp_row        = new \stdClass();
                    $temp_row->id    = $field['name'] . '_dd';
                    $temp_row->value = $field['name'] . '_dd' . $extended_literal;
                    $fieldArray[]    = $temp_row;

                    $temp_row        = new \stdClass();
                    $temp_row->id    = $field['name'] . '_ccyy_mm_dd';
                    $temp_row->value = $field['name'] . '_ccyy_mm_dd' . $extended_literal;
                    $fieldArray[]    = $temp_row;

                    $temp_row        = new \stdClass();
                    $temp_row->id    = $field['name'] . '_month_name_abbr';
                    $temp_row->value = $field['name'] . '_month_name_abbr' . $extended_literal;
                    $fieldArray[]    = $temp_row;

                    $temp_row        = new \stdClass();
                    $temp_row->id    = $field['name'] . '_month_name';
                    $temp_row->value = $field['name'] . '_month_name' . $extended_literal;
                    $fieldArray[]    = $temp_row;

                    $temp_row        = new \stdClass();
                    $temp_row->id    = $field['name'] . '_time';
                    $temp_row->value = $field['name'] . '_time' . $extended_literal;
                    $fieldArray[]    = $temp_row;

                    $temp_row        = new \stdClass();
                    $temp_row->id    = $field['name'] . '_day_number';
                    $temp_row->value = $field['name'] . '_day_number' . $extended_literal;
                    $fieldArray[]    = $temp_row;

                    $temp_row        = new \stdClass();
                    $temp_row->id    = $field['name'] . '_day_name_abbr';
                    $temp_row->value = $field['name'] . '_day_name_abbr' . $extended_literal;
                    $fieldArray[]    = $temp_row;

                    $temp_row        = new \stdClass();
                    $temp_row->id    = $field['name'] . '_day_name';
                    $temp_row->value = $field['name'] . '_day_name' . $extended_literal;
                    $fieldArray[]    = $temp_row;
                }

                if ($field['type'] == 'text') {
                    $temp_row        = new \stdClass();
                    $temp_row->id    = $field['name'] . '_introductory';
                    $temp_row->value = $field['name'] . '_introductory' . $extended_literal;
                    $fieldArray[]    = $temp_row;

                    $temp_row        = new \stdClass();
                    $temp_row->id    = $field['name'] . '_fulltext';
                    $temp_row->value = $field['name'] . '_fulltext' . $extended_literal;
                    $fieldArray[]    = $temp_row;

                    $temp_row        = new \stdClass();
                    $temp_row->id    = $field['name'] . '_snippet';
                    $temp_row->value = $field['name'] . '_snippet' . $extended_literal;
                    $fieldArray[]    = $temp_row;
                }
            }
        }

        if ($status == 0) {
        } else {
            $temp_row        = new \stdClass();
            $temp_row->id    = 'status_name';
            $temp_row->value = 'status_name' . $extended_literal;
            $fieldArray[]    = $temp_row;
        }

        $joins = $this->get('joins', array(), 'model_registry');

        if (count($joins) > 0) {
            foreach ($joins as $field) {
                $temp = explode(',', $field['select']);
                if (count($temp) > 0) {
                    foreach ($temp as $f) {
                        if (trim($f) == '') {
                        } else {
                            $temp_row        = new \stdClass();
                            $temp_row->id    = $field['alias'] . '_' . $f;
                            $temp_row->value = $field['alias'] . '_' . $f . $extended_literal;

                            $fieldArray[] = $temp_row;
                        }
                    }
                }
            }
        }

//$exists = Services::Registry()->exists($model_registry, 'customfieldgroups');
        $customfields     = $this->get('customfields', array(), 'model_registry');
        $customFieldArray = array();
        if (count($customfields) > 0) {
            foreach ($customfields as $field) {
                $temp_row        = new \stdClass();
                $temp_row->id    = $field['name'];
                $temp_row->value = $field['name'] . $customfield_literal;

                $fieldArray[]       = $temp_row;
                $standardArray[]    = $temp_row;
                $customFieldArray[] = $temp_row;
            }
        }

        $parameters      = $this->get(strtolower('Parameters'), array(), 'model_registry');
        $parametersArray = array();
        if (count($parameters) > 0) {
            foreach ($parameters as $field) {
                $temp_row        = new \stdClass();
                $temp_row->id    = 'parameter' . '_' . $field['name'];
                $temp_row->value = $field['name'] . $parameter_literal;

                $fieldArray[]      = $temp_row;
                $parametersArray[] = $temp_row;
            }
        }

        $metadata = $this->get('metadata', array(), 'model_registry');
        if (count($metadata) > 0) {
            foreach ($metadata as $field) {
                $temp_row        = new \stdClass();
                $temp_row->id    = 'Metadata' . '_' . $field['name'];
                $temp_row->value = 'Metadata' . '_' . $field['name'] . $metadata_literal;

                $fieldArray[]    = $temp_row;
                $standardArray[] = $temp_row;
                $metadataArray[] = $temp_row;
            }
        }

        asort($fieldArray);
        asort($standardArray);
        asort($metadataArray);
        asort($parametersArray);
        asort($customFieldArray);

        Services::Registry()->set('Datalist', $model_registry . 'Fields', $fieldArray);
        Services::Registry()->set('Datalist', $model_registry . 'Fieldsstandard', $standardArray);
        Services::Registry()->set('Datalist', $model_registry . 'Metadata', $metadataArray);
        Services::Registry()->set(
            'Datalist',
            $model_registry . 'Fieldsparameters',
            $parametersArray
        );
        Services::Registry()->set('Datalist', $model_registry . 'Fieldscustom', $customFieldArray);

        return true;
    }
}
