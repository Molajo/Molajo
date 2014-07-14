<?php
/**
 * Readmore Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Readmore;

use CommonApi\Event\ReadInterface;
use Molajo\Plugins\ReadEventPlugin;

/**
 * Read More Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
class ReadmorePlugin extends ReadEventPlugin implements ReadInterface
{
    /**
     * After-read processing
     *
     * splits the content_text field into intro and full text on readmore
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterRead()
    {
        $fields = $this->getFieldsByType('text');

        $this->processFieldsByType($fields, 'processReadMore');

        return $this;
    }

    /**
     * Changes line breaks to break tags
     *
     * @param   object $field
     *
     * @return  object
     * @since   1.0.0
     */
    public function processReadMore($field)
    {
        $field_value = $this->getFieldValue($field);

        list($introductory_text, $fulltext) = $this->splitReadMoreText($field_value);

        $introductory_name = $field->name . '_' . 'introductory';
        $this->setField($field, $introductory_name, $introductory_text);

        $fulltext_name = $field->name . '_' . 'fulltext';
        $this->setField($field, $fulltext_name, $fulltext);

        $field['value'] = trim($fulltext);

        return $field;
    }

    /**
     * splitReadMoreText - search for the system-readmore break and split the text at that point into two text fields
     *
     * @param  $text
     *
     * @return string
     * @since   1.0.0
     */
    public function splitReadMoreText($text)
    {
        $pattern = '#{readmore}#';

        $tagPos = preg_match($pattern, $text);

        $introductory_text = '';
        $fulltext          = '';

        if ($tagPos === 0) {
            $introductory_text = $text;
        } else {
            list($introductory_text, $fulltext) = preg_split($pattern, $text, 2);
        }

        return (array($introductory_text, $fulltext));
    }
}
