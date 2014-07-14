<?php
/**
 * Fields Base
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Fields;

use stdClass;

/**
 * Standard Fields Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
abstract class StandardFields extends Base
{
    /**
     * Set Standard Fields
     *
     * @param   array $fields
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setStandardFields($fields)
    {
        if (count($fields) === 0) {
            return $this;
        }

        foreach ($fields as $field) {
            $this->setStandardFieldsField($field);
        }

        return $this;
    }

    /**
     * Set Standard Field
     *
     * @param   array $field
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setStandardFieldsField($field)
    {
        $row     = new stdClass();
        $row->id = $field['name'];

        if (isset($field['calculated']) && $field['calculated'] === 1) {
            $row->value = $field['name']
                . ' ('
                . $this->language_controller->translateString('Extended')
                . ')';

            $this->extended_field_array[] = $row;
        } else {
            $row->value = $field['name'];
        }

        $this->field_array[]      = $row;
        $this->all_fields_array[] = $row;

        return $this;
    }
}
