<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Itemurl;

use Molajo\Service\Services;
use Molajo\Plugin\Content\ContentPlugin;

defined('MOLAJO') or die;

/**
 * Item Url
 *
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class ItemurlPlugin extends ContentPlugin
{

	/**
	 * After-read processing
	 *
	 * Provides the Url for any catalog_id field in the recordset
	 *
	 * @param   $this->data
	 * @param   $model
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onAfterRead()
	{
		if (defined('ROUTE')) {
		} else {
			return true;
		}

		if (isset($this->data->catalog_id)) {
			$newFieldValue = Services::Url()->getUrl($this->data->catalog_id);
			$this->saveField(null, 'catalog_id_url', $newFieldValue);
		}

		$fields = $this->retrieveFieldsByType('url');

		if (is_array($fields) && count($fields) > 0) {

			foreach ($fields as $field) {

				if ($field->as_name == '') {
					$name = $field->name;
				} else {
					$name = $field->as_name;
				}

				$fieldValue = $this->getFieldValue($field);

				if ($fieldValue === false) {
				} else {

					if (substr($fieldValue, 0, 11) == '{sitemedia}') {
						$newFieldValue = SITE_MEDIA_FOLDER . '/' . substr($fieldValue, 11, strlen($fieldValue) - 11);
					} else {
						$newFieldValue = $fieldValue;
					}

					if ($newFieldValue === false) {
					} else {

						/** Creates the new 'normal' or special field and populates the value */
						$newFieldName = $name . '_' . 'url';

						$this->saveField(null, $newFieldName, $newFieldValue);
					}
				}
			}
		}

		$status = $this->data->status;

		if ($status === null) {
			return true;
		}

		if ($status == '2') {
			$status_name = Services::Language()->translate('Archived');
		} elseif ($status == '1') {
			$status_name = Services::Language()->translate('Published');
		} elseif ($status == '0') {
			$status_name = Services::Language()->translate('Unpublished');
		} elseif ($status == '-1') {
			$status_name = Services::Language()->translate('Trashed');
		} elseif ($status == '-2') {
			$status_name = Services::Language()->translate('Spammed');
		} elseif ($status == '-5') {
			$status_name = Services::Language()->translate('Draft');
		} elseif ($status == '-10') {
			$status_name = Services::Language()->translate('Version');
		} else {
			return true;
		}

		$this->saveField(null, 'status_name', $status_name);

		return true;
	}
}
