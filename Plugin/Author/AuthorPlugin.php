<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
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
     * todo: move to it's own include
     * Retrieves Author Information for Item
     *
     * @return  boolean
     * @since   1.0
     */
    public function onAfterRead()
    {
        $field = $this->getField('created_by');
        if ($field === false) {
            return true;
        }

        $fieldValue = $this->getFieldValue($field);
        if ((int) $fieldValue == 0) {
            return true;
        }

        if (Services::Registry()->exists(TEMPLATE_LITERAL, $this->get('template_view_path_node') . $fieldValue)) {

            $authorArray = Services::Registry()->get(
                TEMPLATE_LITERAL,
                $this->get('template_view_path_node') . $fieldValue
            );

            foreach ($authorArray[0] as $key => $value) {
                $new_field_name = $key;
                $this->saveField(null, $new_field_name, $value);
            }

            Services::Registry()->set(TEMPLATE_LITERAL, $this->get('template_view_path_node'), $authorArray);

            return true;
        }

        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();
        $controller->getModelRegistry(SYSTEM_LITERAL, 'Users');
        $controller->setDataobject();

        $controller->set('id', (int) $fieldValue);
        $controller->set('get_item_children', 0);

        $item = $controller->getData(QUERY_OBJECT_ITEM);

        if ($item === false || count($item) == 0) {
            return false;
        }

        $authorArray = array();
        $row = new \stdClass();

        foreach (get_object_vars($item) as $key => $value) {

            if (substr($key, 0, strlen('item_')) == 'item_'
                || substr($key, 0, strlen('form_')) == 'form_'
                || substr($key, 0, strlen('list_')) == 'list_'
                || substr($key, 0, strlen('password')) == 'password'
            ) {

            } else {

                $new_field_name = $this->get('template_view_path_node') . '_' . $key;

                $this->saveField(null, $new_field_name, $value);

                $row->$new_field_name = $value;
            }
        }

        $authorArray[] = $row;

        Services::Registry()->set(TEMPLATE_LITERAL, $this->get('template_view_path_node') . $fieldValue, $authorArray);
        Services::Registry()->set(TEMPLATE_LITERAL, $this->get('template_view_path_node'), $authorArray);

        return true;
    }
}
