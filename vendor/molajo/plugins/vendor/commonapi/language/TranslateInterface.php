<?php
/**
 * Translate Interface
 *
 * @package    Language
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace CommonApi\Language;

/**
 * Translate Interface
 *
 * @package    Translate
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
interface TranslateInterface
{
    /**
     * Translate String
     *
     * @param   $string
     *
     * @return  string
     * @since   1.0.0
     */
    public function translateString($string);
}
