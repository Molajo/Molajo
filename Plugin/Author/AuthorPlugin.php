<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Author;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Author
 *
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class AuthorPlugin extends Plugin
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
        if ($field === false) {
            return true;
        }

        /** Retrieve the current value for created by field */
        $fieldValue = $this->getFieldValue($field);

        if ((int) $fieldValue == 0) {
            return true;
        }

        /** Author information already available */
        if (Services::Registry()->exists('Plugindata', 'Author' . $fieldValue)) {

            $item = Services::Registry()->get('Plugindata', 'Author' . $fieldValue);

            foreach ($item[0] as $key => $value) {
                $new_field_name = $key;
                $this->saveField(null, $new_field_name, $value);
            }

            return true;
        }

        /** Using the created_by value, retrieve the Author Profile Data */
        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();

        $results = $controller->getModelRegistry('System', 'Users');
        if ($results === false) {
            return false;
        }

        $results = $controller->setDataobject();
        if ($results === false) {
            return false;
        }

        $controller->set('id', (int) $fieldValue);
        $controller->set('get_item_children', 0);

        $item = $controller->getData(QUERY_OBJECT_ITEM);

        if ($item === false || count($item) == 0) {
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

        /** Save Plugin Data */
        Services::Registry()->set('Plugindata', 'Author' . $fieldValue, $authorArray);
        Services::Registry()->set('Plugindata', 'Author', $authorArray);

        return true;
    }
}
