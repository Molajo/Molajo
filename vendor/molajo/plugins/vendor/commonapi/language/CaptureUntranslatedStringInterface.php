<?php
/**
 * Capture Untranslated Strings Interface
 *
 * @package    Language
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace CommonApi\Language;

/**
 * Capture Untranslated Strings Interface
 *
 * @package    Language
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
interface CaptureUntranslatedStringInterface
{
    /**
     * Save untranslated strings for localization
     *
     * @param   string $string
     *
     * @return  bool
     * @since   1.0.0
     */
    public function setString($string);
}
