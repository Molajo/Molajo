<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Images;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Date Formats
 *
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class ImagesPlugin extends Plugin
{
    /**
     * Pre-create processing
     *
     * @return boolean
     * @since   1.0
     */
    public function onBeforeCreate()
    {
        return true;
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
        $fields = $this->retrieveFieldsByType('image');

        if (is_array($fields) && count($fields) > 0) {

            foreach ($fields as $field) {

                $name = $field->name;

                /** Retrieves the actual field value from the 'normal' or special field */
                $fieldValue = $this->getFieldValue($field);

                if ($fieldValue === false) {

                } else {

                    /** formats the date for CCYYMMDD */
                    $newFieldValue = Services::Date()->convertCCYYMMDD($fieldValue);

                    if ($newFieldValue === false) {
                        $ccyymmdd = false;
                    } else {

                        /** Creates the new 'normal' or special field and populates the value */
                        $ccyymmdd = $newFieldValue;
                        $new_name = $name . '_ccyymmdd';
                        $this->saveField(null, $new_name, $newFieldValue);
                        $fieldValue = $newFieldValue;
                    }
                }
            }
        }

        return true;
    }
}
