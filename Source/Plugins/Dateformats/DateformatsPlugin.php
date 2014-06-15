<?php
/**
 * Date Formats
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Dateformats;

use Exception;
use Molajo\Plugins\CreateEventPlugin;
use CommonApi\Event\CreateInterface;

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
     * @since   1.0
     */
    public function onBeforeCreate()
    {
        if (is_object($this->date_controller)) {
        } else {
            return $this;
        }

        $fields = $this->getFieldsByType('datetime');

        if (count($fields) > 0) {

            foreach ($fields as $field) {

                $name = $field['name'];

                $fieldValue = $this->getFieldValue($field);

                if ($name == 'modified_datetime') {

                    $this->setField($field, $name, $this->model->now);

                    $modifiedByField = $this->getField('modified_by');
                    $modifiedByValue = $this->getFieldValue($modifiedByField);
                    if ($modifiedByValue === false) {
                        $this->setField(
                            $modifiedByField,
                            'modified_by',
                            $this->runtime_data->user->get('id')
                        );
                    }
                } elseif ($fieldValue === false
                    || $fieldValue == '0000-00-00 00:00:00'
                ) {

                    $this->setField($field, $name, $this->model->now);

                    if ($name == 'created_datetime') {
                        $createdByField = $this->getField('created_by');
                        $createdByValue = $this->getFieldValue($createdByField);
                        if ($createdByValue === false) {
                            $this->setField(
                                $createdByField,
                                'created_by',
                                $this->runtime_data->user->get('id')
                            );
                        }
                    } elseif ($name == 'activity_datetime') {
                        $createdByField = $this->getField('user_id');
                        $createdByValue = $this->getFieldValue($createdByField);
                        if ($createdByValue === false) {
                            $this->setField($createdByField, 'user_id', $this->runtime_data->user->get('id'));
                        }
                    }
                }
            }
        }

        return $this;
    }

    /**
     * After-read processing
     *
     * Adds formatted dates to 'normal' or special fields recordset
     *
     * @return  $this
     * @since   1.0
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
                $fieldValue = $this->getFieldValue($field);

                if ($fieldValue === false
                    || $fieldValue == '0000-00-00 00:00:00'
                    || $fieldValue == ''
                    || $fieldValue == null
                    || substr($name, 0, 12) == 'list_select_'
                ) {
                } else {

                    $newFieldValue = $this->date_controller->convertCCYYMMDD($fieldValue);

                    if ($newFieldValue === false) {
                        $ccyymmdd = false;
                    } else {

                        /** Creates the new 'normal' or special field and populates the value */
                        $ccyymmdd = $newFieldValue;
                        $new_name = $name . '_ccyymmdd';
                        $this->setField($field, $new_name, $newFieldValue);
                        $fieldValue = $newFieldValue;
                    }

                    /** Using newly formatted date, calculate NN days ago */
                    $newFieldValue = $this->date_controller->getNumberofDaysAgo($fieldValue);

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
                    $newFieldValue = $this->date_controller->getPrettyDate($fieldValue);

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

                        $dateObject = $this->date_controller->getDate($fieldValue);
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
