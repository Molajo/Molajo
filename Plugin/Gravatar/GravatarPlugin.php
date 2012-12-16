<?php
/**
 * @package    Niambie
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    MIT, see License folder
 */
namespace Molajo\Plugin\Gravatar;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;

defined('NIAMBIE') or die;

/**
 * Gravatar
 *
 * @package     Niambie
 * @license     MIT
 * @since       1.0
 */
class GravatarPlugin extends Plugin
{

    /**
     * After-read processing
     *
     * Retrieves Author Information for Item
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

        $fields = $this->retrieveFieldsByType('email');

        if (is_array($fields) && count($fields) > 0) {

            if ($this->get('gravatar', 1, 'parameters') == 1) {
                $size = $this->get('gravatar_size', 80, 'parameters');
                $type = $this->get('gravatar_type', 'mm', 'parameters');
                $rating = $this->get('gravatar_rating', 'pg', 'parameters');
                $image = $this->get('gravatar_image', 0, 'parameters');

            } else {
                return true;
            }

            /** @noinspection PhpWrongForeachArgumentTypeInspection */
            foreach ($fields as $field) {

                $name = $field['name'];
                $new_name = $name . '_' . 'gravatar';

                /** Retrieves the actual field value from the 'normal' or special field */
                $fieldValue = $this->getFieldValue($field);

                if ($fieldValue === false) {
                    return true;
                } else {
                    $results = Services::Url()->getGravatar($fieldValue, $size, $type, $rating, $image);
                }

                if ($results === false) {
                } else {
                    $this->saveField(null, $new_name, $results);
                }
            }
        }

        return true;
    }
}
