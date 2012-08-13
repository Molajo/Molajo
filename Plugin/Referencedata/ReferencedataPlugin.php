<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Referencedata;

use Molajo\Plugin\Content\ContentPlugin;

defined('MOLAJO') or die;

/**
 * Reference Data Fields
 *
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class ReferencedataPlugin extends ContentPlugin
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

		if (count($fkFields) == 0 || $fkFields == false || $fkFields == null) {
			return false;
		}

		if (is_array($fkFields) && count($fkFields) > 0) {

			foreach ($fkFields as $fk) {

				$fkName = $fk->name;
				$name = substr($fkName, 3, strlen($fkName) - 3);

				$field = $this->getField($name);
				if ($field == false) {
					$fieldValue = false;
				} else {
					$fieldValue = $this->getFieldValue($field);
				}

				if ($fieldValue == false) {
				} else {

					$new_name = $field->name . '_value';

					$controllerClass = 'Molajo\\Controller\\Controller';
					$m = new $controllerClass();
					$m->connect('Table', $fk->source_model);

					$m->set('get_customfields', '0');
					$m->set('get_item_children', '0');
					$m->set('use_special_joins', '0');
					$m->set('check_view_level_access', '0');

					$m->set($m->get('primary_key', 'id'), (int)$fieldValue);

					$value = $m->getData('result');

					if ($value == false) {
					} else {
						$this->saveForeignKeyValue($new_name, $value);
					}
				}
			}
		}

		return true;
	}
}
