<?php
/**
 * Customfields Plugin
 *
 * @package    Molajo
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugin\Customfields;

use stdClass;
use CommonApi\Exception\RuntimeException;
use CommonApi\Event\ReadInterface;
use Molajo\Plugin\ReadEventPlugin;

/**
 * Customfields Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
class CustomfieldsPlugin extends ReadEventPlugin implements ReadInterface
{
    /**
     * Model Registry
     *
     * @var    object
     * @since  1.0
     */
    protected $model_registry = null;

    /**
     * Source Data containing the custom fields
     *
     * @var    object
     * @since  1.0
     */
    protected $data = null;

    /**
     * runtime_data
     *
     * @var    object
     * @since  1.0
     */
    protected $runtime_data = null;

    /**
     * Type
     *
     * @var    string
     * @since  1.0
     */
    protected $page_type = 'list';

    /**
     * Post-read processing - one row at a time
     *
     * @return  $this
     * @since   1.0
     */
    public function onAfterRead()
    {
        if (count($this->query_results) > 0
            && $this->model_registry['query_object'] != 'result'
            && $this->model_registry['get_customfields'] == 1) {
        } else {
            return $this;
        }

        $customfieldgroups = $this->model_registry['customfieldgroups'];
        if (is_array($customfieldgroups) && count($customfieldgroups) > 0) {
        } else {
            return array();
        }

        $customfields = array();
        foreach ($customfieldgroups as $group) {
            $customfields[$group] = $this->processCustomfieldGroup($group);
        }

        $this->model_registry = null;
        $this->data           = null;

        return $customfields;
    }

    /**
     * Process Customfield Group
     *
     * @param   string $group
     *
     * @return  mixed
     * @since   1.0
     */
    protected function processCustomfieldGroup($group)
    {
        /** Standard Data */
        if (isset($this->data->$group)) {
        } else {
            return array();
        }
        $standard_custom_field_data = json_decode($this->data->$group);

        if (is_array($standard_custom_field_data) > 0
            && isset($this->runtime_data->application->id)
        ) {

            foreach ($standard_custom_field_data as $key => $value) {
                if ($key == $this->runtime_data->application->id) {
                    $standard_custom_field_data = $value;
                    break;
                }
            }
        }

        /** Extension Instances Data */
        $x = 'extension_instances_' . $group;

        if (isset($this->data->$x)) {
            $extension_instances_field_data = json_decode($this->data->$x);

            if (is_array($extension_instances_field_data)
                && isset($this->runtime_data->application->id)
            ) {
                foreach ($extension_instances_field_data as $key => $value) {
                    $id = $this->runtime_data->application->id;

                    if (isset($value->$id)) {
                        $extension_instances_field_data = $value->$id;
                        break;
                    }
                }
            }
        } else {
            $extension_instances_field_data = null;
        }

        /** Application Data */
        if (isset($this->runtime_data->application)
            && isset($this->runtime_data->application->$group)
        ) {
            $application = $this->runtime_data->application->$group;
        } else {
            $application = new stdClass();
        }

        $temp = array();

        foreach ($this->model_registry[$group] as $customfields) {

            $key        = $customfields['name'];
            $target_key = $key;
            $test       = substr($key, 0, strlen($this->page_type));
            $use        = true;

            if ((strlen($this->page_type) > 0)) {

                if ($test == $this->page_type) {
                    if ($this->page_type == 'item' || $this->page_type == 'form' || $this->page_type == 'list') {
                        if (substr($key, 0, strlen($this->page_type) + 1) == $this->page_type . '_') {
                            $target_key = substr($key, strlen($this->page_type) + 1, 9999);
                        } else {
                            $use = false;
                        }
                    }

                } elseif (substr($key, 0, strlen('menuitem_')) == 'menuitem_') {

                    if ($this->page_type == 'item' || $this->page_type == 'form' || $this->page_type == 'list') {
                        $use = false;
                    } else {
                        $target_key = substr($key, strlen('menuitem_'), 9999);
                    }
                }
            }

            if ($use === true) {

                $value = null;

                if (isset($standard_custom_field_data->$key)) {
                    $value = $standard_custom_field_data->$key;
                }

                if (($value === null || $value == '' || $value == ' ')
                    && isset($extension_instances_field_data->$key)
                ) {
                    $value = $extension_instances_field_data->$key;
                }

                if (($value === null || $value == '' || $value == ' ' || $value == 0)
                    && isset($application->$key)
                ) {
                    $value = $application->$key;
                }

                if (($value === null || $value == '' || $value == ' ')
                    && isset($application->$target_key)
                ) {
                    $value = $application->$target_key;
                }

                if ($value === null || $value == '' || $value == ' ') {
                    if (isset($customfields['default'])) {
                        $default = $customfields['default'];
                    } else {
                        $default = false;
                    }
                    $value = $default;
                }

                $filter_options              = array();
                $filter_options['data_type'] = $customfields['type'];

                $temp[$target_key] = $this->filter($key, $value, $filter, $filter_options);
            }
        }

        ksort($temp);

        $group_name = new stdClass();
        foreach ($temp as $key => $value) {
            $group_name->$key = $value;
        }

        return $group_name;
    }
}
