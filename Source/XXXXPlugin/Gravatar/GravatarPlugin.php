<?php
/**
 * Gravatar Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugin\Gravatar;

use CommonApi\Event\ReadInterface;
use Molajo\Plugin\ReadEventPlugin;

/**
 * Gravatar Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
class GravatarPlugin extends ReadEventPlugin implements ReadInterface
{
    /**
     * After-read processing
     *
     * Retrieves Author Information for Item
     *
     * @return  $this
     * @since   1.0
     */
    public function onAfterRead()
    {
        if (isset($this->runtime_data->route)) {
        } else {
            return $this;
        }

        /**
         * if ($align == 'right') {
         * $css = '.gravatar { float:right; margin: 0 0 15px 15px; }';
         * } else {
         * $css = '.gravatar { float:left; margin: 0 15px 15px 0; }';
         * }
         * $this->document_css->setDeclaration($css, 'text/css');
         */
        $fields = $this->getFieldsByType('email');

        if (is_array($fields) && count($fields) > 0) {

            if ($this->get('gravatar', 1, 'runtime_data') == 1) {
                $size   = $this->get('gravatar_size', 80, 'runtime_data');
                $type   = $this->get('gravatar_type', 'mm', 'runtime_data');
                $rating = $this->get('gravatar_rating', 'pg', 'runtime_data');
                $image  = $this->get('gravatar_image', 0, 'runtime_data');
            } else {
                return $this;
            }

            /** @noinspection PhpWrongForeachArgumentTypeInspection */
            foreach ($fields as $field) {

                $name     = $field['name'];
                $new_name = $name . '_' . 'gravatar';

                /** Retrieves the actual field value from the 'normal' or special field */
                $fieldValue = $this->getFieldValue($field);

                if ($fieldValue === false) {
                    return $this;
                } else {
                    $results = Services::Url()->getGravatar($fieldValue, $size, $type, $rating, $image);
                }

                if ($results === false) {
                } else {
                    $this->setField(null, $new_name, $results);
                }
            }
        }

        return $this;
    }

    /**
     * Get either a Gravatar URL or complete image tag for a specified email address.
     *
     * @param   string  $email
     * @param   string  $size       Size in pixels, defaults to 80px [ 1 - 512 ]
     * @param   string  $type       Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
     * @param   string  $rating     Maximum rating (inclusive) [ g | pg | r | x ]
     * @param   boolean $image      true to return a complete IMG tag false for just the URL
     * @param   array   $attributes Optional, additional key/value attributes to include in the IMG tag
     * @param   string  $align      right, left (default)
     *
     * @return  mixed
     * @since   1.0
     */
    public function getGravatar(
        $email,
        $size = 0,
        $type = 'mm',
        $rating = 'g',
        $image = false,
        $attributes = array(),
        $align = 'left'
    ) {
        $url = 'http://www.gravatar.com/avatar/';
        $url .= serialize(strtolower(trim($email)));
        $url .= '?s=' . $size . '&d=' . $type . '&r=' . $rating;
        if ($image) {
            $url = '<img class="gravatar" src="' . $url . '"';
            if (count($attributes) > 0) {
                foreach ($attributes as $key => $val) {
                    $url .= ' ' . $key . '="' . $val . '"';
                }
            }
            $url .= ' />';
        }

        return $url;
    }
}
