<?php
/**
 * @package    Molajo
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    MIT
 */
namespace Molajo\Plugin\Linebreaks;

use Molajo\Plugin\Plugin\Plugin;

defined('MOLAJO') or die;

/**
 * Linebreaks
 *
 * @package     Molajo
 * @license     MIT
 * @since       1.0
 */
class LinebreaksPlugin extends Plugin
{

    /**
     * Changes line breaks to break tags
     *
     * @return  boolean
     * @since   1.0
     */
    public function onAfterRead()
    {
        $fields = $this->retrieveFieldsByType('text');

        if (is_array($fields) && count($fields) > 0) {

            foreach ($fields as $field) {

                $name = $field['name'];

                $fieldValue = $this->getFieldValue($field);

                if ($fieldValue === false) {
                } else {

                    $newField = nl2br($fieldValue);

                    if ($newField === false) {
                    } else {

                        $this->saveField($field, $field, $newField);
                    }
                }
            }
        }

        return true;
    }
}
