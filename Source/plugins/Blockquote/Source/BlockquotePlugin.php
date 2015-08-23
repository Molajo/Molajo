<?php
/**
 * Blockquote Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Blockquote;

use CommonApi\Event\ReadEventInterface;
use Molajo\Plugins\ReadEvent;

/**
 * Blockquote Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
final class BlockquotePlugin extends ReadEvent implements ReadEventInterface
{
    /**
     * Search pattern
     *
     * @var    array
     * @since  1.0.0
     */
    protected $pattern = '/{blockquote}(.*){\/blockquote}/';

    /**
     * Executes after reading row
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterReadRow()
    {
        if ($this->checkProcessPlugin() === false) {
            return $this;
        }

        $this->processFieldsByType('processBlockquoteField', $this->hold_fields);

        return $this;
    }

    /**
     * Process Plugin Determination
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function checkProcessPlugin()
    {
        if ($this->checkEditModel() === true) {
            return false;
        }

        if ($this->existFields('html') === false) {
            return false;
        }

        return true;
    }

    /**
     * Format Blockquote Field
     *
     * @param   array $field
     *
     * @return  array
     * @since   1.0.0
     */
    protected function processBlockquoteField(array $field = array())
    {
        $value = $this->getFieldValue($field);

        $matches = $this->findBlockquoteMatches($value);
        if (count($matches[0]) === 0) {
            return $field;
        }

        $replace_this = array();
        $with_this    = array();

        $i = 0;
        foreach ($matches[0] as $match) {
            $replace_this[] = $match;
            $with_this[]    = $this->handleBlockquoteMatch($matches[1][$i]);
            $i++;
        }

        $after_value = str_replace($replace_this, $with_this, $value);

        if ($value === $after_value) {
            return $field;
        }

        $field['value'] = $after_value;

        return $field;
    }

    /**
     * Handle Blockquote - searches for and returns blockquote
     *
     * @param   string $text
     *
     * @return  array
     * @since   1.0.0
     */
    protected function findBlockquoteMatches($text)
    {
        preg_match_all($this->pattern, $text, $matches);

        return $matches;
    }

    /**
     * blockquote - searches for and returns blockquote
     *
     * @param   string $match
     *
     * @return  string
     * @since   1.0.0
     */
    protected function handleBlockquoteMatch($match)
    {
        $blockquote = strip_tags($match);

        if (trim($blockquote) === '') {
            return '';
        }

        if (substr($blockquote, 0, 6) === '{cite:') {
            $blockquote = $this->handleBlockquoteCite($blockquote);
        }

        return '<blockquote>' . $blockquote . '</blockquote>';
    }

    /**
     * Handle Cite
     *
     * @param   string $blockquote
     *
     * @return  string
     * @since   1.0.0
     */
    protected function handleBlockquoteCite($blockquote)
    {
        $blockquote = substr($blockquote, 6, strlen($blockquote) - 6);
        $cite       = substr($blockquote, 0, strpos($blockquote, '}'));
        $blockquote = substr($blockquote, strlen($cite) + 1, 9999);
        $cite       = '<cite>' . $cite . '</cite>';

        return $blockquote . $cite;
    }
}
