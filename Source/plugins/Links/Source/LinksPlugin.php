<?php
/**
 * Links Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Links;

use CommonApi\Event\ReadEventInterface;
use Molajo\Plugins\ReadEvent;

/**
 * Links Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
final class LinksPlugin extends ReadEvent implements ReadEventInterface
{
    /**
     * Search pattern
     *
     * @var    array
     * @since  1.0.0
     */
    protected $pattern = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";

    /**
     * Creates Linebreaks in Text Fields
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterReadRow()
    {
        if (strtolower($this->controller['parameters']->token->name) === 'comments') {
        } else {
            return $this;
        }

        if ($this->existFields('html') === false) {
            return $this;
        }

        $this->processFieldsByType('setLinks', $this->hold_fields);

        return $this;
    }

    /**
     * Set Links in HTML Field
     *
     * @param   array $field
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setLinks(array $field = array())
    {
        $value       = $this->getFieldValue($field);
        $after_value = $value;

        preg_match($this->pattern, $value, $matches);

        if (count($matches) === 0) {
            return $field;
        }

        foreach ($matches as $replace_this) {
            if (in_array($replace_this, array('http', 'https'))) {
            } elseif (strpos($value, '<a href="' . $replace_this)) {
            } else {
                $with_this   = '<a href="' . $replace_this . '">' . $replace_this . '</a>';
                $after_value = str_replace($replace_this, $with_this, $after_value);
            }
        }

        if ($value === $after_value) {
            return $field;
        }

        $field['value'] = $after_value;

        return $field;
    }
}
