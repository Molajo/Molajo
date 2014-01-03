<?php
/**
 * Item Url
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugin\Itemurl;

use CommonApi\Event\ReadInterface;
use Molajo\Plugin\ReadEventPlugin;

/**
 * Item Url
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
class ItemurlPlugin extends ReadEventPlugin implements ReadInterface
{
    /**
     * Retrieves Url for catalog_id fields in the recordset
     *
     * @return  $this
     * @since   1.0
     */
    public function onAfterRead()
    {
        if ($this->runtime_data->application->parameters->url_sef == 1) {
        } else {
            return $this;
        }

        $fields = $this->getFieldsByType('url');


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

                        $this->setField(null, $newFieldName, $newFieldValue);
                    }
                }
            }
        }

        return $this;
    }
}
