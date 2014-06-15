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
     * @since   1.0
     */
    public function onAfterRead()
    {
        $fields = $this->getFieldsByType('text');

        if (count($fields) > 0) {

            foreach ($fields as $field) {

                $name = $field['name'];

                $fieldValue = $this->getFieldValue($field);

                if ($fieldValue === false) {
                } else {

                    $newFields = $this->splitReadMoreText($fieldValue);

                    if ($newFields === false) {
                    } else {
                        $introductory_name = $name . '_' . 'introductory';
                        $this->setField(null, $introductory_name, $newFields[0]);

                        $fulltext_name = $name . '_' . 'fulltext';
                        $this->setField(null, $fulltext_name, $newFields[1]);

                        $content_text = trim($newFields[0]) . ' ' . trim($newFields[1]);
                        $this->setField($name, $name, $content_text);
                    }
                }
            }
        }

        return $this;
    }

    /**
     * splitReadMoreText - search for the system-readmore break and split the text at that point into two text fields
     *
     * @param  $text
     *
     * @return array
     * @since   1.0
     */
    public function splitReadMoreText($text)
    {
        $pattern = '#{readmore}#';

        $tagPos = preg_match($pattern, $text);

        $introductory_text = '';
        $fulltext          = '';

        if ($tagPos == 0) {
            $introductory_text = $text;
        } else {
            list($introductory_text, $fulltext) = preg_split($pattern, $text, 2);
        }

        return (array($introductory_text, $fulltext));
    }
}
