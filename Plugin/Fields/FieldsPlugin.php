<?php
/**
 * Fields Plugin
 *
 * @package    Molajo
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugin\Fields;

use CommonApi\Event\SystemInterface;
use Exception\Plugin\PluginException;
use Molajo\Plugin\SystemEventPlugin;

/**
 * Fields Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
class FieldsPlugin extends SystemEventPlugin implements SystemInterface
{
    /**
     * Generates list of Fields for select lists and defining Custom Fields
     *
     * @return  $this
     * @since   1.0
     */
    public function onAfterResource()
    {
        if ($this->runtime_data->application->id == 2) {
        } else {
            return $this;
        }
return;
        $model_type = $this->runtime_data->route->model_type;
        $model_name = $this->runtime_data->route->model_name;

        $controller = $this->resources->get(
            'query:///' . $model_type . '/' . $model_name,
            array('Runtimedata', $this->runtime_data)
        );

        $controller->setModelRegistry('check_view_level_access', 0);
        $controller->setModelRegistry('process_events', 1);
        $controller->setModelRegistry('query_object', 'item');
        $controller->setModelRegistry('primary_key_value', (int)$this->query_results->created_by);
        $controller->setModelRegistry('get_customfields', 1);
        $controller->setModelRegistry('use_special_joins', 1);
        $controller->setModelRegistry('get_item_children', 0);

        try {
            $this->runtime_data->author = $controller->getData();
        } catch (Exception $e) {
            throw new PluginException ($e->getMessage());
        }

        $primary_prefix = $controller->getModelRegistry('primary_prefix', 'a');
        $primary_key    = $controller->getModelRegistry('primary_key', 'id');

        $controller->model->query->where(
            $controller->model->database->qn($primary_prefix)
            . '.'
            . $controller->model->database->qn('featured')
            . ' = 1 '
        );

        $this->runtime_data->featured = $controller->getData();


        $model_registry = ucfirst(strtolower($model_name)) . ucfirst(strtolower($model_type));

        $extended_literal    = ' (' . $this->language_controller->translate('extended') . ')';
        $parameter_literal   = ' (' . $this->language_controller->translate('parameter') . ')';
        $customfield_literal = ' (' . $this->language_controller->translate('customfield') . ')';
        $metadata_literal    = ' (' . $this->language_controller->translate('metadata') . ')';

        $fieldArray    = array();
        $standardArray = array();

        $normalFields = $this->get('Fields', array());

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

        $joins = $this->get('joins', array());

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

//$exists = $this->registry->exists($model_registry, 'customfieldgroups');
        $customfields     = $this->get('customfields', array());
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

        $runtime_data      = $this->get(strtolower('Parameters'), array());
        $runtime_dataArray = array();
        if (count($runtime_data) > 0) {
            foreach ($runtime_data as $field) {
                $temp_row        = new \stdClass();
                $temp_row->id    = 'parameter' . '_' . $field['name'];
                $temp_row->value = $field['name'] . $parameter_literal;

                $fieldArray[]        = $temp_row;
                $runtime_dataArray[] = $temp_row;
            }
        }

        $metadata = $this->get('metadata', array());
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
        asort($runtime_dataArray);
        asort($customFieldArray);

        $this->runtime_data->plugin_data->datalists->fields         = $fieldArray;
        $this->runtime_data->plugin_data->datalists->fieldsstandard = $standardArray;
        $this->runtime_data->plugin_data->datalists->metadata       = $metadataArray;
        $fieldname                                     = $model_registry . 'Fieldsruntime_data';
        $this->runtime_data->plugin_data->datalists->$fieldname     = $runtime_dataArray;
        $fieldname                                     = $model_registry . 'Fieldscustom';
        $this->runtime_data->plugin_data->datalists->$fieldname     = $customFieldArray;

        return $this;
    }
}
