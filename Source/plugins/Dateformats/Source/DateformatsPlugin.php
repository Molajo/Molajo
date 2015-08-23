<?php
/**
 * Dateformats Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Dateformats;

use CommonApi\Event\ReadEventInterface;
use Molajo\Plugins\ReadEvent;

/**
 * Dateformats Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
final class DateformatsPlugin extends ReadEvent implements ReadEventInterface
{
    /**
     * Time
     *
     * @var    string
     * @since  1.0.0
     */
    protected $time_value;

    /**
     * Formatted Time
     *
     * @var    string
     * @since  1.0.0
     */
    protected $formatted_time_value;

    /**
     * Executes after reading row
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterReadRow()
    {
        if ($this->checkProcessPlugin() === false) {
            return $this;
        }

        $this->processDateformatsFields();

        ksort($this->controller['model_registry']['fields']);

        return $this;
    }

    /**
     * Should plugin be executed?
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function checkProcessPlugin()
    {
        if ($this->existFields('datetime') === false) {
            return false;
        }

        return true;
    }

    /**
     * Process Date Format Fields
     *
     * @return  object
     * @since   1.0.0
     */
    protected function processDateformatsFields()
    {
        if (is_array($this->hold_fields) && count($this->hold_fields) > 0) {
        } else {
            return $this;
        }

        foreach ($this->hold_fields as $field) {
            if (isset($this->controller['row']->$field['name'])) {
                $field['value'] = $this->controller['row']->$field['name'];
                $this->processDateFormatsField($field);
            }
        }

        return $this;
    }

    /**
     * Format date
     *
     * @param   array $field
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function processDateFormatsField($field)
    {
        $this->setTime($field);
        $this->setDateccyymmdd($field);
        $this->setDatePrettyDate($field);

        return $this;
    }

    /**
     * Format date: ccyymmdd
     *
     * @param   array $field
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setTime($field)
    {
        $new_field         = $field;
        $new_field['name'] = $new_field['name'] . '_' . 'time';
        if ($new_field['locked'] === 1) {
        } else {
            $new_field['locked'] = 0;
        }
        $new_field['calculated'] = 1;

        $this->time_value = substr($new_field['value'], 11, 8);

        if ($this->time_value === '00:00:00') {
            $new_field['value'] = '';
            $new_field['type']  = 'string';
            $this->time_value   = '';

        } else {
            $new_field['value'] = $this->time_value;
            $new_field['type']  = 'time';
            $hh                 = substr($this->time_value, 0, 2);
            $mm                 = substr($this->time_value, 3, 2);

            if ($hh > 12) {
                $hh    = $hh - 12;
                $am_fm = 'PM';
            } else {
                $am_fm = 'AM';
            }

            $new_field['value'] = $hh . ':' . $mm . ' ' . $am_fm;
        }

        $this->setField($new_field['name'], $new_field['value'], $new_field);

        $this->formatted_time_value = $new_field['value'];

        return $this;
    }

    /**
     * Format date: ccyymmdd
     *
     * @param   array $field
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setDateccyymmdd($field)
    {
        $new_field         = $field;
        $new_field['name'] = $new_field['name'] . '_' . 'ccyymmdd';

        $ccyymmdd = $this->date->convertCCYYMMDD($field['value']);

        if ($ccyymmdd === '00000000') {
            $new_field['value']      = '';
            $dash                    = '';
            $slash                   = '';
            $ccyy                    = '';
            $mm                      = '';
            $month_name_abbreviation = '';
            $month_name              = '';
            $dd                      = '';
            $day_name_abbreviation   = '';
            $day_name                = '';
            $day_number              = '';
            $n_days_ago              = '';
        } else {
            $new_field['value']      = $ccyymmdd;
            $dash                    = $this->date->convertCCYYdashMMdashDD($field['value']);
            $slash                   = $this->date->convertMMslashDDslashCCYY($field['value']);
            $ccyy                    = substr($ccyymmdd, 0, 4);
            $mm                      = substr($ccyymmdd, 4, 2);
            $month_name_abbreviation = $this->date->getMonthName((int)substr($ccyymmdd, 4, 2), true);
            $month_name              = $this->date->getMonthName((int)substr($ccyymmdd, 4, 2), false);
            $dd                      = substr($ccyymmdd, 6, 2);
            $day_name_abbreviation   = $this->date->getDayName($ccyymmdd, true);
            $day_name                = $this->date->getDayName($ccyymmdd, false);
            $day_number              = $this->date->getDayNumber($ccyymmdd);
            $n_days_ago              = $this->date->getNumberofDaysAgo($ccyymmdd);
        }

        $this->setField($new_field['name'], $new_field['value'], $new_field);

        $this->setDateParts($field, 'ccyy_mm_dd', $dash . ' ' . $this->time_value, 'datetime', false);
        $this->setDateParts($field, 'mm_dd_ccyy', $slash . ' ' . $this->formatted_time_value, 'datetime', false);
        $this->setDateParts($field, 'local', $dash . 'T' . $this->time_value, 'datetime', false);
        $this->setDateParts($field, 'ccyy', $ccyy, 'string', true);
        $this->setDateParts($field, 'mm', $mm, 'string', true);
        $this->setDateParts($field, 'month_name_abbreviation', $month_name_abbreviation, 'string', true);
        $this->setDateParts($field, 'month_name', $month_name, 'string', true);
        $this->setDateParts($field, 'dd', $dd, 'string', true);
        $this->setDateParts($field, 'day_name_abbreviation', $day_name_abbreviation, 'string', true);
        $this->setDateParts($field, 'day_name', $day_name, 'string', true);
        $this->setDateParts($field, 'day_number', $day_number, 'string', true);
        $this->setDateParts($field, 'n_days_ago', $n_days_ago, 'string', true);

        return $this;
    }

    /**
     * Set Date Parts
     *
     * @param   array   $field
     * @param   string  $literal
     * @param   string  $value
     * @param   string  $type
     * @param   boolean $locked
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setDateParts($field, $literal, $value, $type, $locked)
    {
        $new_field = $field;

        if (trim($value) === 'T') {
            $value = ' ';
        }

        $new_field['name']       = $new_field['name'] . '_' . $literal;
        $new_field['value']      = $value;
        $new_field['calculated'] = 1;
        $new_field['type']       = $type;

        $this->setLocked($new_field, $locked);

        $this->setField($new_field['name'], $new_field['value'], $new_field);

        return $this;
    }

    /**
     * Set Locked
     *
     * @param   array   $new_field
     * @param   boolean $locked
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setLocked($new_field, $locked)
    {
        if (isset($new_field['locked']) && (int)$new_field['locked'] === 1) {
        } else {
            if ($locked === true) {
                $new_field['locked'] = 1;
            } else {
                $new_field['locked'] = 0;
            }
        }

        return $this;
    }

    /**
     * Format date: Pretty Date
     *
     * @param   array $field
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setDatePrettyDate($field)
    {
        $new_field         = $field;
        $new_field['name'] = $new_field['name'] . '_' . 'pretty_date';

        if ($field['value'] === '0000-00-00 00:00:00') {
            $new_field['value'] = '';
        } else {
            $new_field['value'] = $this->date->getPrettyDate($field['value']);
        }

        $this->setField($new_field['name'], $new_field['value'], $new_field);

        return $this;
    }
}
