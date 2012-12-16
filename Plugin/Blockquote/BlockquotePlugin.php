<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    MIT, see License folder
 */
namespace Molajo\Plugin\Blockquote;

use Molajo\Plugin\Plugin\Plugin;

defined('MOLAJO') or die;

/**
 * Blockquote
 *
 * @package     Molajo
 * @license     MIT
 * @since       1.0
 */
class BlockquotePlugin extends Plugin
{
    /**
     * Blockquote extraction and formatting
     *
     * {blockquote}{cite:xYZ}*.*{/blockquote}
     *
     * @return  boolean
     * @since   1.0
     */
    public function onAfterRead()
    {
        $fields = $this->retrieveFieldsByType('text');

        if (is_array($fields) && count($fields) > 0) {

            foreach ($fields as $field) {

                /** retrieve each text field */
                $name = $field['name'];
                $fieldValue = $this->getFieldValue($field);

                if ($fieldValue === false) {
                } else {

                    $results = $this->blockquote($fieldValue);

                    if ($results === false || $results == '') {
                    } else {
                        /** Replace existing text */
                        $fieldValue = $results;
                        $this->saveField($field, $name, $fieldValue);
                    }
                }
            }
        }

        return true;
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
        $withThis = array();

        if (count($matches) == 0) {

        } else {

            $i = 0;
            foreach ($matches[1] as $match) {

                $replaceThis[] = $matches[0][$i];

                $blockquote = strip_tags($match);

                if (trim($blockquote) == '') {
                } else {
                    $cite = '';
                    if (substr($blockquote, 0, 6) == '{cite:') {
                        $blockquote = substr($blockquote, 6, strlen($blockquote) - 6);
                        $cite = substr($blockquote, 0, strpos($blockquote, '}'));
                        $blockquote = substr($blockquote, strlen($cite) + 1, 9999);
                        $cite = '<cite>' . $cite . '</cite>';
                    }
                    $withThis[] = '<blockquote>' . $blockquote . $cite . '</blockquote>';
                }
                $i++;
            }
        }

        $text = str_replace($replaceThis, $withThis, $text);

        return $text;
    }
}
