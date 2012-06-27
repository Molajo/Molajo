<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Trigger\Snippet;

use Molajo\Extension\Trigger\Content\ContentTrigger;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Snippet
 *
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class SnippetTrigger extends ContentTrigger
{

	/**
	 * After-read processing
	 *
	 * Parses the Content Text into a snippet, stripped of HTML tags
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

					$newField = Services::Text()->snippet($fieldValue);

					if ($newField == false) {
					} else {

						$newFieldName = $name . '_' . 'snippet';
						$fieldValue = $this->saveField($field, $newFieldName, $newField);
					}
				}
			}
		}

		return true;
	}
}
