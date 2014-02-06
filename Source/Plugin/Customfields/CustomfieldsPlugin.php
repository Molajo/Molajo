<?php
/**
 * Customfields Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugin\Customfields;

use CommonApi\Event\ReadInterface;
use Molajo\Plugin\ReadEventPlugin;
use stdClass;

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
     * Post-read processing - one row at a time
     *
     * @return  $this
     * @since   1.0
     */
    public function onAfterRead()
    {
        if (count($this->row) === 0) {
            return $this;
        }

        if (isset($this->model_registry['query_object'])
            && isset($this->model_registry['get_customfields'])
        ) {
        } else {
            return $this;
        }

        if ($this->model_registry['query_object'] == 'result') {
            return $this;
        }

        if ((int)$this->model_registry['get_customfields'] == 0) {
            return $this;
        }

        $customfieldgroups = $this->model_registry['customfieldgroups'];

        if (is_array($customfieldgroups) && count($customfieldgroups) > 0) {
        } else {
            return $this;
        }

        foreach ($customfieldgroups as $group) {
            $this->row->$group = $this->processCustomfieldGroup($group);
        }

        return $this;
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
        /** Page Type */
        if (isset($this->runtime_data->route->page_type)) {
            $page_type = strtolower($this->runtime_data->route->page_type);
        } else {
            $page_type = '';
        }

        /** Standard Data */
        if (isset($this->row->$group)) {
        } else {
            return new stdClass();
        }

        if (is_object($this->row->$group)) {
            return $this->row->$group;
        }

        $standard_custom_field_data = json_decode($this->row->$group);

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

        if (isset($this->row->$x)) {

            $extension_instances_field_data = json_decode($this->row->$x);
            unset($this->row->$x);

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

        if ($page_type == 'new' || $page_type == 'edit') {
            $page_type = 'form';
        }

        $temp = array();

        foreach ($this->model_registry[$group] as $customfields) {

            $key        = $customfields['name'];
            $target_key = $key;
            $test       = substr($key, 0, strlen($page_type));
            $use        = true;

            if ((strlen($page_type) > 0)) {

                if ($test == $page_type) {
                    if ($page_type == 'item' || $page_type == 'form' || $page_type == 'list') {

                        if (substr($key, 0, strlen($page_type) + 1) == $page_type . '_') {
                            $target_key = substr($key, strlen($page_type) + 1, 9999);

                        } else {
                            $use = true;
                        }
                    }

                } elseif (substr($key, 0, strlen('menuitem_')) == 'menuitem_') {

                    if ($page_type == 'item' || $page_type == 'form' || $page_type == 'list') {
                        $use = false;
                    } else {
                        $target_key = substr($key, strlen('menuitem_'), 9999);
                    }

                } else {
                    $use = true;
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

                $temp[$target_key] = $this->filter($key, $value, $filter = null, $filter_options);
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
