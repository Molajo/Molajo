<?php
/**
 * Text Interface
 *
 * @package    Controller
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace CommonApi\Controller;

/**
 * Text Interface
 *
 * @package     CommonApi
 * @subpackage  Utilities
 * @since       0.1
 */
interface TextInterface
{
    /**
     * Generates Lorem Ipsum Placeholder Text
     *
     * Usage:
     * $text->getPlaceHolderText(2, 3, 7, 'p', true);
     *  Generates 2 paragraphs, each with 3 lines of 7 random words each, each paragraph starting with 'Lorem ipsum'
     *
     * $text->getPlaceHolderText(1, 1, 3, 'h1', false);
     *  Generates 1 <h1> line using 3 random words
     *
     * $text->getPlaceHolderText(1, 10, 3, 'li', false);
     *  Generates 1 <ul> list with 10 items each with 3 random words
     *
     * @param   int    $number_of_paragraphs
     * @param   int    $lines_per_paragraphs
     * @param   int    $words_per_line
     * @param   string $markup_type ('p', 'h1', 'h2', 'h3', 'h4', 'h5', 'ul', 'ol', 'blockquote')
     * @param   bool   $start_with_lorem_ipsum
     *
     * @return  string
     * @since   1.0.0
     */
    public function getPlaceHolderText(
        $number_of_paragraphs = 3,
        $lines_per_paragraphs = 3,
        $words_per_line = 7,
        $markup_type = 'p',
        $start_with_lorem_ipsum = true
    );
}
