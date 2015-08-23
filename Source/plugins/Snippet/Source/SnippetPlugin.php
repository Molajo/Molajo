<?php
/**
 * Snippet Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Snippet;

use CommonApi\Event\ReadEventInterface;
use Molajo\Plugins\ReadEvent;

/**
 * Snippet Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
final class SnippetPlugin extends ReadEvent implements ReadEventInterface
{
    /**
     * Runs after read
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterReadRow()
    {
        if ($this->existFields('html') === false) {
            return $this;
        }

        return $this->processFieldsByType('processSnippet', $this->hold_fields);
    }

    /**
     * Process Snippet
     *
     * @param   array $field
     *
     * @return  array
     * @since   1.0.0
     */
    protected function processSnippet(array $field = array())
    {
        $value       = $this->getFieldValue($field);
        $after_value = $this->setSnippetValue($value);

        if ($value === $after_value) {
            return $field;
        }

        $field['value'] = $after_value;
        $field['name']  = $field['name'] . '_' . 'snippet';

        return $field;
    }

    /**
     * Set Snippet Value
     *
     * @param   string $field_value
     *
     * @return  string
     * @since   1.0.0
     */
    protected function setSnippetValue($field_value)
    {
        if ($field_value === null) {
            return null;
        }

        $snippetLength = 200;

        $new_field_value = substr(strip_tags($field_value), 0, $snippetLength);

        if (trim($new_field_value) === trim(strip_tags($field_value))) {
        } else {
            $new_field_value .= '...';
        }

        return $new_field_value;
    }
}
