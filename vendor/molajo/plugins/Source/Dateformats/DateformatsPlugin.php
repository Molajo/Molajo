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
use Exception;
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
     * @param   object  $field
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
        $createdByValue = $this->getFieldValue($createdByField);
        if ($createdByValue === false) {
            $this->setField(
                $createdByField,
                'created_by',
                $this->runtime_data->user->get('id')
            );
        } */
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
     * After-read processing
     *
     * Adds formatted dates to 'normal' or special fields recordset
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterRead()
    {
        if (is_object($this->date_controller)) {
        } else {
            return $this;
        }

        $fields = $this->getFieldsByType('datetime');

        try {
            $this->date_controller->convertCCYYMMDD('2011-11-11');
            /** Skip when Date Controller is not available (likely startup) */
        } catch (Exception $e) {
            return $this;
        }

        if (count($fields) > 0) {

            foreach ($fields as $field) {

                $name = $field['name'];

                /** Retrieves the actual field value from the 'normal' or special field */
                $field_value = $this->getFieldValue($field);

                if ($field_value === false
                    || $field_value === '0000-00-00 00:00:00'
                    || $field_value === ''
                    || $field_value === null
                    || substr($name, 0, 12) === 'list_select_'
                ) {
                } else {

                    $newFieldValue = $this->date_controller->convertCCYYMMDD($field_value);

                    if ($newFieldValue === false) {
                        $ccyymmdd = false;
                    } else {

                        /** Creates the new 'normal' or special field and populates the value */
                        $ccyymmdd = $newFieldValue;
                        $new_name = $name . '_ccyymmdd';
                        $this->setField($field, $new_name, $newFieldValue);
                        $field_value = $newFieldValue;
                    }

                    /** Using newly formatted date, calculate NN days ago */
                    $newFieldValue = $this->date_controller->getNumberofDaysAgo($field_value);

                    if ($newFieldValue === false) {
                        $this->setField($field, $name . '_n_days_ago', null);
                        $this->setField($field, $name . '_ccyy', null);
                        $this->setField($field, $name . '_mm', null);
                        $this->setField($field, $name . '_dd', null);
                        $this->setField($field, $name . '_month_name_abbr', null);
                        $this->setField($field, $name . '_month_name', null);
                        $this->setField($field, $name . '_time', null);
                        $this->setField($field, $name . '_day_number', null);
                        $this->setField($field, $name . '_day_name_abbr', null);
                        $this->setField($field, $name . '_day_name', null);
                    } else {

                        /** Creates the new 'normal' or special field and populates the value */
                        $new_name = $name . '_n_days_ago';
                        $this->setField($field, $new_name, $newFieldValue);
                    }

                    /** Pretty Date */
                    $newFieldValue = $this->date_controller->getPrettyDate($field_value);

                    if ($newFieldValue === false) {
                    } else {

                        /** Creates the new 'normal' or special field and populates the value */
                        $new_name = $name . '_pretty_date';
                        $this->setField($field, $new_name, $newFieldValue);
                    }

                    /** Date Parts */
                    if ($ccyymmdd === false) {
                    } else {
                        $this->setField($field, $name . '_ccyy', substr($ccyymmdd, 0, 4));
                        $this->setField($field, $name . '_mm', (int)substr($ccyymmdd, 5, 2));
                        $this->setField($field, $name . '_dd', (int)substr($ccyymmdd, 8, 2));

                        $this->setField(
                            $field,
                            $name . '_ccyy_mm_dd',
                            substr($ccyymmdd, 0, 4) . '-' . substr($ccyymmdd, 5, 2) . '-' . substr($ccyymmdd, 8, 2)
                        );

                        $newFieldValue = $this->date_controller->getMonthName((int)substr($ccyymmdd, 5, 2), true);
                        $this->setField($field, $name . '_month_name_abbr', $newFieldValue);

                        $newFieldValue = $this->date_controller->getMonthName((int)substr($ccyymmdd, 5, 2), false);
                        $this->setField($field, $name . '_month_name', $newFieldValue);

                        $dateObject = $this->date_controller->getDate($field_value);
                        $this->setField($field, $name . '_time', substr($dateObject, 11, 8));

                        $this->setField($field, $name . '_day_number', (int)substr($ccyymmdd, 5, 2));

                        $newFieldValue = $this->date_controller->getDayName((int)substr($ccyymmdd, 5, 2), true);
                        $this->setField($field, $name . '_day_name_abbr', $newFieldValue);

                        $newFieldValue = $this->date_controller->getDayName((int)substr($ccyymmdd, 7, 2), true);
                        $this->setField($field, $name . '_day_name_abbr', $newFieldValue);

                        $newFieldValue = $this->date_controller->getDayName((int)substr($ccyymmdd, 7, 2), false);
                        $this->setField($field, $name . '_day_name', $newFieldValue);
                    }
                }
            }
        }

        return $this;
    }
}
