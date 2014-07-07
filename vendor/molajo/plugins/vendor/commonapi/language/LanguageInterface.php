<?php
/**
 * Language Interface
 *
 * @package    Language
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace CommonApi\Language;

/**
 * Language Interface
 *
 * @package    Language
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
interface LanguageInterface
{
    /**
     * Get Language Properties
     *
     * Specify null for key to have all language properties for current language returned as object
     *
     * @param   null|string $key
     *
     * @return  mixed
     * @since   1.0.0
     */
    public function get($key = null);
}
