<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Extension\Trigger\Itemurl;

use Molajo\Application;
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

		$fields = $this->retrieveFieldsByType('url');

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

					if (substr($fieldValue, 0, 11) == '{sitemedia}') {
						$newFieldValue = SITE_MEDIA_FOLDER . '/' . substr($fieldValue, 11, strlen($fieldValue) - 11);
					} else {
						$newFieldValue = $fieldValue;
					}

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
    public function onBeforeParse()
    {
        $url = Application::Request()->get('base_url_path_for_application') .
            Application::Request()->get('requested_resource_for_route');

        Services::Registry()->set('Triggerdata', 'full_page_url', $url);

        return true;
    }
}
