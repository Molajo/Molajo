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

/**
 * Customfields Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
class CustomfieldGroupValue extends ProcessCustomFields implements ReadInterface
{
    /**
     * Set Customfield Value
     *
     * @param   string $key
     * @param   string $page_type
     * @param   object $content_data
     * @param   object $extension_data
     * @param   object $application_data
     * @param   array  $custom_fields
     *
     * @return  mixed
     * @since   1.0.0
     */
    protected function setCustomfieldValue(
        $key,
        $page_type,
        $content_data,
        $extension_data,
        $application_data,
        $custom_fields
    ) {
        $target_key = $this->processCustomfieldGroupPageType($page_type, $key);

        $results = $this->setCustomfieldRealValue($key, $target_key, $content_data, $extension_data, $application_data);
        if ($results === null) {
        } else {
            return $results;
        }

        return $this->setCustomfieldValueDefault($custom_fields);
    }

    /**
     * Set Customfield Value
     *
     * @param   string $key
     * @param   string $target_key
     * @param   object $content_data
     * @param   object $extension_data
     * @param   object $application_data
     *
     * @return  mixed
     * @since   1.0.0
     */
    protected function setCustomfieldRealValue(
        $key,
        $target_key,
        $content_data,
        $extension_data,
        $application_data
    ) {
        $results = $this->setCustomfieldValueKey($key, $content_data, $extension_data, $application_data);
        if ($results === null) {
        } else {
            return $results;
        }

        $results = $this->setCustomfieldValueTargetKey($target_key, $application_data);
        if ($results === null) {
        } else {
            return $results;
        }

        return null;
    }

    /**
     * Set Customfield Value for Key Content, Extension or Application Data
     *
     * @param   string $key
     * @param   object $content_data
     * @param   object $extension_data
     * @param   object $application_data
     *
     * @return  mixed
     * @since   1.0.0
     */
    protected function setCustomfieldValueKey(
        $key,
        $content_data,
        $extension_data,
        $application_data
    ) {
        if (isset($content_data->$key)) {
            return $content_data->$key;
        }

        if (isset($extension_data->$key)) {
            return $extension_data->$key;
        }

        if (isset($application_data->$key)) {
            return $application_data->$key;
        }

        return null;
    }

    /**
     * Set Customfield Value for Application Target Key
     *
     * @param   object $application_data
     * @param string $target_key
     *
     * @return  mixed
     * @since   1.0.0
     */
    protected function setCustomfieldValueTargetKey(
        $target_key,
        $application_data
    ) {
        if (isset($application_data->$target_key)) {
            return $application_data->$target_key;
        }

        return null;
    }

    /**
     * Set Customfield Value using Model Registry Default
     *
     * @param   array $custom_fields
     *
     * @return  mixed
     * @since   1.0.0
     */
    protected function setCustomfieldValueDefault($custom_fields)
    {
        if (isset($custom_fields['default'])) {
            return $custom_fields['default'];
        }

        return null;
    }

    /**
     * Process Pagetype data for Customfield Group
     *
     * @param   string $page_type
     * @param   string $key
     *
     * @return  string
     * @since   1.0.0
     */
    protected function processCustomfieldGroupPageType($page_type, $key)
    {
        $test = substr($key, 0, strlen($page_type));

        if (($test === $page_type)) {
            return $this->processCustomfieldGroupPageTypeFound($page_type, $key);
        }

        return $this->processCustomfieldGroupPageTypeMenu($key);
    }

    /**
     * Set Key for Item Form or List
     *
     * @param   string $page_type
     * @param   string $key
     *
     * @return  string
     * @since   1.0.0
     */
    protected function processCustomfieldGroupPageTypeFound($page_type, $key)
    {
        $page_type_array = array('item', 'form', 'list');

        if (in_array($page_type, $page_type_array)) {
            if (substr($key, 0, strlen($page_type) + 1) === $page_type . '_') {
                return substr($key, strlen($page_type) + 1, 9999);
            }
        }

        return $key;
    }

    /**
     * Set Key for MenuItem
     *
     * @param   string $key
     *
     * @return  string
     * @since   1.0.0
     */
    protected function processCustomfieldGroupPageTypeMenu($key)
    {
        if (substr($key, 0, strlen('menuitem_')) === 'menuitem_') {
            return substr($key, strlen('menuitem_'), 9999);
        }

        return $key;
    }
}
