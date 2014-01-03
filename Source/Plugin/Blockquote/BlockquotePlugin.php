<?php
/**
 * Blockquote Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugin\Blockquote;

use CommonApi\Event\ReadInterface;
use Molajo\Plugin\ReadEventPlugin;

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
     * @since   1.0
     */
    public function onAfterRead()
    {
        $fields = $this->getFieldsByType('text');

        if (is_array($fields) && count($fields) > 0) {

            foreach ($fields as $field) {
                $name = $field['name'];

                $fieldValue = $this->getFieldValue($field);

                if ($fieldValue === null) {
                } else {

                    $results = $this->blockquote($fieldValue);

                    if ($results === null) {
                    } else {
                        $this->setField($field, $name, $results);
                    }
                }
            }
        }

        return $this;
    }

    /**
     * blockquote - searches for and returns blockquote
     *
     * @param  $text
     *
     * @return array
     * @since   1.0
     */
    protected function blockquote($text)
    {
        $pattern = '/{blockquote}(.*){\/blockquote}/';

        preg_match_all($pattern, $text, $matches);

        $replaceThis = array();
        $withThis    = array();

        if (count($matches) == 0) {
            return null;
        }

        $i = 0;
        foreach ($matches[1] as $match) {

            $replaceThis[] = $matches[0][$i];

            $blockquote = strip_tags($match);

            if (trim($blockquote) == '') {

            } else {
                $cite = '';
                if (substr($blockquote, 0, 6) == '{cite:') {
                    $blockquote = substr($blockquote, 6, strlen($blockquote) - 6);
                    $cite       = substr($blockquote, 0, strpos($blockquote, '}'));
                    $blockquote = substr($blockquote, strlen($cite) + 1, 9999);
                    $cite       = '<cite>' . $cite . '</cite>';
                }
                $withThis[] = '<blockquote>' . $blockquote . $cite . '</blockquote>';
            }
            $i ++;
        }

        $text = str_replace($replaceThis, $withThis, $text);

        return $text;
    }
}
