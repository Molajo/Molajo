<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Extension\Plugin\Linebreaks;

use Molajo\Extension\Plugin\Content\ContentPlugin;

defined('MOLAJO') or die;

/**
 * Linebreaks
 *
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class LinebreaksPlugin extends ContentPlugin
{

    /**
     * Changes line breaks to break tags
     *
     * @param   $this->data
     * @param   $model
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterRead()
    {
        $fields = $this->retrieveFieldsByType('text');

        if (is_array($fields) && count($fields) > 0) {

            foreach ($fields as $field) {

                $name = $field->name;

                $fieldValue = $this->getFieldValue($field);

                if ($fieldValue == false) {
                } else {

                    $newField = nl2br($fieldValue);

                    if ($newField == false) {
                    } else {

                        $this->saveField($field, $field, $newField);
                    }
                }
            }
        }

        return true;
    }
}
