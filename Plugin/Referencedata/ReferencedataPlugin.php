<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Referencedata;

use Molajo\Plugin\Plugin\Plugin;

defined('MOLAJO') or die;

/**
 * Reference Data Fields
 *
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class ReferencedataPlugin extends Plugin
{
    /**
     * Retrieve textual values for primary keys
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterRead()
    {

        $fkFields = $this->retrieveFieldsByType('foreignkeys');

        if (count($fkFields) == 0 || $fkFields === false || $fkFields == null) {
            return false;
        }

        if (is_array($fkFields) && count($fkFields) > 0) {

            foreach ($fkFields as $fk) {

                $fkName = $fk->name;
                $name = substr($fkName, 3, strlen($fkName) - 3);

                $field = $this->getField($name);
                if ($field === false) {
                    $fieldValue = false;
                } else {
                    $fieldValue = $this->getFieldValue($field);
                }

                if ($fieldValue === false) {
                } else {

                    $new_name = $field->name . '_value';

                    $controllerClass = CONTROLLER_CLASS;
                    $controller = new $controllerClass();
                    $controller->getModelRegistry(DATASOURCE_LITERAL, $fk->source_model);

                    $results = $controller->setDataobject();
                    if ($results === false) {
                        return false;
                    }

                    $controller->set('get_customfields', '0');
                    $controller->set('get_item_children', '0');
                    $controller->set('use_special_joins', '0');
                    $controller->set('check_view_level_access', '0');

                    $controller->set($controller->get('primary_key', 'id'), (int) $fieldValue);

                    $value = $controller->getData(QUERY_OBJECT_RESULT);

                    if ($value === false) {
                    } else {
                        $this->saveForeignKeyValue($new_name, $value);
                    }
                }
            }
        }

        return true;
    }
}
