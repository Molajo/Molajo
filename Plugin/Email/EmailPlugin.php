<?php
/**
 * @package    Niambie
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    MIT
 */
namespace Molajo\Plugin\Email;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;

defined('NIAMBIE') or die;

/**
 * Email
 *
 * @package     Niambie
 * @license     MIT
 * @since       1.0
 */
class EmailPlugin extends Plugin
{
    /**
     * After-read processing
     *
     * Retrieves Author Information for Item
     *
     * @return  boolean
     * @since   1.0
     */
    public function onAfterRead()
    {
        $fields = $this->retrieveFieldsByType('email');

        if (is_array($fields) && count($fields) > 0) {

            foreach ($fields as $field) {

                $name = $field['name'];
                $new_name = $name . '_' . 'obfuscated';

                /** Retrieves the actual field value from the 'normal' or special field */
                $fieldValue = $this->getFieldValue($field);

                if ($fieldValue === false) {
                } else {

                    $newFieldValue = Services::Url()->obfuscateEmail($fieldValue);

                    if ($newFieldValue === false) {
                    } else {

                        if (strtolower($this->get('model_query_object', '', 'parameters')) == QUERY_OBJECT_ITEM) {
                        } else {
                            return true;
                        }
                        /** Creates the new 'normal' or special field and populates the value */
                        $this->saveField(null, $new_name, $newFieldValue);
                    }
                }
            }
        }

        return true;
    }

    /**
     * Pre-update processing
     *
     * @param   $this->row
     * @param   $model
     *
     * @return boolean
     * @since   1.0
     */
    public function onBeforeUpdate()
    {
        return false;
    }
}
