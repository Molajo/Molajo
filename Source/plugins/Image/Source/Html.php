<?php
/**
 * Replace Image Tokens in HTML fields
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Image;

/**
 * Replace Image Tokens in HTML fields
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
abstract class Html extends Key
{
    /**
     * Search pattern
     *
     * @var    array
     * @since  1.0.0
     */
    protected $pattern = '/{image}(.*){\/image}/';

    /**
     * Format Html Field
     *
     * @param   array $field
     *
     * @return  array
     * @since   1.0.0
     */
    protected function processHtmlField(array $field = array())
    {
        $field['value'] = $this->getFieldValue($field);

        $matches = $this->findImageMatches($field['value']);

        if (count($matches) === 0) {
            return $field;
        }

        $i = 0;
        foreach ($matches[0] as $match) {
            $replace_this   = $match;
            $with_this      = $this->createReplaceWith($matches[1][$i]);
            $field['value'] = str_replace($replace_this, $with_this, $field['value']);

            $i++;
        }

        return $field;
    }

    /**
     * Image Search
     *
     * @param   string $text
     *
     * @return  array
     * @since   1.0.0
     */
    protected function findImageMatches($text)
    {
        preg_match_all($this->pattern, $text, $matches);

        return $matches;
    }

    /**
     * Create Replace With
     *
     * @param   string $match
     *
     * @return  string
     * @since   1.0.0
     */
    protected function createReplaceWith($match)
    {
        $number       = substr($match, strlen('id='), 2);
        $rest         = substr($match, strlen('id=' . trim($number)), 9999);
        $replace_with = '{I image '
            . 'image_id=' . md5($this->controller['row']->customfields->{'image' . trim($number)})
            . ' ' . trim($rest)
            . ' I}';

        return $replace_with;
    }
}
