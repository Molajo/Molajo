<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Trigger\Dateformats;

use Molajo\Extension\Trigger\Content\ContentTrigger;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Date Formats
 *
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class DateformatsTrigger extends ContentTrigger
{
    /**
     * Static instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

    /**
     * getInstance
     *
     * @static
     * @return bool|object
     * @since  1.0
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new DateformatsTrigger();
        }

        return self::$instance;
    }

    /**
     * After-read processing
     *
     * Adds formatted dates to 'normal' or special fields recordset
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterRead()
    {
        $fields = $this->retrieveFieldsByType('date');

        if (is_array($fields) && count($fields) > 0) {

			/** @noinspection PhpWrongForeachArgumentTypeInspection */
			/** @noinspection PhpWrongForeachArgumentTypeInspection */
			foreach ($fields as $field) {

                $name = $field->name;

                /** Retrieves the actual field value from the 'normal' or special field */
                $fieldValue = $this->getFieldValue($field);

                if ($fieldValue == false
                    || $fieldValue == '0000-00-00 00:00:00') {
                } else {

                    /** formats the date for CCYYMMDD */
                    $newFieldValue = Services::Date()->convertCCYYMMDD($fieldValue);

                    if ($newFieldValue == false) {
                    } else {

                        /** Creates the new 'normal' or special field and populates the value */
                        $new_name = $name . '_ccyymmdd';
                        $newFieldValue = str_replace('-', '', $newFieldValue);
                        $fieldValue = $this->saveField($field, $new_name, $newFieldValue);
                    }

                    /** NN days ago */
                    $newFieldValue = Services::Date()->differenceDays(date('Y-m-d'), $fieldValue);

                    if ($newFieldValue == false) {
                    } else {

                        /** Creates the new 'normal' or special field and populates the value */
                        $new_name = $name . '_n_days_ago';
                        $fieldValue = $this->saveField($field, $new_name, $newFieldValue);
                    }

                    /** Pretty Date */
                    $newFieldValue = Services::Date()->prettydate($fieldValue);

                    if ($newFieldValue == false) {
                    } else {

                        /** Creates the new 'normal' or special field and populates the value */
                        $new_name = $name . '_pretty_date';
                        $fieldValue = $this->saveField($field, $new_name, $newFieldValue);
                    }
                }
            }
        }

        return true;
    }

    /**
     * itemDateRoutine
     *
     * Creates formatted date fields based on a named field
     *
     * @param $field
     * @param $this->query_results
     *
     * @return array
     * @since 1.0
     */
    protected function itemDateRoutine($field)
    {
        return false;
    }
}
