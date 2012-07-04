<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
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
	 * @param   $this->data
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

	/**
	 * Build the page url to be used in links
	 *
	 * page_url was set in Route and it contains any non-routable parameters that
	 * were used. Non-routable parameters include such values as /edit, /new, /tag/value, etc
	 *
	 * These values are used in conjunction with the permanent URL for basic operations on that data
	 */
	public function onAfterRoute()
	{

		echo Services::Registry()->get('Parameters', 'page_url');

		echo Services::Registry()->get('Parameters', 'catalog_url_request');

		echo Services::Registry()->get('Configuration', 'application_base_url');
		    die;
		echo $path;
		echo '<br />';
		echo $url;
		die;

		Services::Registry()->set('Parameters', 'page_url', $path);


echo '<pre>';
var_dump($this->parameters);
echo '</pre>';

		if (Services::Registry()->get('Configuration', 'url_sef') == 1) {
			$url .= '/' . $this->parameters['page_url'];
			$connector = '?';
		} else {
			$url .= '/' . $this->parameters['catalog_url_request'];
			$connector = '&';
		}

		Services::Registry()->set('Trigger', 'full_page_url', $url);
	}
}
