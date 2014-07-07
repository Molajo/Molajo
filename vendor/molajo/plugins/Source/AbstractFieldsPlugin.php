<?php
/**
 * Abstract Plugin
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 */
namespace Molajo\Plugins;

use stdClass;

/**
 * Abstract Plugin - Overrides Abstract Plugin in Event Package
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
abstract class AbstractFieldsPlugin extends AbstractPlugin
{
    /**
     * Text Fields
     *
     * @var    array
     * @since  1.0.0
     */
    protected $hold_fields;

    /**
     * Are fields available
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function countGetFields($type = 'text')
    {
        $this->hold_fields = $this->getFieldsByType($type);
        if (count($this->hold_fields) === 0) {
            return false;
        }

        return true;
    }

    /**
     * Get Field Definition for specific Field Name
     *
     * @param   string     $name
     * @param   null|mixed $default
     *
     * @return  mixed
     * @since   1.0.0
     */
    protected function getField($name, $default = null)
    {
        if (isset($this->model_registry->field->$name)) {
        } else {
            $this->model_registry->field->$name = $default;
        }

        return $this->row->$name;
    }

    /**
     * Retrieve Fields for a specified Data Type
     *
     * @param   string $type
     *
     * @return  array
     * @since   1.0.0
     */
    protected function getFieldsByType($type)
    {
        $results = array();

        if (isset($this->model_registry['fields'])) {
        } else {
            return array();
        }

        foreach ($this->model_registry['fields'] as $field) {
            if ($field['type'] === $type) {
                $results[] = $field;
            }
        }

        return $results;
    }

    /**
     * Loop thru fields and process fields with value by named method
     *
     * @param   array  $fields
     * @param   string $method
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function processFieldsByType(array $fields, $method)
    {
        if (count($fields) === 0) {
            return $this;
        }

        foreach ($fields as $field) {
            $this->processFieldByType($field, $method);
        }

        return $this;
    }

    /**
     * Loop thru fields and process fields with value by named method
     *
     * @param   object $field
     * @param   string $method
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function processFieldByType($field, $method = '')
    {
        if ($method === '') {
        } else {
            $field = $this->$method($field);
        }

        $field_value = $this->getFieldValue($field);

        if ($field_value === null) {
        } else {
            $this->setField($field, $field->name, $field_value);
        }

        return $this;
    }

    /**
     * getFieldValue retrieves the actual field value from the 'normal' or special field
     *
     * @param   object $field
     *
     * @return  mixed
     * @since   1.0.0
     */
    protected function getFieldValue($field)
    {
        $name = $this->getFieldValueAsName($field);

        if ($name === '') {
            $name = $field['name'];
        }

        if (isset($this->row->$name)) {
            return $this->row->$name;
        }

        return $this->getFieldValueDefault($field);
    }

    /**
     * Retrieves the actual field value matching 'as_name'
     *
     * @param   object $field
     *
     * @return  string
     * @since   1.0.0
     */
    protected function getFieldValueAsName($field)
    {
        $name = '';

        if (isset($field['as_name'])) {
            if ($field['as_name'] === '') {
            } else {
                $name = $field['as_name'];
            }
        }

        return $name;
    }

    /**
     * Retrieves default value for field
     *
     * @param   object $field
     *
     * @return  mixed
     * @since   1.0.0
     */
    protected function getFieldValueDefault($field)
    {
        if (isset($field['default'])) {
            return $field['default'];
        }

        return null;
    }

    /**
     * setField adds a field to the 'normal' or special field group
     *
     * @param   string $field
     * @param   string $new_field_name
     * @param   mixed  $value
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setField($field, $new_field_name, $value)
    {
        $this->setObject('row');

        $this->row->$new_field_name = $value;

        $this->addFieldModelRegistry($field, $new_field_name);

        return $this;
    }

    /**
     * Verify object or create object
     *
     * @param   string $field
     * @param   string $new_field_name
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function addFieldModelRegistry($field, $new_field_name)
    {
        $this->setArray('model_registry');
        $this->setArrayMember('model_registry', 'fields');

        if ($this->verifyModelRegistryFieldExists($new_field_name) === true) {
            return $this;
        }

        $temp                                            = $field;
        $temp['name']                                    = $new_field_name;
        $temp['calculated']                              = 1;
        $this->model_registry['fields'][$new_field_name] = $temp;

        return $this;
    }

    /**
     * Verify object or create object
     *
     * @param   string $name
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function verifyModelRegistryFieldExists($name)
    {
        foreach ($this->model_registry['fields'] as $field) {
            if ($field['type'] === $name) {
                return true;
            }
        }

        return false;
    }

    /**
     * saveForeignKeyValue
     *
     * @param   $new_field_name
     * @param   $value
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function saveForeignKeyValue($new_field_name, $value)
    {
        if (is_object($this->row)) {
        } else {
            $this->row = new stdClass();
        }

        if (isset($this->row->$new_field_name)) {
            return $this;
        }

        $this->row->$new_field_name = $value;

        return $this;
    }
}
