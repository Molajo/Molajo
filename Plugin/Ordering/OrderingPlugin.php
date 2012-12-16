<?php
/**
 * @package    Niambie
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    MIT, see License folder
 */
namespace Molajo\Plugin\Ordering;

use Molajo\Plugin\Plugin\Plugin;

defined('NIAMBIE') or die;

/**
 * Date Formats
 *
 * @package     Niambie
 * @license     MIT
 * @since       1.0
 */
class OrderingPlugin extends Plugin
{
    //@todo reorder on delete, too

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
            $controller->getModelRegistry(
                $this->get('model_type', '', 'parameters'),
                $this->get('model_name', '', 'parameters'),
                1
            );

            $primary_prefix = $controller->set('primary_prefix', 0, 'model_registry');

            $catalog_type_idField = $this->getField('catalog_type_id');
            $catalog_type_id = $this->getFieldValue($catalog_type_idField);

            $controller->model->query->select('max(' . $this->db->qn($primary_prefix) . '.' . $this->db->qn('ordering') . ')');
            $controller->model->query->where($this->db->qn($primary_prefix) . '.' . $this->db->qn('catalog_type_id')
                . ' = ' . (int) $catalog_type_id);

            $controller->set('use_special_joins', 0, 'model_registry');
            $controller->set('check_view_level_access', 0, 'model_registry');
            $controller->set('process_plugins', 0, 'model_registry');
            $controller->set('get_customfields', 0, 'model_registry');

            $ordering = $controller->getData(QUERY_OBJECT_RESULT);

            $newFieldValue = (int) $ordering + 1;

            $this->saveField($field, 'ordering', $newFieldValue);

        }

        return true;
    }
}
