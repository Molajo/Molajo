<?php
/**
 * Date Formats
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Dateformats;

use CommonApi\Event\CreateInterface;
use Molajo\Plugins\CreateEventPlugin;

/**
 * Date Formats
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
class DateformatsPlugin extends CreateEventPlugin implements CreateInterface
{
    /**
     * Fields
     *
     * @var    array
     * @since  1.0.0
     */
    protected $fields = array();

    /**
     * Pre-create processing
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeCreate()
    {
        if (is_object($this->date_controller)) {
        } else {
            return $this;
        }

        $fields = $this->getFieldsByType('datetime');

        $this->processFieldsByType($fields, 'handleDateFormat');

        return $this;
    }

    /**
     * Handle Date Format
     *
     * @param   object $field
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function handleDateFormat($field)
    {
        if ($field->value === false || $field->Value === '0000-00-00 00:00:00') {
        }

        if ($field->name === 'created_datetime') {
        }
        if ($field->name === 'modified_datetime') {
        }
        if ($field->name === 'activity_datetime') {
        }

        return $field;
    }

    /**
     * Add Create By Name
     *
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function addCreateByName()
    {
        /** $createdByField = $this->getField('created_by');
         * $createdByValue = $this->getFieldValue($createdByField);
         * if ($createdByValue === false) {
         * $this->setField(
         * $createdByField,
         * 'created_by',
         * $this->runtime_data->user->get('id')
         * );
         * } */
    }

    /**
     * Add Changed By Name
     *
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function addChangedByName()
    {

    }

    /**
     * Prepares formatted copyright statement with year span
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterRead()
    {
        if ($this->processReadDates() === false) {
            return $this;
        }

        return $this->processAfterRead();
    }

    /**
     * Should plugin be executed?
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function processReadDates()
    {
        if (is_object($this->date_controller)) {
        } else {
            return false;
        }

        $this->fields = $this->getFieldsByType('datetime');

        if (count($this->fields) === 0) {
            return false;
        }

        return true;
    }

    /**
     * After-read processing
     *
     * Adds formatted dates to 'normal' or special fields recordset
     *
     * @return  $this
     * @since   1.0.0
     */
    public function processAfterRead()
    {
        foreach ($this->fields as $field) {
            $this->processDateField($field);
        }

        return $this;
    }

    /**
     * Process a single date field
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function processDateField($field)
    {
        $name        = $field['name'];
        $field_value = $this->getFieldValue($field);

        if ($this->useField($name, $field_value) === false) {
            return $this;
        }

        $ccyymmdd = $this->date_controller->convertCCYYMMDD($field_value);

        $this->setField($field, $name . '_pretty_date', $this->date_controller->getPrettyDate($field_value));
        $this->setField($field, $name . '_time', substr($field_value, 11, 8));

        $this->setDateParts($field, $name, $ccyymmdd, $field_value);
        $this->setMonthParts($field, $name, $ccyymmdd);
        $this->setDayParts($field, $name, $ccyymmdd);

        return $this;
    }

    /**
     * Process a single date field
     *
     * @param   string $name
     * @param   string $value
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function useField($name, $value)
    {
        $invalid_values = array(null, false, '0000-00-00 00:00:00', '');
        if (in_array($value, $invalid_values)) {
            return false;
        }

        if (substr($name, 0, 12) === 'list_select_') {
            return false;
        }

        return true;
    }

    /**
     * Set Date Parts
     *
     * @param   object $field
     * @param   string $name
     * @param   string $ccyymmdd
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setDateParts($field, $name, $ccyymmdd, $field_value)
    {
        $dash = $this->date_controller->convertCCYYdashMMdashDD($field_value);

        $this->setField($field, $name . '_ccyymmdd', $ccyymmdd);
        $this->setField($field, $name . '_ccyy', substr($ccyymmdd, 0, 4));
        $this->setField($field, $name . '_mm', substr($ccyymmdd, 4, 2));
        $this->setField($field, $name . '_dd', substr($ccyymmdd, 6, 2));
        $this->setField($field, $name . '_ccyy_mm_dd', $dash);

        return $this;
    }

    /**
     * Set Month Parts
     *
     * @param   object $field
     * @param   string $name
     * @param   string $ccyymmdd
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setMonthParts($field, $name, $ccyymmdd)
    {
        $this->setField(
            $field,
            $name . '_month_name_abbr',
            $this->date_controller->getMonthName((int)substr($ccyymmdd, 4, 2), true)
        );

        $this->setField(
            $field,
            $name . '_month_name',
            $this->date_controller->getMonthName((int)substr($ccyymmdd, 4, 2), false)
        );

        return $this;
    }

    /**
     * Set Day Parts
     *
     * @param   object $field
     * @param   string $name
     * @param   string $ccyymmdd
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setDayParts($field, $name, $ccyymmdd)
    {
        $this->setField($field, $name . '_n_days_ago', $ccyymmdd);

        $this->setField(
            $field,
            $name . '_day_number',
            $this->date_controller->getDayNumber($ccyymmdd)
        );

        $this->setField(
            $field,
            $name . '_day_name_abbr',
            $this->date_controller->getDayName($ccyymmdd, true)
        );

        $this->setField(
            $field,
            $name . '_day_name',
            $this->date_controller->getDayName($ccyymmdd, false)
        );

        return $this;
    }
}
