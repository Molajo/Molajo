<?php
/**
 * @package    Niambie
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    MIT
 */
namespace Molajo\Plugin\Itemurl;

use Molajo\Service\Services;
use Molajo\Plugin\Plugin\Plugin;

defined('NIAMBIE') or die;

/**
 * Item Url
 *
 * @package     Niambie
 * @license     MIT
 * @since       1.0
 */
class ItemurlPlugin extends Plugin
{

    /**
     * After-read processing
     *
     * Provides the Url for any catalog_id field in the recordset
     *
     * @param   $this->row
     * @param   $model
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterRead()
    {

		if (Services::Registry()->get(CONFIGURATION_LITERAL, 'url_sef', 1) == 1) {

			if (isset($this->row->catalog_sef_request)) {
				$newFieldValue = Services::Url()->getApplicationURL($this->row->catalog_sef_request);
				$this->saveField(null, 'catalog_id_url', $newFieldValue);

			} elseif (isset($this->row->sef_request)) {
				$newFieldValue = Services::Url()->getApplicationURL($this->row->sef_request);
				$this->saveField(null, 'catalog_id_url', $newFieldValue);

			} elseif (isset($this->row->catalog_id)) {
				$newFieldValue = Services::Url()->getUrl($this->row->catalog_id);
				$this->saveField(null, 'catalog_id_url', $newFieldValue);
			}

		} else {

			if (isset($this->row->catalog_id)) {
				$newFieldValue = Services::Url()->getUrl($this->row->catalog_id);
				$this->saveField(null, 'catalog_id_url', $newFieldValue);
			}

		}

        $fields = $this->retrieveFieldsByType('url');

        if (is_array($fields) && count($fields) > 0) {

            foreach ($fields as $field) {

                if ($field->as_name == '') {
                    $name = $field['name'];
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

        return true;
    }
}
