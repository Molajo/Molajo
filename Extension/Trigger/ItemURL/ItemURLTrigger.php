<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Trigger\Itemurl;

use Molajo\Service\Services;
use Molajo\Extension\Trigger\Content\ContentTrigger;

defined('MOLAJO') or die;

/**
 * Item Url
 *
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class ItemurlTrigger extends ContentTrigger
{

	/**
	 * After-read processing
	 *
	 * Provides the Url for any catalog_id field in the recordset
	 *
	 * @param   $this->query_results
	 * @param   $model
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onAfterRead()
	{
		$fields = $this->retrieveFieldsByType('catalog_id');

		if (is_array($fields) && count($fields) > 0) {

			foreach ($fields as $field) {

				if ($field->as_name == '') {
					$name = $field->name;
				} else {
					$name = $field->as_name;
				}

				$fieldValue = $this->getFieldValue($field);

				if ($fieldValue == false) {
				} else {

					$newFieldValue = Services::Url()->getUrl($fieldValue);

					if ($newFieldValue == false) {
					} else {

						/** Creates the new 'normal' or special field and populates the value */
						$newFieldName = $name . '_' . 'url';

						$fieldValue = $this->saveField($field, $newFieldName, $newFieldValue);
					}
				}
			}
		}

		return true;
	}
}
