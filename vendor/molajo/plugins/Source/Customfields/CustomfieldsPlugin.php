<?php
/**
 * Customfields Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Customfields;

use CommonApi\Event\ReadInterface;
use stdClass;

/**
 * Customfields Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
class CustomfieldsPlugin extends CustomfieldGroupValue implements ReadInterface
{
    /**
     * Prepares formatted copyright statement with year span
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterRead()
    {
        if ($this->processCustomfieldsPlugin() === false) {
            return $this;
        }

        return $this->processCustomfieldGroups();
    }

    /**
     * Process Customfield Groups
     *
     * @return  $this
     * @since   1.0.0
     */
    public function processCustomfieldGroups()
    {
        foreach ($this->model_registry['customfieldgroups'] as $group) {

            $content_data = $this->setContentCustomfieldGroup($group);

            if ($content_data === false) {
            } else {
                $this->row->$group = $this->processCustomfieldGroup($group, $content_data);
            }
        }

        return $this;
    }

    /**
     * Process Customfield Group
     *
     * @param   string $group
     * @param   object $content_data
     *
     * @return  stdClass
     * @since   1.0.0
     */
    protected function processCustomfieldGroup($group, $content_data)
    {
        $page_type        = $this->setPageType();
        $extension_data   = $this->setExtensionCustomfieldGroup($group);
        $application_data = $this->setApplicationCustomfieldGroup($group);
        $group_data       = $this->processCustomfieldGroupLoop(
            $group,
            $page_type,
            $content_data,
            $extension_data,
            $application_data
        );

        return $this->setCustomfieldGroupValues($group_data);
    }

    /**
     * Process Pagetype data for Customfield Group
     *
     * @param   string $group
     * @param   string $page_type
     * @param   object $content_data
     * @param   object $extension_data
     * @param   object $application_data
     *
     * @return  array
     * @since   1.0.0
     */
    protected function processCustomfieldGroupLoop(
        $group,
        $page_type,
        $content_data,
        $extension_data,
        $application_data
    ) {
        $group_data = array();

        foreach ($this->model_registry[$group] as $custom_fields) {

            $group_data = $this->processCustomfieldGroupLoopItem(
                $page_type,
                $content_data,
                $extension_data,
                $application_data,
                $custom_fields,
                $group_data
            );
        }

        return $group_data;
    }

    /**
     * Set value for a single Custom Field element
     *
     * @param   string $page_type
     * @param   object $content_data
     * @param   object $extension_data
     * @param   object $application_data
     * @param   array  $custom_fields
     * @param   array  $group_data
     * @return  mixed
     * @since   1.0.0
     */
    protected function processCustomfieldGroupLoopItem(
        $page_type,
        $content_data,
        $extension_data,
        $application_data,
        $custom_fields,
        $group_data
    ) {
        $key = $custom_fields['name'];

        $value = $this->setCustomfieldValue(
            $key,
            $page_type,
            $content_data,
            $extension_data,
            $application_data,
            $custom_fields
        );

        $group_data[$key] = $this->filter($key, $value, $custom_fields['data_type'], array());

        return $group_data;
    }

    /**
     * Process Pagetype data for Customfield Group
     *
     * @param   array $group_data
     *
     * @return  stdClass
     * @since   1.0.0
     */
    protected function setCustomfieldGroupValues($group_data)
    {
        ksort($group_data);

        $group_name = new stdClass();
        foreach ($group_data as $key => $value) {
            $group_name->$key = $value;
        }

        return $group_name;
    }

    /**
     * Set Page Type
     *
     * @return  string
     * @since   1.0.0
     */
    protected function setPageType()
    {
        if (isset($this->runtime_data->route->page_type)) {
            $page_type = strtolower($this->runtime_data->route->page_type);
        } else {
            $page_type = '';
        }

        if ($page_type === 'edit' || $page_type === 'new') {
            $page_type = 'form';
        }

        return $page_type;
    }

    /**
     * Process the group content to decode (if JSON) or bypass (if already processed and an array)
     *
     * @param   string $group
     *
     * @return  mixed
     * @since   1.0.0
     */
    public function setContentCustomfieldGroup($group)
    {
        return $this->getCustomfieldGroupData($group);
    }

    /**
     * Get Extension Customfield Group
     *
     * @param   string $group
     *
     * @return  mixed
     * @since   1.0.0
     */
    protected function setExtensionCustomfieldGroup($group)
    {
        return $this->getCustomfieldGroupData('extension_instances_' . $group);
    }

    /**
     * Get Application Customfield Group Data
     *
     * @param   string $group
     *
     * @return  object
     * @since   1.0.0
     */
    protected function setApplicationCustomfieldGroup($group)
    {
        if (isset($this->runtime_data->application->$group)) {
            return $this->runtime_data->application->$group;
        }

        return new stdClass();
    }

    /**
     * Process the group content to decode (if JSON) or bypass (if already processed and an array)
     *
     * @param   string $group
     *
     * @return  mixed
     * @since   1.0.0
     */
    public function getCustomfieldGroupData($group)
    {
        if (isset($this->row->$group)) {
        } else {
            return false;
        }

        if (is_array($this->row->$group)) {
            return false;
        }

        $content_data = json_decode($this->row->$group);

        return $this->setCustomfieldContentForApplication($content_data);
    }

    /**
     * Process Customfield Group for Application
     *
     * @param   array $content_data
     *
     * @return  mixed
     * @since   1.0.0
     */
    protected function setCustomfieldContentForApplication($content_data = array())
    {
        if (isset($this->runtime_data->application->id)) {
        } else {
            return $content_data;
        }

        foreach ($content_data as $key => $value) {
            if ($key === $this->runtime_data->application->id) {
                $content_data = $value;
                break;
            }
        }

        return $content_data;
    }
}
