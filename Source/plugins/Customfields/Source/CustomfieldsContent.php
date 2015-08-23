<?php
/**
 * Customfields Content
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Customfields;

use CommonApi\Event\ReadEventInterface;
use stdClass;

/**
 * Customfields Content
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
abstract class CustomfieldsContent extends ProcessCustomFields implements ReadEventInterface
{
    /**
     * Get Primary Content for Custom Field Group
     *
     * @param   string $group
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setCustomfieldGroupContent($group)
    {
        $this->content_data = $this->getCustomfieldGroupContent($group);

        return $this;
    }

    /**
     * Get Extension Content for Custom Field Group
     *
     * @param   string $group
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setCustomfieldGroupContentForExtension($group)
    {
        $this->extension_data = new stdClass();

        if (strtolower($this->runtime_data->route->page_type) === 'new') {
            $this->extension_data = $this->getCustomfieldGroupContentDataNew($group);
        }

        if (count(get_object_vars($this->extension_data)) === 0) {
            $this->extension_data = $this->getCustomfieldGroupContent('extension_instances_' . $group);
        }

        return $this;
    }

    /**
     * Get Group Content
     *
     * @param   string $group
     *
     * @return  object
     * @since   1.0.0
     */
    protected function getCustomfieldGroupContent($group)
    {
        $data = new stdClass();

        if (isset($this->controller['row']->$group)) {
            $data = json_decode($this->controller['row']->$group);
            unset($this->controller['row']->$group);
        }

        return $data;
    }

    /**
     * Get Group Content - New Page Type
     *
     * @param   string $group
     *
     * @return  object
     * @since   1.0.0
     */
    protected function getCustomfieldGroupContentDataNew($group)
    {
        $data = new stdClass();

        if (count(get_object_vars($this->runtime_data->resource->data)) === 0) {
            return $data;
        }

        if ($this->controller['row']->extension_instance_id === $this->runtime_data->resource->data->extension_id) {
        } else {
            return $data;
        }

        if (isset($this->runtime_data->resource->data->$group)) {
            $data = $this->runtime_data->resource->data->$group;

        } elseif (isset($this->runtime_data->resource->$group)) {
            $data = $this->runtime_data->resource->$group;
        }

        return $data;
    }

    /**
     * Customfield Data is in an array by Application ID
     *
     * @param   array $content_data
     *
     * @return  object
     * @since   1.0.0
     */
    protected function getApplicationContent(array $content_data = array())
    {
        $data = new stdClass();

        if (isset($this->runtime_data->application->id)) {
        } else {
            return $data;
        }

        if (count($content_data) > 0) {

            foreach ($content_data as $key => $value) {
                if ($key === $this->runtime_data->application->id) {
                    $data = $value;
                    break;
                }
            }
        }

        return $data;
    }

    /**
     * Get Application Content for Custom Field Group
     *
     * @param   string $group
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setCustomfieldGroupContentForApplication($group)
    {
        $this->application_data = new stdClass();

        if (isset($this->runtime_data->application->$group)) {
            $this->application_data = clone $this->runtime_data->application->$group;
        }

        return $this;
    }

    /**
     * Set Data Element Values, first using Application, then Extension and finally Item values
     *
     * @param   string $group
     *
     * @return  array
     * @since   1.0.0
     */
    protected function setCustomfieldGroupElements($group)
    {
        $group_data = new stdClass();

        if (is_array($this->model_registry_merged[$group]) && count($this->model_registry_merged[$group]) > 0) {

            foreach ($this->model_registry_merged[$group] as $key => $custom_fields) {

                $group_data->$key = null;

                /** Application */
                if (isset($this->application_data->$key)) {
                    $group_data->$key = $this->application_data->$key;
                    unset($this->application_data->$key);
                }

                /** Resource Extension */
                if (isset($this->extension_data->$key)) {
                    if ($this->extension_data->$key === null
                        || trim($this->extension_data->$key) == ''
                        || trim($this->extension_data->$key) == '0'
                    ) {
                    } else {
                        $group_data->$key = $this->extension_data->$key;
                    }
                    unset($this->extension_data->$key);
                }

                /** Row */
                if (isset($this->content_data->$key)) {
                    if ($this->content_data->$key === null
                        || trim($this->content_data->$key) == ''
                        || trim($this->content_data->$key) == '0'
                    ) {
                    } else {
                        $group_data->$key = $this->content_data->$key;
                    }
                    unset($this->content_data->$key);
                }

                /** Default */
                if ($group_data->$key === null
                    || trim($group_data->$key) === ''
                    || trim($group_data->$key) === '0'
                ) {

                    if (isset($custom_fields['default'])) {
                        $group_data->$key = $custom_fields['default'];
                    }
                }
            }
        }

        if (in_array($this->page_type, $this->standard_page_types) === true) {
            $group_data = $this->setPageTypeValues($group_data);
        }

        return $this->sortObject($group_data);
    }

    /**
     * Overlay using Page Type Values
     *
     * @param   object $group_data
     *
     * @return  object
     * @since   1.0.0
     */
    protected function setPageTypeValues($group_data)
    {
        $test_page_type = $this->page_type . '_';
        $length         = strlen($test_page_type);

        foreach ($group_data as $key => $value) {

            if (substr($key, 0, $length) === $test_page_type) {

                if (isset($group_data->$key)) {
                    $new_key              = substr($key, $length, 999999);
                    $group_data->$new_key = $group_data->$key;
                    unset($group_data->$key);
                }
            }
        }

        return $group_data;
    }
}
