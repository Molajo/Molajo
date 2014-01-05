<?php
/**
 * Fields Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugin\Fields;

use CommonApi\Event\ReadInterface;
use Molajo\Plugin\ReadEventPlugin;
use stdClass;

/**
 * Fields Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
class FieldsPlugin extends ReadEventPlugin implements ReadInterface
{
    /**
     * Generates list of Fields for select lists and defining Custom Fields
     *
     * @return  $this
     * @since   1.0
     */
    public function onAfterReadAll()
    {
        $extended_literal    = ' (' . $this->language_controller->translate('extended') . ')';
        $parameter_literal   = ' (' . $this->language_controller->translate('parameter') . ')';
        $customfield_literal = ' (' . $this->language_controller->translate('customfield') . ')';
        $metadata_literal    = ' (' . $this->language_controller->translate('metadata') . ')';

        $fieldArray    = array();
        $standardArray = array();

        $model_registry = $this->runtime_data->resource->model_registry;

        $normalFields = $model_registry['fields'];

        $status = 0;

        if (count($normalFields) > 0) {

            foreach ($normalFields as $field) {

                $temp_row        = new stdClass();
                $temp_row->id    = $field['name'];
                $temp_row->value = $field['name'];

                $standardArray[] = $temp_row;
                $fieldArray[]    = $temp_row;

                if ($field['name'] == 'status') {
                    $status = 1;
                }

                if ($field['type'] == 'datetime') {
                    $temp_row        = new stdClass();
                    $temp_row->id    = $field['name'] . '_n_days_ago';
                    $temp_row->value = $field['name'] . '_n_days_ago ' . $extended_literal;
                    $fieldArray[]    = $temp_row;

                    $temp_row        = new stdClass();
                    $temp_row->id    = $field['name'] . '_ccyy';
                    $temp_row->value = $field['name'] . '_ccyy' . $extended_literal;
                    $fieldArray[]    = $temp_row;

                    $temp_row        = new stdClass();
                    $temp_row->id    = $field['name'] . '_mm';
                    $temp_row->value = $field['name'] . '_mm' . $extended_literal;
                    $fieldArray[]    = $temp_row;

                    $temp_row        = new stdClass();
                    $temp_row->id    = $field['name'] . '_dd';
                    $temp_row->value = $field['name'] . '_dd' . $extended_literal;
                    $fieldArray[]    = $temp_row;

                    $temp_row        = new stdClass();
                    $temp_row->id    = $field['name'] . '_ccyy_mm_dd';
                    $temp_row->value = $field['name'] . '_ccyy_mm_dd' . $extended_literal;
                    $fieldArray[]    = $temp_row;

                    $temp_row        = new stdClass();
                    $temp_row->id    = $field['name'] . '_month_name_abbr';
                    $temp_row->value = $field['name'] . '_month_name_abbr' . $extended_literal;
                    $fieldArray[]    = $temp_row;

                    $temp_row        = new stdClass();
                    $temp_row->id    = $field['name'] . '_month_name';
                    $temp_row->value = $field['name'] . '_month_name' . $extended_literal;
                    $fieldArray[]    = $temp_row;

                    $temp_row        = new stdClass();
                    $temp_row->id    = $field['name'] . '_time';
                    $temp_row->value = $field['name'] . '_time' . $extended_literal;
                    $fieldArray[]    = $temp_row;

                    $temp_row        = new stdClass();
                    $temp_row->id    = $field['name'] . '_day_number';
                    $temp_row->value = $field['name'] . '_day_number' . $extended_literal;
                    $fieldArray[]    = $temp_row;

                    $temp_row        = new stdClass();
                    $temp_row->id    = $field['name'] . '_day_name_abbr';
                    $temp_row->value = $field['name'] . '_day_name_abbr' . $extended_literal;
                    $fieldArray[]    = $temp_row;

                    $temp_row        = new stdClass();
                    $temp_row->id    = $field['name'] . '_day_name';
                    $temp_row->value = $field['name'] . '_day_name' . $extended_literal;
                    $fieldArray[]    = $temp_row;
                }

                if ($field['type'] == 'text') {
                    $temp_row        = new stdClass();
                    $temp_row->id    = $field['name'] . '_introductory';
                    $temp_row->value = $field['name'] . '_introductory' . $extended_literal;
                    $fieldArray[]    = $temp_row;

                    $temp_row        = new stdClass();
                    $temp_row->id    = $field['name'] . '_fulltext';
                    $temp_row->value = $field['name'] . '_fulltext' . $extended_literal;
                    $fieldArray[]    = $temp_row;

                    $temp_row        = new stdClass();
                    $temp_row->id    = $field['name'] . '_snippet';
                    $temp_row->value = $field['name'] . '_snippet' . $extended_literal;
                    $fieldArray[]    = $temp_row;
                }
            }
        }

        if ($status == 0) {
        } else {
            $temp_row        = new stdClass();
            $temp_row->id    = 'status_name';
            $temp_row->value = 'status_name' . $extended_literal;
            $fieldArray[]    = $temp_row;
        }

        $joins = $model_registry['joins'];

        if (count($joins) > 0) {
            foreach ($joins as $field) {
                $temp = explode(',', $field['select']);
                if (count($temp) > 0) {
                    foreach ($temp as $f) {
                        if (trim($f) == '') {
                        } else {
                            $temp_row        = new stdClass();
                            $temp_row->id    = $field['alias'] . '_' . $f;
                            $temp_row->value = $field['alias'] . '_' . $f . $extended_literal;

                            $fieldArray[] = $temp_row;
                        }
                    }
                }
            }
        }

//$exists = $this->registry->exists($model_registry, 'customfieldgroups');
        $customfields     = $model_registry['customfields'];
        $customFieldArray = array();
        if (count($customfields) > 0) {
            foreach ($customfields as $field) {
                $temp_row        = new stdClass();
                $temp_row->id    = $field['name'];
                $temp_row->value = $field['name'] . $customfield_literal;

                $fieldArray[]       = $temp_row;
                $standardArray[]    = $temp_row;
                $customFieldArray[] = $temp_row;
            }
        }

        $runtime_data      = $model_registry['parameters'];
        $runtime_dataArray = array();
        if (count($runtime_data) > 0) {
            foreach ($runtime_data as $field) {
                $temp_row        = new stdClass();
                $temp_row->id    = 'parameter' . '_' . $field['name'];
                $temp_row->value = $field['name'] . $parameter_literal;

                $fieldArray[]        = $temp_row;
                $runtime_dataArray[] = $temp_row;
            }
        }

        $metadata = $model_registry['metadata'];
        if (count($metadata) > 0) {
            foreach ($metadata as $field) {
                $temp_row        = new stdClass();
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
        asort($runtime_dataArray);
        asort($customFieldArray);

        $this->runtime_data->plugin_data->datalists                 = new stdClass();
        $this->runtime_data->plugin_data->datalists->fields         = $fieldArray;
        $this->runtime_data->plugin_data->datalists->fieldsstandard = $standardArray;
        $this->runtime_data->plugin_data->datalists->metadata       = $metadataArray;

        $fieldname                                              = $model_registry['name'] . 'Fieldsruntime_data';
        $this->runtime_data->plugin_data->datalists->$fieldname = $runtime_dataArray;
        $fieldname                                              = $model_registry['name'] . 'Fieldscustom';
        $this->runtime_data->plugin_data->datalists->$fieldname = $customFieldArray;

        return $this;
    }
}
