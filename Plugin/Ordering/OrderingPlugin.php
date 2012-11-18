<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Ordering;

use Molajo\Plugin\Plugin\Plugin;

defined('MOLAJO') or die;

/**
 * Date Formats
 *
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class OrderingPlugin extends Plugin
{
    //todo reorder on delete, too

    /**
     * Pre-create processing
     *
     * @return boolean
     * @since   1.0
     */
    public function onBeforeCreate()
    {

        $field = $this->getField('ordering');

        if ($field === false) {
            $fieldValue = false;
        } else {
            $fieldValue = $this->getFieldValue($field);
        }

        if ((int) $fieldValue > 0) {
            return true;
        }

        $newFieldValue = '';

        if ($fieldValue === false
            || (int) $fieldValue == 0
        ) {

            $controllerClass = CONTROLLER_CLASS;
            $controller = new $controllerClass();
            $results = $controller->getModelRegistry($this->get('model_type'), $this->get('model_name'));
            if ($results === false) {
                return false;
            }

            $results = $controller->setDataobject();
            if ($results === false) {
                return false;
            }
            $primary_prefix = $this->get('primary_prefix');

            $catalog_type_idField = $this->getField('catalog_type_id');
            $catalog_type_id = $this->getFieldValue($catalog_type_idField);

            $controller->model->query->select('max(' . $this->db->qn($primary_prefix) . '.' . $this->db->qn('ordering') . ')');
            $controller->model->query->where($this->db->qn($primary_prefix) . '.' . $this->db->qn('catalog_type_id')
                . ' = ' . (int) $catalog_type_id);

            $controller->set('use_special_joins', 0);
            $controller->set('check_view_level_access', 0);
            $controller->set('process_plugins', 0);
            $controller->set('get_customfields', 0);

            $ordering = $controller->getData('result');

            $newFieldValue = (int) $ordering + 1;

            $this->saveField($field, 'ordering', $newFieldValue);

        }

        return true;
    }
}
