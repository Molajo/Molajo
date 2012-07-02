<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see license folder
 */
namespace Molajo\Extension\Trigger\Linebreaks;

use Molajo\Extension\Trigger\Content\ContentTrigger;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Linebreaks
 *
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class LinebreaksTrigger extends ContentTrigger
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
