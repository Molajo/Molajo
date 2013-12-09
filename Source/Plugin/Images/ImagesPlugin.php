<?php
/**
 * Images Plugin
 *
 * @package    Molajo
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugin\Images;

use CommonApi\Event\ReadInterface;
use Molajo\Plugin\ReadEventPlugin;

/**
 * Images Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
class ImagesPlugin extends ReadEventPlugin implements ReadInterface
{
    /**
     * After-read processing
     *
     * Adds formatted dates to 'normal' or special fields recordset
     *
     * @return  $this
     * @since   1.0
     */
    public function onAfterRead()
    {
        $fields = $this->getFieldsByType('image');

        if (is_array($fields) && count($fields) > 0) {

            foreach ($fields as $field) {

                $name = $field['name'];

                $fieldValue = $this->getFieldValue($field);

                if ($fieldValue === false) {
                } else {

                    $newFieldValue = $this->date_controller->convertCCYYMMDD($fieldValue);

                    if ($newFieldValue === false) {
                        $ccyymmdd = false;
                    } else {

                        $ccyymmdd = $newFieldValue;
                        $new_name = $name . '_ccyymmdd';
                        $this->setField(null, $new_name, $newFieldValue);
                        $fieldValue = $newFieldValue;
                    }
                }
            }
        }

        return $this;
    }
}
