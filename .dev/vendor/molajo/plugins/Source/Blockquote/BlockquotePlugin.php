<?php
/**
 * Blockquote Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Blockquote;

use CommonApi\Event\ReadInterface;
use Molajo\Plugins\ReadEventPlugin;

/**
 * Blockquote Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
class BlockquotePlugin extends ReadEventPlugin implements ReadInterface
{
    /**
     * Blockquote extraction and formatting
     *
     * {blockquote}{cite:xYZ}*.*{/blockquote}
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterRead()
    {
        $fields = $this->getFieldsByType('text');

        $this->processFieldsByType($fields, 'handleBlockquote');

        return $this;
    }

    /**
     * Handle Blockquote - searches for and returns blockquote
     *
     * @param   object $field
     *
     * @return  object
     * @since   1.0.0
     */
    protected function handleBlockquote($field)
    {
        $matches = $this->findBlockquoteMatches($field['value']);
        if (count($matches) === 0) {
            return $field;
        }

        $replace_this = array();
        $with_this    = array();

        $i = 0;
        foreach ($matches[$i] as $match) {
            $replace_this[] = $match[0];
            $with_this[]    = $this->handleBlockquoteMatch($match[1]);
            $i ++;
        }

        return $this->replaceBlockquoteValue($replace_this, $with_this, $field);
    }

    /**
     * Handle Blockquote - searches for and returns blockquote
     *
     *
     * @return  object
     * @since   1.0.0
     */
    protected function findBlockquoteMatches($text)
    {
        $pattern = '/{blockquote}(.*){\/blockquote}/';

        preg_match_all($pattern, $text, $matches);

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
     *
     * @param string $blockquote
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

    /**
     * Replace with final value
     *
     * @param   array  $replace_this
     * @param   array  $with_this
     * @param   object $field
     *
     * @return  object
     * @since   1.0.0
     */
    protected function replaceBlockquoteValue($replace_this, $with_this, $field)
    {
        $temp = str_replace($replace_this, $with_this, $field['value']);

        $field['value'] = $temp;

        return $field;
    }
}
