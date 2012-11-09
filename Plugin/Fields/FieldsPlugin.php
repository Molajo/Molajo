<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Fields;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;
use Molajo\Helpers;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class FieldsPlugin extends Plugin
{
    /**
     * Generates list of Datalists for use in defining Custom Fields of Type Selectlist
     *
     * This can be moved to onBeforeParse when Plugin ordering is in place
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterRoute()
    {
        if (APPLICATION_ID == 2) {
        } else {
            return true;
        }

        $model_name = Services::Registry()->get('Parameters', 'model_name');
        $model_type = Services::Registry()->get('Parameters', 'model_type');

        $table_registry_name = ucfirst(strtolower($model_name)) . ucfirst(strtolower($model_type));

        if (Services::Registry()->exists($table_registry_name) === true) {
        } else {
            Helpers::Content()->getResourceContentParameters($model_type, $model_name);
        }

        $primary_prefix = Services::Registry()->get($table_registry_name, 'primary_prefix');

        $fieldArray = array();
		$normalFieldArray = array();

        $normalFields = Services::Registry()->get($table_registry_name, 'fields');


        $status = 0;

        if (count($normalFields) > 0) {

            foreach ($normalFields as $field) {

                $row = new \stdClass();
				$row->id = $field['name'];
                $row->value = $field['name'];

                $normalFieldArray[] = $row;
				$fieldArray[] = $row;

                if ($field['name'] == 'status') {
                    $status = 1;
                }

                if ($field['type'] == 'datetime') {
					$row = new \stdClass();
					$row->id = $field['name'] . '_n_days_ago';
					$row->value = $field['name'] . '_n_days_ago';
					$fieldArray[] = $row;

					$row = new \stdClass();
					$row->id = $field['name'] . '_ccyy';
					$row->value = $field['name'] . '_ccyy';
					$fieldArray[] = $row;

					$row = new \stdClass();
					$row->id = $field['name'] . '_mm';
					$row->value = $field['name'] . '_mm';
					$fieldArray[] = $row;

					$row = new \stdClass();
					$row->id = $field['name'] . '_dd';
					$row->value = $field['name'] . '_dd';
					$fieldArray[] = $row;

                    $row = new \stdClass();
                    $row->id = $field['name'] . '_ccyy_mm_dd';
                    $row->value = $field['name'] . '_ccyy_mm_dd';
                    $fieldArray[] = $row;

					$row = new \stdClass();
					$row->id = $field['name'] . '_month_name_abbr';
					$row->value = $field['name'] . '_month_name_abbr';
					$fieldArray[] = $row;

                    $row = new \stdClass();
					$row->id = $field['name'] . '_month_name';
                    $row->value = $field['name'] . '_month_name';
                    $fieldArray[] = $row;

                    $row = new \stdClass();
					$row->id = $field['name'] . '_time';
                    $row->value = $field['name'] . '_time';
                    $fieldArray[] = $row;

                    $row = new \stdClass();
					$row->id = $field['name'] . '_day_number';
                    $row->value = $field['name'] . '_day_number';
                    $fieldArray[] = $row;

					$row = new \stdClass();
					$row->id = $field['name'] . '_day_name_abbr';
					$row->value = $field['name'] . '_day_name_abbr';
					$fieldArray[] = $row;

					$row = new \stdClass();
					$row->id = $field['name'] . '_day_name';
					$row->value = $field['name'] . '_day_name';
					$fieldArray[] = $row;
                }

                if ($field['type'] == 'text') {
                    $row = new \stdClass();
					$row->id = $field['name'] . '_introductory';
                    $row->value = $field['name'] . '_introductory';
                    $fieldArray[] = $row;

                    $row = new \stdClass();
					$row->id = $field['name'] . '_fulltext';
                    $row->value = $field['name'] . '_fulltext';
                    $fieldArray[] = $row;

                    $row = new \stdClass();
					$row->id = $field['name'] . '_snippet';
                    $row->value = $field['name'] . '_snippet';
                    $fieldArray[] = $row;
                }

            }
        }

        if ($status == 0) {
        } else {
            $row = new \stdClass();
			$row->id = 'status_name';
            $row->value = 'status_name';

            $fieldArray[] = $row;
        }

        $joins = Services::Registry()->get($table_registry_name, 'joins');

        if (count($joins) > 0) {
            foreach ($joins as $field) {
                $temp = explode(',', $field['select']);
                if (count($temp) > 0) {
                    foreach ($temp as $f) {
                        if (trim($f) == '') {
                        } else {
                            $row = new \stdClass();
							$row->id = $field['alias'] . '_' . $f;
                            $row->value =  $field['alias'] . '_' . $f;

                            $fieldArray[] = $row;
                        }
                    }
                }
            }
        }

        $customfields = Services::Registry()->get($table_registry_name, 'Customfields');
        if (count($customfields) > 0) {
            foreach ($customfields as $field) {
                $row = new \stdClass();
				$row->id = 'customfield' . '_' . $field['name'];
                $row->value = $field['name'] . ' (customfield)';

                $fieldArray[] = $row;
            }
        }

        $metadata = Services::Registry()->get($table_registry_name, 'Metadata');
        if (count($metadata) > 0) {
            foreach ($metadata as $field) {
                $row = new \stdClass();
				$row->id = 'metadata' . '_' . $field['name'];
                $row->value = 'metadata' . '_' . $field['name'];

                $fieldArray[] = $row;
            }
        }

        asort($fieldArray);
		asort($normalFieldArray);

        Services::Registry()->set('Datalist', $table_registry_name . 'Fields', $fieldArray);
		Services::Registry()->set('Datalist', $table_registry_name . 'Fieldsstandard', $normalFieldArray);

        return true;
    }
}
