<?php
/**
 * Snippet Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Snippet;

use CommonApi\Event\ReadInterface;
use Molajo\Plugins\ReadEventPlugin;

/**
 * Snippet Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
class SnippetPlugin extends ReadEventPlugin implements ReadInterface
{
    /**
     * Provides Text for Status ID
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterRead()
    {
        if ($this->countGetFields('text') === false) {
            return $this;
        }

        return $this->processFieldsByType($this->hold_fields, 'processSnippet');
    }

    /**
     * Process Smilies
     *
     * @param   object $field
     *
     * @return  object
     * @since   1.0.0
     */
    public function processSnippet($field)
    {
        $field['value'] = $this->setSnippetValue($field['value']);
        $field['name']  = $field['name'] . '_' . 'snippet';

        return $field;
    }

    /**
     * Process Snippet
     *
     * @param   object  $field_value
     *
     * @return  null|string
     * @since   1.0.0
     */
    public function setSnippetValue($field_value)
    {
        if ($field_value === null) {
            return null;
        }

        $snippetLength = 200;

        $newFieldValue = substr(strip_tags($field_value), 0, $snippetLength);

        if (trim($newFieldValue) === trim(strip_tags($field_value))) {
        } else {
            $newFieldValue .= '...';
        }

        return $newFieldValue;
    }
}
