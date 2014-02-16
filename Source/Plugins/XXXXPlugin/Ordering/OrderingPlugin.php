<?php
/**
 * Ordering Plugins
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Ordering;

use CommonApi\Event\CreateInterface;
use Molajo\Plugins\CreateEventPlugin;

/**
 * Ordering Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
class OrderingPlugin extends CreateEventPlugin implements CreateInterface
{
    //@todo reorder on delete, too

    /**
     * Pre-create processing
     *
     * @return  $this
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

        if ((int)$fieldValue > 0) {
            return $this;
        }

        $newFieldValue = '';

        if ($fieldValue === false
            || (int)$fieldValue == 0
        ) {

            $controller_class_namespace = $this->controller_namespace;
            $controller                 = new $controller_class_namespace();
            $controller->getModelRegistry(
                $this->get('model_type', '', 'runtime_data'),
                $this->get('model_name', '', 'runtime_data'),
                1
            );

            $primary_prefix = $controller->set('primary_prefix', 0);

            $catalog_type_idField = $this->getField('catalog_type_id');
            $catalog_type_id      = $this->getFieldValue($catalog_type_idField);

            $controller->model->query->select(
                'max(' . $this->db->qn($primary_prefix) . '.' . $this->db->qn('ordering') . ')'
            );
            $controller->model->query->where(
                $this->db->qn($primary_prefix) . '.' . $this->db->qn('catalog_type_id')
                . ' = ' . (int)$catalog_type_id
            );

            $controller->set('use_special_joins', 0);
            $controller->set('check_view_level_access', 0);
            $controller->set('process_events', 0);
            $controller->set('get_customfields', 0);

            $ordering = $controller->getData('result');

            $newFieldValue = (int)$ordering + 1;

            $this->setField($field, 'ordering', $newFieldValue);
        }

        return $this;
    }
}
