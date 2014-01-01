<?php
/**
 * Reference Data Fields
 *
 * @package    Molajo
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugin\Referencedata;

use CommonApi\Event\ReadInterface;
use Molajo\Plugin\ReadEventPlugin;

/**
 * Reference Data Fields
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
class ReferencedataPlugin extends ReadEventPlugin implements ReadInterface
{
    /**
     * Retrieve textual values for primary keys
     *
     * @return  $this
     * @since   1.0
     */
    public function onAfterRead()
    {
        return;
        $fkFields = $this->getFieldsByType('foreignkeys');

        if (count($fkFields) == 0 || $fkFields === false || $fkFields == null) {
            return $this;
        }

        if (is_array($fkFields) && count($fkFields) > 0) {

            foreach ($fkFields as $fk) {

                $fkName = $fk->name;
                $name   = substr($fkName, 3, strlen($fkName) - 3);

                $field = $this->getField($name);
                if ($field === false) {
                    $fieldValue = false;
                } else {
                    $fieldValue = $this->getFieldValue($field);
                }

                if ($fieldValue === false) {
                } else {

                    $new_name = $field['name'] . '_value';

                    $controller_class_namespace = $this->controller_namespace;
                    $controller                 = new $controller_class_namespace();
                    $controller->getModelRegistry('datasource', $fk->source_model, 1);
                    $controller->set('get_customfields', 0);
                    $controller->set('get_item_children', 0);
                    $controller->set('use_special_joins', 0);
                    $controller->set('check_view_level_access', 0);

                    $controller->set(
                        $controller->get('primary_key', 'id', 0)
                        ,
                        (int)$fieldValue,
                        'model_registry'
                    );

                    $value = $controller->getData('result');

                    if ($value === false) {
                    } else {
                        // what is this?
                        $this->saveForeignKeyValue($new_name, $value);
                    }
                }
            }
        }

        return $this;
    }
}
