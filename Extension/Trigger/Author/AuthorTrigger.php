<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Extension\Trigger\Author;

use Molajo\Extension\Trigger\Content\ContentTrigger;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Author
 *
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class AuthorTrigger extends ContentTrigger
{

    /**
     * After-read processing
     *
     * Retrieves Author Information for Item
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterRead()
    {
        /** Retrieve created_by field definition */
        $field = $this->getField('created_by');
        if ($field == false) {
            return true;
        }

        /** Retrieve the current value for created by field */
        $fieldValue = $this->getFieldValue($field);

        if ((int) $fieldValue == 0) {
            return true;
        }

        /** Author information already available */
        if (Services::Registry()->exists('Triggerdata', 'Author' . $fieldValue)) {

            $item = Services::Registry()->get('Triggerdata', 'Author' . $fieldValue);

            foreach ($item[0] as $key => $value) {
                $new_field_name = $key;
                $this->saveField(null, $new_field_name, $value);
            }

            return true;
        }

        /** Using the created_by value, retrieve the Author Profile Data */
        $controllerClass = 'Molajo\\Controller\\Controller';
        $m = new $controllerClass();
        $results = $m->connect('Table', 'Users');
        if ($results == false) {
            return false;
        }

        $m->set('id', (int) $fieldValue);
        $m->set('get_item_children', 0);

        $item = $m->getData('item');

        if ($item == false || count($item) == 0) {
            return false;
        }

        $authorArray = array();
        $row = new \stdClass();

        /** Save each field */
        foreach (get_object_vars($item) as $key => $value) {

            if (substr($key, 0, strlen('item_')) == 'item_'
                || substr($key, 0, strlen('form_')) == 'form_'
                || substr($key, 0, strlen('list_')) == 'list_'
                || substr($key, 0, strlen('password')) == 'password'
            ) {

            } else {

                $new_field_name = 'author' . '_' . $key;
                $this->saveField(null, $new_field_name, $value);

                $row->$new_field_name = $value;
            }

            $authorArray[] = $row;
        }

        /** Save Trigger Data */
        Services::Registry()->set('Triggerdata', 'Author' . $fieldValue, $authorArray);
        Services::Registry()->set('Triggerdata', 'Author', $authorArray);

        return true;
    }
}
