<?php
/**
 * Readmore Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Readmore;

use CommonApi\Event\ReadEventInterface;
use Molajo\Plugins\ReadEvent;

/**
 * Readmore Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
final class ReadmorePlugin extends ReadEvent implements ReadEventInterface
{
    /**
     * Search pattern
     *
     * @var    array
     * @since  1.0.0
     */
    protected $pattern = '#{readmore}#';

    /**
     * Executes after reading row
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterReadRow()
    {
        if ($this->checkEditModel() === true) {
            return false;
        }

        if ($this->existFields('html') === false) {
            return $this;
        }

        $this->processFieldsByType('processHtmlFields', $this->hold_fields);

        return $this;
    }

    /**
     * Search for the system-readmore break and split the text at that point into two text fields
     *
     * @param   array $field
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function processHtmlFields(array $field = array())
    {
        $value = $this->getFieldValue($field);

        $tag_position = preg_match($this->pattern, $value);

        if ($tag_position === 0) {
            $this->setReadMoreTextNone($value, $field);
        } else {
            $this->setReadMoreTextSplit($value, $field);
        }

        return $field;
    }

    /**
     * Search for the system-readmore break and split the text at that point into two text fields
     *
     * @param   string $value
     * @param   array  $field
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setReadMoreTextNone($value, array $field = array())
    {
        $introductory_text = $value;
        $full_text         = $value;

        $this->saveReadMoreFields($introductory_text, $full_text, $field);

        return $this;
    }

    /**
     * Search for the system-readmore break and split the text at that point into two text fields
     *
     * @param   string $value
     * @param   array  $field
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setReadMoreTextSplit($value, array $field = array())
    {
        list($introductory_text, $full_text) = preg_split($this->pattern, $value, 2);

        $this->saveReadMoreFields($introductory_text, $full_text, $field);

        return $this;
    }

    /**
     * Search for the system-readmore break and split the text at that point into two text fields
     *
     * @param   string $introductory_text
     * @param   string $full_text
     * @param   array  $field
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function saveReadMoreFields($introductory_text, $full_text, array $field = array())
    {
        $this->setField($field['name'], trim($introductory_text) . trim($full_text), $field);

        $newfield          = $field;
        $newfield['name']  = $newfield['name'] . '_' . 'introductory';
        $newfield['value'] = trim($introductory_text);

        $this->setField($newfield['name'], $introductory_text, $newfield);

        $newfield          = $field;
        $newfield['name']  = $newfield['name'] . '_' . 'fulltext';
        $newfield['value'] = trim($full_text);

        $this->setField($newfield['name'], $full_text, $newfield);

        return $this;
    }
}
